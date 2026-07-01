<?php

namespace App\Actions\DonationSetting;

use App\DTOs\Donation\DonationSettingData;
use App\Models\DonationSetting;

class UpdateDonationSettingAction
{
    public function execute(DonationSetting $setting, DonationSettingData $data): DonationSetting
    {
        $setting->update([
            'type' => $data->type,
            'label' => $data->label,
            'value' => $data->value,
            'is_active' => $data->is_active,
            'sort_order' => $data->sort_order,
        ]);

        return $setting;
    }
}
