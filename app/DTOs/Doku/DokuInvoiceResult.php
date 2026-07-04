<?php

namespace App\DTOs\Doku;

class DokuInvoiceResult
{
    /**
     * @param  array<string, mixed>  $rawResponse
     */
    public function __construct(
        public string $paymentUrl,
        public string $invoiceNumber,
        public ?string $paymentId,
        public array $rawResponse,
    ) {}
}
