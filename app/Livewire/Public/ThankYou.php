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
    public Donation $donation;

    /**
     * Route-bound by `reference` and gated by the `signed` route middleware
     * (see routes/web.php), rather than session state — this page is reached
     * both via DOKU's redirect-back from an external domain and via a link
     * in the confirmation email, neither of which reliably carries session.
     */
    public function mount(Donation $donation): void
    {
        $this->donation = $donation;
    }

    public function render()
    {
        return view('livewire.public.thank-you');
    }
}
