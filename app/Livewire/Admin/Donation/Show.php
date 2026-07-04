<?php

namespace App\Livewire\Admin\Donation;

use App\Actions\Donation\SendDonationStatusEmailAction;
use App\Actions\Donation\UpdateDonationStatusAction;
use App\Models\Donation;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mary\Traits\Toast;

#[Layout('layouts.app')]
#[Title('Donation Detail')]
class Show extends Component
{
    use Toast;

    public Donation $donation;

    public function mount(Donation $donation): void
    {
        // Opening the detail page clears the "newly received" marker.
        if ($donation->admin_seen_at === null) {
            $donation->forceFill(['admin_seen_at' => now()])->save();
        }

        $this->donation = $donation;
    }

    /** Update the donation's status (successful / pending / failed). */
    public function setStatus(string $status, UpdateDonationStatusAction $action): void
    {
        $action->execute($this->donation, $status);
        $this->donation->refresh();

        $this->success("Status updated to {$status}.");
    }

    /** (Re)send the donor the verification email matching the current status. */
    public function resendEmail(SendDonationStatusEmailAction $action): void
    {
        $action->execute($this->donation);
        $this->donation->refresh();

        $this->success('Email sent', "A '{$this->donation->status}' notice was sent to {$this->donation->donor_email}.");
    }

    public function render()
    {
        return view('livewire.admin.donation.show', [
            'statuses' => Donation::STATUSES,
        ]);
    }
}
