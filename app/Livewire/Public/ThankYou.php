<?php

namespace App\Livewire\Public;

use App\Models\Donation;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.guest')]
#[Title('Thank You — Grace Community Church')]
class ThankYou extends Component
{
    public ?Donation $donation = null;

    public function mount()
    {
        $ref = session('last_donation_ref');

        if (! $ref) {
            return $this->redirect(route('donate'), navigate: true);
        }

        $this->donation = Donation::where('reference', $ref)->first();
    }

    public function render()
    {
        return view('livewire.public.thank-you');
    }
}
