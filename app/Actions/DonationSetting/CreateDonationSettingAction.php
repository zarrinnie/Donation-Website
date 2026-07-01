<?php

namespace App\Actions\DonationSetting;

use App\DTOs\Donation\DonationSettingData;
use App\Models\DonationSetting;

class CreateDonationSettingAction
{
    public function execute(DonationSettingData $data): DonationSetting
    {
        return DonationSetting::create([
            'type' => $data->type,
            'label' => $data->label,
            'value' => $data->value,
            'is_active' => $data->is_active,
            'sort_order' => $data->sort_order,
        ]);
    }
}
