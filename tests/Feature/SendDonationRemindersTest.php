<?php

use App\Actions\Donation\SendDonationRemindersAction;
use App\Mail\DonationReminderMail;
use App\Models\Donation;
use Illuminate\Support\Facades\Mail;

it('reminds a successful donor once their support period has ended', function () {
    Mail::fake();

    $donation = Donation::factory()->status('successful')->create([
        'time_range_days' => 30,
        'created_at' => now()->subDays(31),
        'reminder_sent_at' => null,
    ]);

    $sent = app(SendDonationRemindersAction::class)->execute();

    expect($sent)->toBe(1)
        ->and($donation->fresh()->reminder_sent_at)->not->toBeNull();

    Mail::assertSent(DonationReminderMail::class, fn (DonationReminderMail $mail) => $mail->donation->is($donation));
});

it('never sends a reminder twice for the same donation', function () {
    Mail::fake();

    Donation::factory()->status('successful')->create([
        'time_range_days' => 30,
        'created_at' => now()->subDays(31),
        'reminder_sent_at' => now()->subDay(),
    ]);

    expect(app(SendDonationRemindersAction::class)->execute())->toBe(0);

    Mail::assertNothingSent();
});

it('skips donations whose period has not ended and non-successful donations', function () {
    Mail::fake();

    // Period not yet over.
    Donation::factory()->status('successful')->create([
        'time_range_days' => 30,
        'created_at' => now()->subDays(5),
        'reminder_sent_at' => null,
    ]);

    // Period over, but the donation was never successful.
    Donation::factory()->status('pending')->create([
        'time_range_days' => 30,
        'created_at' => now()->subDays(31),
        'reminder_sent_at' => null,
    ]);

    expect(app(SendDonationRemindersAction::class)->execute())->toBe(0);

    Mail::assertNothingSent();
});
