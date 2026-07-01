<?php

namespace App\Livewire\Public;

use App\Models\Donation;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.guest')]
#[Title('Grace Community Church — Give with purpose')]
class Landing extends Component
{
    public function render()
    {
        return view('livewire.public.landing', [
            'totalDonated' => (float) Donation::successful()->sum('amount'),
            'donorCount' => Donation::successful()->distinct('donor_email')->count('donor_email'),
            'donationCount' => Donation::successful()->count(),
        ]);
    }
}
