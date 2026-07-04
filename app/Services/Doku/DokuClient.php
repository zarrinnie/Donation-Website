<?php

namespace App\Services\Doku;

use App\DTOs\Doku\CreateInvoiceData;
use App\DTOs\Doku\DokuInvoiceResult;
use App\Exceptions\Doku\DokuRequestFailedException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/**
 * Thin wrapper around DOKU's Checkout API. Owns the wire format (headers,
 * signing, request/response shape) so Actions stay about orchestration —
 * this is the first external-API client in this codebase, setting the
 * convention rather than following one.
 *
 * NOTE: the request/response payload shape below follows DOKU's documented
 * Checkout API. Verify field names against DOKU's current sandbox docs
 * before going live.
 */
class DokuClient
{
    private const CREATE_INVOICE_PATH = '/checkout/v1/payment';

    public function __construct(
        private readonly string $clientId,
        private readonly string $secretKey,
        private readonly string $baseUrl,
    ) {}

    public function createInvoice(CreateInvoiceData $data): DokuInvoiceResult
    {
        $body = [
            'order' => [
                'invoice_number' => $data->invoiceNumber,
                'amount' => $data->amount,
                'currency' => $data->currency,
            ],
            'payment' => [
                'payment_due_date' => 60, // minutes
                'success_redirect_url' => $data->successRedirectUrl,
                'failure_redirect_url' => $data->failureRedirectUrl,
            ],
            'customer' => [
                'name' => $data->customerName,
                'email' => $data->customerEmail,
                'phone' => $data->customerPhone,
            ],
        ];

        $rawBody = json_encode($body, JSON_THROW_ON_ERROR);
        $requestId = (string) Str::uuid();
        $requestTimestamp = now()->utc()->format('Y-m-d\TH:i:s\Z');
        $digest = DokuSignature::digest($rawBody);
        $canonicalString = DokuSignature::canonicalString(
            $this->clientId,
            $requestId,
            $requestTimestamp,
            self::CREATE_INVOICE_PATH,
            $digest,
        );
        $signature = DokuSignature::sign($this->secretKey, $canonicalString);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Client-Id' => $this->clientId,
            'Request-Id' => $requestId,
            'Request-Timestamp' => $requestTimestamp,
            'Digest' => $digest,
            'Signature' => $signature,
        ])->withBody($rawBody, 'application/json')
            ->post($this->baseUrl.self::CREATE_INVOICE_PATH);

        if ($response->failed()) {
            throw new DokuRequestFailedException(
                "DOKU createInvoice failed ({$response->status()}): {$response->body()}"
            );
        }

        $json = $response->json();

        return new DokuInvoiceResult(
            paymentUrl: (string) Arr::get($json, 'response.payment.url', ''),
            invoiceNumber: (string) Arr::get($json, 'response.order.invoice_number', $data->invoiceNumber),
            paymentId: Arr::get($json, 'response.payment.token_id'),
            rawResponse: $json ?? [],
        );
    }
}
