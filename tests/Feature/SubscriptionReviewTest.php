<?php

use App\Livewire\Public\SubscriptionReview;
use App\Models\Donation;
use App\Models\DonationSubscription;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;

function signedSubscriptionReviewUrl(DonationSubscription $subscription): string
{
    return URL::temporarySignedRoute('donate.subscription.review', now()->addDays(14), ['subscription' => $subscription->id]);
}

it('rejects an unsigned subscription review link', function () {
    $subscription = DonationSubscription::factory()->create();

    $this->get(route('donate.subscription.review', ['subscription' => $subscription->id]))
        ->assertForbidden();
});

it('prefills the previous amount and lets a donor adjust and pay again', function () {
    Http::fake([
        'api-sandbox.doku.com/*' => Http::response([
            'response' => [
                'order' => ['invoice_number' => 'INV-RENEW'],
                'payment' => ['url' => 'https://sandbox.doku.com/checkout/INV-RENEW', 'token_id' => 'tok'],
            ],
        ], 200),
    ]);

    $subscription = DonationSubscription::factory()->create(['amount' => 100000]);
    $url = signedSubscriptionReviewUrl($subscription);

    Livewire::test(SubscriptionReview::class, ['subscription' => $subscription])
        ->assertSet('amount', 100000.0)
        ->set('amount', 75000)
        ->call('payAgain')
        ->assertRedirect('https://sandbox.doku.com/checkout/INV-RENEW');

    $donation = Donation::where('subscription_id', $subscription->id)->first();
    expect($donation)->not->toBeNull()
        ->and((float) $donation->amount)->toBe(75000.0)
        ->and($donation->donor_email)->toBe($subscription->donor_email);

    $this->get($url)->assertOk();
});

it('blocks renewal for a cancelled subscription', function () {
    $subscription = DonationSubscription::factory()->cancelled()->create();

    Livewire::test(SubscriptionReview::class, ['subscription' => $subscription])
        ->assertStatus(410);
});
