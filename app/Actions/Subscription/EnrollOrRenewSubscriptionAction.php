<?php

namespace App\Actions\Subscription;

use App\DTOs\Subscription\EnrollSubscriptionData;
use App\Models\Donation;
use App\Models\DonationSubscription;

class EnrollOrRenewSubscriptionAction
{
    /**
     * Find the donor's active recurring plan (by email) and renew it, or
     * create a new one if this is their first successful gift. Either way,
     * the plan's amount/donor fields are refreshed from the latest payment
     * and next_reminder_at is pushed a month out. Also links the source
     * donation back to the plan.
     */
    public function execute(EnrollSubscriptionData $data): DonationSubscription
    {
        $subscription = DonationSubscription::active()->where('donor_email', $data->donorEmail)->first();

        $attributes = [
            'donor_name' => $data->donorName,
            'donor_email' => $data->donorEmail,
            'donor_phone' => $data->donorPhone,
            'amount' => $data->amount,
            'last_donation_id' => $data->sourceDonationId,
            'next_reminder_at' => now()->addMonthNoOverflow(),
        ];

        if ($subscription) {
            $subscription->update($attributes);
        } else {
            $subscription = DonationSubscription::create($attributes + [
                'currency' => 'IDR',
                'status' => DonationSubscription::STATUS_ACTIVE,
            ]);
        }

        Donation::whereKey($data->sourceDonationId)->update(['subscription_id' => $subscription->id]);

        return $subscription;
    }
}
