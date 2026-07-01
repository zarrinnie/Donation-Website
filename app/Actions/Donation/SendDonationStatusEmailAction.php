<?php

namespace App\Actions\Donation;

use App\Mail\DonationStatusMail;
use App\Models\Donation;
use Illuminate\Support\Facades\Mail;

class SendDonationStatusEmailAction
{
    /**
     * Send the donor a status-verification email matching the donation's
     * current status (successful / pending / failed). With MAIL_MAILER=log
     * the message is written to storage/logs/laravel.log (mocked delivery).
     */
    public function execute(Donation $donation): void
    {
        Mail::to($donation->donor_email)->send(new DonationStatusMail($donation));
    }
}
