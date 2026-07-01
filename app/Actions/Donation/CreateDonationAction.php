<?php

namespace App\Actions\Donation;

use App\DTOs\Donation\DonationData;
use App\Models\Donation;
use Illuminate\Support\Str;

class CreateDonationAction
{
    /**
     * Persist a new donation from the public checkout. Donations start
     * in the `pending` state until an admin reviews them.
     */
    public function execute(DonationData $data): Donation
    {
        return Donation::create([
            'donor_name' => $data->donor_name,
            'donor_age' => $data->donor_age,
            'donor_email' => $data->donor_email,
            'donor_phone' => $data->donor_phone,
            'amount' => $data->amount,
            'time_range_label' => $data->time_range_label,
            'time_range_days' => $data->time_range_days,
            'is_custom_amount' => $data->is_custom_amount,
            'is_custom_range' => $data->is_custom_range,
            'status' => Donation::STATUS_PENDING,
            'payment_method' => $data->payment_method,
            'reference' => 'GCC-'.strtoupper(Str::random(10)),
        ]);
    }
}
