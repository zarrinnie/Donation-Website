<?php

namespace App\DTOs\Donation;

class DonationData
{
    public function __construct(
        public string $donor_name,
        public ?int $donor_age,
        public string $donor_email,
        public ?string $donor_phone,
        public float $amount,
        public string $time_range_label,
        public ?int $time_range_days,
        public bool $is_custom_amount = false,
        public bool $is_custom_range = false,
        public ?string $payment_method = 'card',
    ) {}
}
