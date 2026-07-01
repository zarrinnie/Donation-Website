<?php

use App\Actions\Donation\CreateDonationAction;
use App\Actions\Donation\SendDonationStatusEmailAction;
use App\DTOs\Donation\DonationData;
use App\Livewire\Admin\Donation\Index as AdminDonationIndex;
use App\Livewire\Public\Donate;
use App\Mail\DonationStatusMail;
use App\Models\Donation;
use App\Models\DonationSetting;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

it('creates a pending donation with a unique reference', function () {
    $donation = app(CreateDonationAction::class)->execute(new DonationData(
        donor_name: 'Jane Doe',
        donor_age: 30,
        donor_email: 'jane@example.com',
        donor_phone: '+1 555 000 1111',
        amount: 50.00,
        time_range_label: '1 Month',
        time_range_days: 30,
    ));

    expect($donation->status)->toBe(Donation::STATUS_PENDING)
        ->and($donation->reference)->toStartWith('GCC-')
        ->and((float) $donation->amount)->toBe(50.00);
});

it('sends a status-specific email matching the donation status', function () {
    Mail::fake();

    foreach (Donation::STATUSES as $status) {
        $donation = Donation::factory()->status($status)->create();
        app(SendDonationStatusEmailAction::class)->execute($donation);
    }

    Mail::assertSent(DonationStatusMail::class, 3);

    // The successful email subject confirms the gift.
    Mail::assertSent(DonationStatusMail::class, function (DonationStatusMail $mail) {
        return $mail->donation->status === Donation::STATUS_SUCCESSFUL
            && str_contains($mail->envelope()->subject, 'confirmed');
    });
});

it('lets a donor build a donation and reach the payment page', function () {
    DonationSetting::create(['type' => 'amount', 'label' => '$50', 'value' => '50', 'is_active' => true, 'sort_order' => 0]);
    DonationSetting::create(['type' => 'time_range', 'label' => '1 Month', 'value' => '30', 'is_active' => true, 'sort_order' => 0]);

    Livewire::test(Donate::class)
        ->set('amountChoice', '50')
        ->set('donor_name', 'John Giver')
        ->set('donor_email', 'john@example.com')
        ->set('donor_phone', '+1 555 222 3333')
        ->set('donor_age', 40)
        ->call('continueToPayment')
        ->assertRedirect(route('donate.payment'));

    expect(session('donation_intent'))->not->toBeNull()
        ->and(session('donation_intent')['amount'])->toBe(50.0);
});

it('allows an admin to change a donation status and notify the donor', function () {
    Mail::fake();

    $admin = User::factory()->create(['role' => 'admin']);
    $donation = Donation::factory()->status('pending')->create();

    Livewire::actingAs($admin)
        ->test(AdminDonationIndex::class)
        ->call('setStatus', $donation->id, 'successful')
        ->call('sendEmail', $donation->id);

    expect($donation->fresh()->status)->toBe('successful');
    Mail::assertSent(DonationStatusMail::class);
});

it('blocks guests and regular users from the admin donations ledger', function () {
    $this->get(route('admin.donations'))->assertRedirect(route('login'));

    $user = User::factory()->create(['role' => 'user']);
    $this->actingAs($user)->get(route('admin.donations'))->assertRedirect();
});
