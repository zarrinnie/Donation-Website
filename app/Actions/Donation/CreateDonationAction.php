<?php

namespace App\Actions\Donation;

use App\DTOs\Donation\DonationData;
use App\Models\Donation;

class CreateDonationAction
{
    /**
     * Persist a new donation from the public checkout. Donations start
     * in the `pending` state until an admin reviews them (or, for DOKU
     * checkouts, until the payment webhook confirms them). Passing a
     * $subscriptionId links this charge back to the recurring plan it
     * renews (omit for a donor's first-ever gift).
     */
    public function execute(DonationData $data, ?int $subscriptionId = null): Donation
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
            'reference' => Donation::generateReference(),
            'subscription_id' => $subscriptionId,
        ]);
    }
}
