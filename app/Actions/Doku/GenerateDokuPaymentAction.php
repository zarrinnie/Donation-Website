<?php

namespace App\Actions\Doku;

use App\DTOs\Doku\CreateInvoiceData;
use App\Models\Donation;
use App\Services\Doku\DokuClient;
use Illuminate\Support\Facades\URL;

class GenerateDokuPaymentAction
{
    public function __construct(private readonly DokuClient $client) {}

    /**
     * Generate a fresh DOKU hosted-checkout invoice for a pending donation
     * and persist the correlation fields. Called on-demand, only at the
     * moment the donor clicks "Pay" — never pre-generated. Used for both a
     * donor's first gift and a monthly renewal, so the redirect target is
     * always the same signed Thank You route, regardless of entry point.
     */
    public function execute(Donation $donation): Donation
    {
        // A fresh invoice number per attempt: a donor may click "Pay" again
        // on an abandoned/expired checkout, and DOKU requires a unique
        // invoice_number per create-invoice call even for the same donation.
        $invoiceNumber = $donation->reference.'-'.now()->format('YmdHis');

        $successUrl = URL::temporarySignedRoute(
            'donate.thank-you',
            now()->addHours(2),
            ['donation' => $donation->reference],
        );

        $result = $this->client->createInvoice(new CreateInvoiceData(
            invoiceNumber: $invoiceNumber,
            amount: (int) round((float) $donation->amount),
            customerName: $donation->donor_name,
            customerEmail: $donation->donor_email,
            customerPhone: $donation->donor_phone,
            successRedirectUrl: $successUrl,
            failureRedirectUrl: $successUrl,
        ));

        $donation->update([
            'doku_invoice_number' => $result->invoiceNumber,
            'doku_payment_url' => $result->paymentUrl,
            'doku_response_payload' => $result->rawResponse,
        ]);

        return $donation;
    }
}
