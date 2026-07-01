<?php

namespace App\DTOs\Donation;

class DonationSettingData
{
    public function __construct(
        public string $type,        // 'amount' | 'time_range'
        public string $label,
        public string $value,       // amount, or number of days for a time range
        public bool $is_active = true,
        public int $sort_order = 0,
    ) {}
}
