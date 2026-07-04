<?php

use App\Actions\Donation\SendDonationRemindersAction;
use App\Actions\Subscription\SendSubscriptionRenewalRemindersAction;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Manually send any due donation reminders. Kept for the pre-DOKU one-off
// reminder flow (superseded going forward by the recurring subscription
// loop below), but no longer scheduled — see the note further down.
Artisan::command('donations:send-reminders', function (SendDonationRemindersAction $action) {
    $count = $action->execute();
    $this->info("Sent {$count} donation reminder(s).");
})->purpose('Email a "give again" reminder to donors whose support period has ended');

// Manually send any due monthly renewal reminders (also runs daily via the scheduler).
Artisan::command('subscriptions:send-renewal-reminders', function (SendSubscriptionRenewalRemindersAction $action) {
    $count = $action->execute();
    $this->info("Sent {$count} subscription renewal reminder(s).");
})->purpose('Email active recurring donors a "renew your gift" reminder with a signed review link');

// -----------------------------------------------------------------------------
// SCHEDULER: RESET VIEW COUNTERS
// -----------------------------------------------------------------------------

// 1. Reset Daily Views (Setiap Hari jam 00:00)
Schedule::call(function () {
    // Update query langsung ke database (lebih cepat daripada Eloquent loop)
    DB::table('news')->update(['daily_views' => 0]);

    // Catat log agar kita tahu scheduler berjalan
    Log::info('SCHEDULER: Daily views berhasil di-reset ke 0.');
})->daily(); // Default jam 00:00

// 2. Reset Monthly Views (Setiap Tanggal 1 jam 00:00)
Schedule::call(function () {
    DB::table('news')->update(['monthly_views' => 0]);

    Log::info('SCHEDULER: Monthly views berhasil di-reset ke 0.');
})->monthly(); // Default tanggal 1 jam 00:00

// 3. (Opsional) Hapus cache view jika menggunakan cache driver
// Schedule::command('cache:clear')->daily();

// -----------------------------------------------------------------------------
// SCHEDULER: RECURRING SUBSCRIPTION RENEWAL REMINDERS
// -----------------------------------------------------------------------------

// Every DOKU donation now enrolls the donor in an indefinite monthly loop
// (see EnrollOrRenewSubscriptionAction), so this replaces the one-off
// "SendDonationRemindersAction" schedule above — that Action/mail/column
// are left in place (and still manually triggerable) but intentionally no
// longer scheduled, so donors don't get two competing "give again" emails.
Schedule::call(function () {
    $count = app(SendSubscriptionRenewalRemindersAction::class)->execute();

    Log::info("SCHEDULER: Sent {$count} subscription renewal reminder(s).");
})->daily();
