<?php

namespace App\DTOs\Doku;

class CreateInvoiceData
{
    public function __construct(
        public string $invoiceNumber,
        public int $amount,
        public string $customerName,
        public string $customerEmail,
        public ?string $customerPhone,
        public string $successRedirectUrl,
        public string $failureRedirectUrl,
        public string $currency = 'IDR',
    ) {}
}
