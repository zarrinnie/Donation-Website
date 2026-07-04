<?php

namespace App\Actions\Donation;

use App\Models\Donation;
use App\Models\User;
use App\Notifications\GeneralNotification;
use Illuminate\Support\Facades\Notification;

class NotifyAdminsOfDonationAction
{
    /**
     * Notify every admin / super_admin that a new donation has been received.
     * Delivered via the database channel (the navbar bell) and broadcast
     * (real-time via Pusher when configured). A no-op when there are no
     * admin users.
     */
    public function execute(Donation $donation): void
    {
        $admins = User::whereIn('role', ['super_admin', 'admin'])->get();

        if ($admins->isEmpty()) {
            return;
        }

        $amount = 'Rp '.number_format((float) $donation->amount, 0, ',', '.');

        Notification::send($admins, new GeneralNotification([
            'title' => 'New donation received',
            'message' => "{$donation->donor_name} donated {$amount}",
            'url' => route('admin.donations.show', $donation),
            'icon' => 'o-banknotes',
            'color' => 'text-success',
        ]));
    }
}
