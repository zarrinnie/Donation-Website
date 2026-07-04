<?php

namespace App\Actions\Donation;

use App\Models\Donation;
use InvalidArgumentException;

class UpdateDonationStatusAction
{
    /**
     * Update a donation's status to one of: successful, pending, failed.
     * $source records whether this came from the DOKU webhook or an admin
     * manual override, for audit purposes.
     */
    public function execute(Donation $donation, string $status, string $source = Donation::STATUS_SOURCE_ADMIN): Donation
    {
        if (! in_array($status, Donation::STATUSES, true)) {
            throw new InvalidArgumentException("Invalid donation status: {$status}");
        }

        $donation->update(['status' => $status, 'status_source' => $source]);

        return $donation;
    }
}
