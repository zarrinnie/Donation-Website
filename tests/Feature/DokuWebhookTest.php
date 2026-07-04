<?php

use App\Mail\DonationStatusMail;
use App\Models\Donation;
use App\Models\DonationSubscription;
use App\Services\Doku\DokuSignature;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

function dokuWebhookHeaders(string $rawBody, string $path, string $clientId, string $secret): array
{
    $requestId = (string) Str::uuid();
    $timestamp = now()->utc()->format('Y-m-d\TH:i:s\Z');
    $digest = DokuSignature::digest($rawBody);
    $canonical = DokuSignature::canonicalString($clientId, $requestId, $timestamp, $path, $digest);
    $signature = DokuSignature::sign($secret, $canonical);

    return [
        'Content-Type' => 'application/json',
        'Client-Id' => $clientId,
        'Request-Id' => $requestId,
        'Request-Timestamp' => $timestamp,
        'Digest' => $digest,
        'Signature' => $signature,
    ];
}

function postDokuWebhook(array $payload, array $headers)
{
    $rawBody = json_encode($payload);

    return test()->call(
        'POST',
        route('webhooks.doku.notification'),
        server: test()->transformHeadersToServerVars($headers),
        content: $rawBody,
    );
}

beforeEach(function () {
    config([
        'services.doku.client_id' => 'test-client',
        'services.doku.webhook_secret' => 'test-secret',
    ]);
});

it('processes a validly signed successful payment notification and enrolls a subscription', function () {
    Mail::fake();
    $donation = Donation::factory()->status('pending')->create(['doku_invoice_number' => 'INV-1']);

    $payload = [
        'order' => ['invoice_number' => 'INV-1', 'amount' => (int) $donation->amount],
        'transaction' => ['status' => 'SUCCESS', 'date' => now()->toIso8601String()],
    ];
    $headers = dokuWebhookHeaders(json_encode($payload), '/api/webhooks/doku/notification', 'test-client', 'test-secret');

    postDokuWebhook($payload, $headers)->assertOk();

    $donation->refresh();
    expect($donation->status)->toBe(Donation::STATUS_SUCCESSFUL)
        ->and($donation->status_source)->toBe(Donation::STATUS_SOURCE_WEBHOOK)
        ->and($donation->paid_at)->not->toBeNull();

    expect(DonationSubscription::where('donor_email', $donation->donor_email)->active()->exists())->toBeTrue();
    Mail::assertSent(DonationStatusMail::class);
});

it('rejects a notification with a tampered signature', function () {
    $donation = Donation::factory()->status('pending')->create(['doku_invoice_number' => 'INV-2']);

    $payload = ['order' => ['invoice_number' => 'INV-2', 'amount' => 1], 'transaction' => ['status' => 'SUCCESS']];
    $headers = dokuWebhookHeaders(json_encode($payload), '/api/webhooks/doku/notification', 'test-client', 'test-secret');
    $headers['Signature'] = 'HMACSHA256=invalidinvalidinvalid';

    postDokuWebhook($payload, $headers)->assertUnauthorized();

    expect($donation->fresh()->status)->toBe(Donation::STATUS_PENDING);
});

it('rejects a notification with a stale timestamp', function () {
    $donation = Donation::factory()->status('pending')->create(['doku_invoice_number' => 'INV-3']);

    $payload = ['order' => ['invoice_number' => 'INV-3', 'amount' => 1], 'transaction' => ['status' => 'SUCCESS']];
    $rawBody = json_encode($payload);

    $requestId = (string) Str::uuid();
    $staleTimestamp = now()->subMinutes(10)->utc()->format('Y-m-d\TH:i:s\Z');
    $digest = DokuSignature::digest($rawBody);
    $canonical = DokuSignature::canonicalString('test-client', $requestId, $staleTimestamp, '/api/webhooks/doku/notification', $digest);
    $signature = DokuSignature::sign('test-secret', $canonical);

    $headers = [
        'Content-Type' => 'application/json',
        'Client-Id' => 'test-client',
        'Request-Id' => $requestId,
        'Request-Timestamp' => $staleTimestamp,
        'Digest' => $digest,
        'Signature' => $signature,
    ];

    postDokuWebhook($payload, $headers)->assertUnauthorized();
    expect($donation->fresh()->status)->toBe(Donation::STATUS_PENDING);
});

it('safely ignores a duplicate notification for an already-processed donation', function () {
    Mail::fake();
    $donation = Donation::factory()->status('successful')->create(['doku_invoice_number' => 'INV-4']);

    $payload = [
        'order' => ['invoice_number' => 'INV-4', 'amount' => (int) $donation->amount],
        'transaction' => ['status' => 'SUCCESS', 'date' => now()->toIso8601String()],
    ];
    $headers = dokuWebhookHeaders(json_encode($payload), '/api/webhooks/doku/notification', 'test-client', 'test-secret');

    postDokuWebhook($payload, $headers)->assertOk();

    Mail::assertNothingSent();
    expect(DonationSubscription::count())->toBe(0);
});
