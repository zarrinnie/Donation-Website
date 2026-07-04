<?php

namespace App\Actions\Subscription;

use App\Models\DonationSubscription;

class CancelSubscriptionAction
{
    /**
     * Stop future renewal reminders for this recurring plan. No donor-facing
     * trigger yet (admin-only for now) — built ahead of that so adding one
     * later is a small follow-up, not a redesign.
     */
    public function execute(DonationSubscription $subscription): DonationSubscription
    {
        $subscription->update(['status' => DonationSubscription::STATUS_CANCELLED]);

        return $subscription;
    }
}
