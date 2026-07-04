<?php

namespace App\DTOs\Doku;

use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class WebhookNotificationData
{
    /**
     * Built only after VerifyDokuWebhookSignatureAction has confirmed the
     * request is authentic. $transactionStatus is already mapped onto our
     * own vocabulary (Donation::STATUS_*), not DOKU's raw status enum.
     *
     * @param  array<string, mixed>  $rawPayload
     */
    public function __construct(
        public string $invoiceNumber,
        public string $transactionStatus,
        public int $amountPaid,
        public ?string $paidAt,
        public array $rawPayload,
    ) {}

    /**
     * NOTE: the payload shape assumed here (order.invoice_number,
     * transaction.status, order.amount, transaction.date) follows DOKU's
     * documented Checkout notification format. Verify against DOKU's
     * current sandbox docs before going live.
     */
    public static function fromRequest(Request $request): self
    {
        $payload = (array) $request->json()->all();

        return new self(
            invoiceNumber: (string) Arr::get($payload, 'order.invoice_number', ''),
            transactionStatus: self::mapStatus((string) Arr::get($payload, 'transaction.status', '')),
            amountPaid: (int) Arr::get($payload, 'order.amount', 0),
            paidAt: Arr::get($payload, 'transaction.date'),
            rawPayload: $payload,
        );
    }

    private static function mapStatus(string $rawStatus): string
    {
        return match (strtoupper($rawStatus)) {
            'SUCCESS' => Donation::STATUS_SUCCESSFUL,
            'FAILED', 'EXPIRED', 'CANCELLED' => Donation::STATUS_FAILED,
            default => Donation::STATUS_PENDING,
        };
    }
}
