<?php

use App\Actions\Doku\GenerateDokuPaymentAction;
use App\Livewire\Public\Payment;
use App\Models\Donation;
use Illuminate\Support\Facades\Http;
use Livewire\Livewire;

function fakeDokuInvoiceResponse(string $invoiceNumber = 'INV-123'): array
{
    return [
        'response' => [
            'order' => ['invoice_number' => $invoiceNumber],
            'payment' => ['url' => 'https://sandbox.doku.com/checkout/'.$invoiceNumber, 'token_id' => 'tok_abc'],
        ],
    ];
}

it('generates a doku invoice and persists the payment url on the donation', function () {
    Http::fake([
        'api-sandbox.doku.com/*' => Http::response(fakeDokuInvoiceResponse('INV-999'), 200),
    ]);

    $donation = Donation::factory()->status('pending')->create(['amount' => 150000]);

    $result = app(GenerateDokuPaymentAction::class)->execute($donation);

    expect($result->doku_invoice_number)->toBe('INV-999')
        ->and($result->doku_payment_url)->toBe('https://sandbox.doku.com/checkout/INV-999')
        ->and($result->doku_response_payload)->toBeArray();

    Http::assertSent(function ($request) {
        return $request->hasHeader('Client-Id')
            && $request->hasHeader('Request-Id')
            && $request->hasHeader('Request-Timestamp')
            && $request->hasHeader('Digest')
            && $request->hasHeader('Signature');
    });
});

it('lets a donor pay and redirects to the doku hosted checkout page', function () {
    Http::fake([
        'api-sandbox.doku.com/*' => Http::response(fakeDokuInvoiceResponse('INV-ABC'), 200),
    ]);

    session()->put('donation_intent', [
        'donor_name' => 'Jane Doe',
        'donor_age' => 30,
        'donor_email' => 'jane@example.com',
        'donor_phone' => '+62 811 0000 000',
        'amount' => 50000,
        'time_range_label' => '1 Month',
        'time_range_days' => 30,
        'is_custom_amount' => false,
        'is_custom_range' => false,
    ]);

    Livewire::test(Payment::class)
        ->call('pay')
        ->assertRedirect('https://sandbox.doku.com/checkout/INV-ABC');

    expect(session('donation_intent'))->toBeNull();

    $donation = Donation::first();
    expect($donation->status)->toBe(Donation::STATUS_PENDING)
        ->and($donation->doku_payment_url)->toBe('https://sandbox.doku.com/checkout/INV-ABC')
        ->and($donation->payment_method)->toBe('doku');
});

it('shows an error and does not redirect when doku is unreachable', function () {
    Http::fake([
        'api-sandbox.doku.com/*' => Http::response(['error' => 'server error'], 500),
    ]);

    session()->put('donation_intent', [
        'donor_name' => 'Jane Doe',
        'donor_email' => 'jane@example.com',
        'amount' => 50000,
        'time_range_label' => '1 Month',
        'time_range_days' => 30,
    ]);

    Livewire::test(Payment::class)
        ->call('pay')
        ->assertNoRedirect();

    // The donation was still recorded (pending) even though DOKU failed.
    expect(Donation::count())->toBe(1)
        ->and(Donation::first()->doku_payment_url)->toBeNull();
});
