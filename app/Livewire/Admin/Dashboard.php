<?php

namespace App\Livewire\Admin;

use App\Models\Donation;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Latest Donations')]
class Dashboard extends Component
{
    public function render()
    {
        return view('livewire.admin.dashboard', [
            'totalRaised' => (float) Donation::successful()->sum('amount'),
            'countSuccessful' => Donation::where('status', Donation::STATUS_SUCCESSFUL)->count(),
            'countPending' => Donation::where('status', Donation::STATUS_PENDING)->count(),
            'countFailed' => Donation::where('status', Donation::STATUS_FAILED)->count(),
            'totalCount' => Donation::count(),
            'donations' => Donation::latestFirst()->limit(10)->get(),
        ]);
    }
}
