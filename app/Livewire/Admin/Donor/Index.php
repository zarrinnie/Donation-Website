<?php

namespace App\Livewire\Admin\Donor;

use App\Models\Donation;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Donor Directory')]
class Index extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $sortBy = 'recent';

    public function updated($property): void
    {
        if (in_array($property, ['search', 'sortBy'], true)) {
            $this->resetPage();
        }
    }

    public function render()
    {
        // Each unique donor (grouped by email) with their aggregated biodata + totals.
        $query = Donation::query()
            ->selectRaw('donor_email,
                MAX(donor_name) as donor_name,
                MAX(donor_age) as donor_age,
                MAX(donor_phone) as donor_phone,
                COUNT(*) as donations_count,
                SUM(amount) as total_amount,
                MAX(created_at) as last_donation')
            ->groupBy('donor_email');

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('donor_name', 'like', "%{$this->search}%")
                    ->orWhere('donor_email', 'like', "%{$this->search}%");
            });
        }

        match ($this->sortBy) {
            'top' => $query->orderByDesc('total_amount'),
            'name' => $query->orderBy('donor_name'),
            default => $query->orderByDesc('last_donation'),
        };

        return view('livewire.admin.donor.index', [
            'donors' => $query->paginate(15),
        ]);
    }
}
