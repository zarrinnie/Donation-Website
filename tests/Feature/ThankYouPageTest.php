<?php

use App\Models\Donation;
use Illuminate\Support\Facades\URL;

it('shows the thank you page for a validly signed url', function () {
    $donation = Donation::factory()->status('successful')->create();

    $url = URL::temporarySignedRoute('donate.thank-you', now()->addHours(2), ['donation' => $donation->reference]);

    $this->get($url)
        ->assertOk()
        ->assertSee($donation->reference);
});

it('rejects an unsigned or tampered thank you link', function () {
    $donation = Donation::factory()->status('successful')->create();

    $this->get(route('donate.thank-you', ['donation' => $donation->reference]))
        ->assertForbidden();

    $url = URL::temporarySignedRoute('donate.thank-you', now()->addHours(2), ['donation' => $donation->reference]);
    $tampered = $url.'x';

    $this->get($tampered)->assertForbidden();
});

it('shows a still-processing message while the donation is pending', function () {
    $donation = Donation::factory()->status('pending')->create();

    $url = URL::temporarySignedRoute('donate.thank-you', now()->addHours(2), ['donation' => $donation->reference]);

    $this->get($url)
        ->assertOk()
        ->assertSee('confirming your payment');
});
