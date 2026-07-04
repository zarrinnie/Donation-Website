<?php

namespace App\Livewire\Admin\Donor;

use App\Models\Donation;
use App\Models\DonationSubscription;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Donor Detail')]
class Show extends Component
{
    public string $email;

    public function mount(string $email): void
    {
        // Route param arrives URL-decoded already; keep the raw email for lookups.
        $this->email = $email;

        abort_unless(Donation::where('donor_email', $email)->exists(), 404);
    }

    public function render()
    {
        $donations = Donation::where('donor_email', $this->email)
            ->latest()
            ->get();

        $latest = $donations->first();

        // "Campaigns" here = the distinct support-period labels this donor gave under.
        $supportPeriods = $donations
            ->pluck('time_range_label')
            ->filter()
            ->unique()
            ->values();

        $subscription = DonationSubscription::where('donor_email', $this->email)
            ->active()
            ->first();

        return view('livewire.admin.donor.show', [
            'donorName' => $latest?->donor_name ?? $this->email,
            'donorPhone' => $latest?->donor_phone,
            'donorAge' => $latest?->donor_age,
            'donations' => $donations,
            'donationsCount' => $donations->count(),
            'totalGiven' => (float) $donations->sum('amount'),
            'successfulTotal' => (float) $donations->where('status', Donation::STATUS_SUCCESSFUL)->sum('amount'),
            'supportPeriods' => $supportPeriods,
            'subscription' => $subscription,
        ]);
    }
}
