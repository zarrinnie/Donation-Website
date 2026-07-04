<?php

namespace App\DTOs\Subscription;

class EnrollSubscriptionData
{
    public function __construct(
        public string $donorName,
        public string $donorEmail,
        public ?string $donorPhone,
        public float $amount,
        public int $sourceDonationId,
    ) {}
}
