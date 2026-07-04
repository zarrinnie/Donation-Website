<?php

namespace App\Actions\Subscription;

use App\Mail\SubscriptionRenewalMail;
use App\Models\DonationSubscription;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class SendSubscriptionRenewalRemindersAction
{
    /** How long the emailed review link stays valid before the next reminder would supersede it. */
    private const REVIEW_LINK_EXPIRY_DAYS = 14;

    /**
     * Email every active subscription whose next_reminder_at has passed —
     * done in PHP (not SQL) so behaviour matches MySQL and the SQLite test
     * database, same rationale as SendDonationRemindersAction. Each email
     * carries a signed link to the payment review page; next_reminder_at is
     * always re-pushed a month out from send time (self-scheduling, so it
     * never drifts regardless of exact scheduler run time).
     *
     * @return int Number of reminders sent.
     */
    public function execute(): int
    {
        $due = DonationSubscription::active()
            ->whereNotNull('next_reminder_at')
            ->get()
            ->filter(fn (DonationSubscription $s) => $s->next_reminder_at->isPast());

        foreach ($due as $subscription) {
            $reviewUrl = URL::temporarySignedRoute(
                'donate.subscription.review',
                now()->addDays(self::REVIEW_LINK_EXPIRY_DAYS),
                ['subscription' => $subscription->id],
            );

            Mail::to($subscription->donor_email)->send(new SubscriptionRenewalMail($subscription, $reviewUrl));

            $subscription->update([
                'last_reminder_sent_at' => now(),
                'next_reminder_at' => now()->addMonthNoOverflow(),
            ]);
        }

        return $due->count();
    }
}
