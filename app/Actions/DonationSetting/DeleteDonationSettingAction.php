<?php

namespace App\Actions\DonationSetting;

use App\Models\DonationSetting;

class DeleteDonationSettingAction
{
    public function execute(DonationSetting $setting): void
    {
        $setting->delete();
    }
}
