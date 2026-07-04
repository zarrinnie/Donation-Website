<?php

use App\Actions\Donation\NotifyAdminsOfDonationAction;
use App\Actions\Donation\SendDonationStatusEmailAction;
use App\Livewire\Admin\Donation\Show as DonationShow;
use App\Livewire\Admin\Donor\Show as DonorShow;
use App\Models\Donation;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

it('notifies admin users when a donation is received', function () {
    Notification::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $superAdmin = User::factory()->create(['role' => 'super_admin']);
    $plainUser = User::factory()->create(['role' => 'user']);

    $donation = Donation::factory()->status('successful')->create();

    app(NotifyAdminsOfDonationAction::class)->execute($donation);

    Notification::assertSentTo([$admin, $superAdmin], GeneralNotification::class);
    Notification::assertNotSentTo($plainUser, GeneralNotification::class);
});

it('records status_email_sent_at when the donor is emailed', function () {
    Mail::fake();

    $donation = Donation::factory()->status('successful')->create(['status_email_sent_at' => null]);

    app(SendDonationStatusEmailAction::class)->execute($donation);

    expect($donation->fresh()->status_email_sent_at)->not->toBeNull();
});

it('clears the new-donation marker when an admin opens the detail page', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $donation = Donation::factory()->status('successful')->create(['admin_seen_at' => null]);

    expect($donation->isNewForAdmin())->toBeTrue();

    Livewire::actingAs($admin)->test(DonationShow::class, ['donation' => $donation]);

    expect($donation->fresh()->admin_seen_at)->not->toBeNull();
});

it('aggregates a donor\'s history, total, and support periods', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $email = 'repeat@example.com';

    Donation::factory()->status('successful')->create([
        'donor_email' => $email, 'amount' => 100000, 'time_range_label' => '1 Month',
    ]);
    Donation::factory()->status('successful')->create([
        'donor_email' => $email, 'amount' => 50000, 'time_range_label' => '1 Year',
    ]);
    Donation::factory()->status('pending')->create([
        'donor_email' => $email, 'amount' => 25000, 'time_range_label' => '1 Month',
    ]);

    Livewire::actingAs($admin)->test(DonorShow::class, ['email' => $email])
        ->assertViewHas('donationsCount', 3)
        ->assertViewHas('totalGiven', 175000.0)
        ->assertViewHas('successfulTotal', 150000.0)
        ->assertViewHas('supportPeriods', fn ($p) => $p->count() === 2
            && $p->contains('1 Month') && $p->contains('1 Year'));
});
