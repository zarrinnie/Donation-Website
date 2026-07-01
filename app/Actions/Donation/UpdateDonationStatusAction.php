<?php

namespace App\Actions\Donation;

use App\Models\Donation;
use InvalidArgumentException;

class UpdateDonationStatusAction
{
    /**
     * Update a donation's status to one of: successful, pending, failed.
     */
    public function execute(Donation $donation, string $status): Donation
    {
        if (! in_array($status, Donation::STATUSES, true)) {
            throw new InvalidArgumentException("Invalid donation status: {$status}");
        }

        $donation->update(['status' => $status]);

        return $donation;
    }
}
