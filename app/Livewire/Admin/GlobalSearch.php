<?php

namespace App\Livewire\Admin;

use App\Models\Donation;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Search Results')]
class GlobalSearch extends Component
{
    #[Url(as: 'q')]
    public string $search = '';

    // Advanced filter: constrain admin-user results by role.
    public string $filterUserRole = '';

    public function render()
    {
        $term = trim($this->search);

        // 1. Donations (donor name / email / reference)
        $donations = collect();
        if ($term) {
            $donations = Donation::query()
                ->where(fn ($q) => $q->where('donor_name', 'like', "%$term%")
                    ->orWhere('donor_email', 'like', "%$term%")
                    ->orWhere('reference', 'like', "%$term%"))
                ->latest()
                ->limit(10)
                ->get();
        }

        // 2. Admin users (name / email), optionally filtered by role.
        $users = collect();
        if ($term) {
            $users = User::query()
                ->where(fn ($q) => $q->where('name', 'like', "%$term%")
                    ->orWhere('email', 'like', "%$term%"))
                ->when($this->filterUserRole, fn ($q) => $q->where('role', $this->filterUserRole))
                ->limit(10)
                ->get();
        }

        return view('livewire.admin.global-search', [
            'donations' => $donations,
            'users' => $users,
        ]);
    }
}
