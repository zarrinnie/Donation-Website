<?php

namespace App\Livewire\Admin\Donation;

use App\Actions\Donation\SendDonationStatusEmailAction;
use App\Actions\Donation\UpdateDonationStatusAction;
use App\Models\Donation;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Mary\Traits\Toast;

#[Layout('layouts.app')]
#[Title('Donations Ledger')]
class Index extends Component
{
    use Toast, WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public string $filterStatus = '';

    #[Url(history: true)]
    public string $sortBy = 'latest';

    public function updated($property): void
    {
        if (in_array($property, ['search', 'filterStatus', 'sortBy'], true)) {
            $this->resetPage();
        }
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'filterStatus', 'sortBy']);
        $this->resetPage();
    }

    /** Update a donation's status (successful / pending / failed). */
    public function setStatus(int $id, string $status, UpdateDonationStatusAction $action): void
    {
        $donation = Donation::findOrFail($id);
        $action->execute($donation, $status);

        $this->success("Status updated to {$status}.");
    }

    /** Send the donor the verification email matching the current status. */
    public function sendEmail(int $id, SendDonationStatusEmailAction $action): void
    {
        $donation = Donation::findOrFail($id);
        $action->execute($donation);

        $this->success('Email queued', "A '{$donation->status}' notice was sent to {$donation->donor_email}.");
    }

    public function render()
    {
        $query = Donation::query();

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('donor_name', 'like', "%{$this->search}%")
                    ->orWhere('donor_email', 'like', "%{$this->search}%")
                    ->orWhere('reference', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterStatus !== '') {
            $query->where('status', $this->filterStatus);
        }

        match ($this->sortBy) {
            'oldest' => $query->oldest(),
            'amount_high' => $query->orderByDesc('amount'),
            'amount_low' => $query->orderBy('amount'),
            default => $query->latest(),
        };

        return view('livewire.admin.donation.index', [
            'donations' => $query->paginate(12),
            'statuses' => Donation::STATUSES,
        ]);
    }
}
