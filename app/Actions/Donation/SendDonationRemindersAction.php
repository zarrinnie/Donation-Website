<?php

namespace App\Actions\Donation;

use App\Mail\DonationReminderMail;
use App\Models\Donation;
use Illuminate\Support\Facades\Mail;

class SendDonationRemindersAction
{
    /**
     * Email a "come give again" reminder to every donor whose chosen support
     * period has ended (created_at + time_range_days is in the past) and who
     * hasn't already been reminded. Each donation is reminded exactly once.
     *
     * The due-date check is done in PHP (not raw SQL) so it behaves the same
     * on MySQL and the SQLite test database.
     *
     * @return int Number of reminders sent.
     */
    public function execute(): int
    {
        $due = Donation::query()
            ->successful()
            ->whereNotNull('time_range_days')
            ->whereNull('reminder_sent_at')
            ->get()
            ->filter(fn (Donation $d) => $d->created_at->copy()->addDays($d->time_range_days)->isPast());

        foreach ($due as $donation) {
            Mail::to($donation->donor_email)->send(new DonationReminderMail($donation));
            $donation->update(['reminder_sent_at' => now()]);
        }

        return $due->count();
    }
}
