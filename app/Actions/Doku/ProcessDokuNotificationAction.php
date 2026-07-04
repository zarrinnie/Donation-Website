<?php

namespace App\Actions\Doku;

use App\Actions\Donation\NotifyAdminsOfDonationAction;
use App\Actions\Donation\SendDonationStatusEmailAction;
use App\Actions\Donation\UpdateDonationStatusAction;
use App\Actions\Subscription\EnrollOrRenewSubscriptionAction;
use App\DTOs\Doku\WebhookNotificationData;
use App\DTOs\Subscription\EnrollSubscriptionData;
use App\Models\Donation;
use Illuminate\Support\Facades\Log;

class ProcessDokuNotificationAction
{
    public function __construct(
        private readonly UpdateDonationStatusAction $updateStatus,
        private readonly EnrollOrRenewSubscriptionAction $enrollOrRenew,
        private readonly SendDonationStatusEmailAction $sendStatusEmail,
        private readonly NotifyAdminsOfDonationAction $notifyAdmins,
    ) {}

    /**
     * Fan-out for a verified DOKU webhook notification. Called only after
     * VerifyDokuWebhookSignatureAction has confirmed the request is
     * authentic. Safe to call more than once for the same notification
     * (DOKU may retry) — a repeat that doesn't change the mapped status is
     * a no-op.
     */
    public function execute(WebhookNotificationData $data): void
    {
        $donation = Donation::where('doku_invoice_number', $data->invoiceNumber)->first();

        if (! $donation) {
            Log::warning('doku.webhook.unknown_invoice', ['invoice_number' => $data->invoiceNumber]);

            return;
        }

        if ($donation->status === $data->transactionStatus) {
            Log::info('doku.webhook.duplicate_notification_ignored', ['donation_id' => $donation->id]);

            return;
        }

        $donation->update([
            'doku_webhook_payload' => $data->rawPayload,
            'paid_at' => $data->transactionStatus === Donation::STATUS_SUCCESSFUL ? ($data->paidAt ?? now()) : $donation->paid_at,
        ]);

        $donation = $this->updateStatus->execute($donation, $data->transactionStatus, Donation::STATUS_SOURCE_WEBHOOK);

        if ($donation->status === Donation::STATUS_SUCCESSFUL) {
            $this->enrollOrRenew->execute(new EnrollSubscriptionData(
                donorName: $donation->donor_name,
                donorEmail: $donation->donor_email,
                donorPhone: $donation->donor_phone,
                amount: (float) $donation->amount,
                sourceDonationId: $donation->id,
            ));

            $this->notifyAdmins->execute($donation);
        }

        $this->sendStatusEmail->execute($donation);
    }
}
