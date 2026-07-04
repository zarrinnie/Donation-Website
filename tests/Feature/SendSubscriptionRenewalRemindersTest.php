<?php

use App\Actions\Subscription\SendSubscriptionRenewalRemindersAction;
use App\Mail\SubscriptionRenewalMail;
use App\Models\DonationSubscription;
use Illuminate\Support\Facades\Mail;

it('reminds an active subscription whose next_reminder_at has passed', function () {
    Mail::fake();

    $subscription = DonationSubscription::factory()->dueForReminder()->create();

    $sent = app(SendSubscriptionRenewalRemindersAction::class)->execute();

    expect($sent)->toBe(1);

    $subscription->refresh();
    expect($subscription->last_reminder_sent_at)->not->toBeNull()
        ->and($subscription->next_reminder_at->isFuture())->toBeTrue();

    Mail::assertSent(SubscriptionRenewalMail::class, fn (SubscriptionRenewalMail $mail) => $mail->subscription->is($subscription));
});

it('skips subscriptions that are not yet due and cancelled subscriptions', function () {
    Mail::fake();

    // Not due yet.
    DonationSubscription::factory()->create(['next_reminder_at' => now()->addWeek()]);

    // Due, but cancelled.
    DonationSubscription::factory()->cancelled()->dueForReminder()->create();

    expect(app(SendSubscriptionRenewalRemindersAction::class)->execute())->toBe(0);

    Mail::assertNothingSent();
});

it('includes a valid signed review link in the reminder email', function () {
    Mail::fake();

    DonationSubscription::factory()->dueForReminder()->create();

    app(SendSubscriptionRenewalRemindersAction::class)->execute();

    Mail::assertSent(SubscriptionRenewalMail::class, function (SubscriptionRenewalMail $mail) {
        return str_contains($mail->reviewUrl, 'signature=');
    });
});
