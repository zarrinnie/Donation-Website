<?php

namespace App\Livewire\Public;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.guest')]
#[Title('About — Grace Community Church')]
class About extends Component
{
    public function render()
    {
        return view('livewire.public.about');
    }
}
