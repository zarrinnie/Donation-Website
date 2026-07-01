<?php

namespace App\Livewire\Public;

use App\Actions\Donation\CreateDonationAction;
use App\DTOs\Donation\DonationData;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mary\Traits\Toast;

#[Layout('layouts.guest')]
#[Title('Checkout — Grace Community Church')]
class Payment extends Component
{
    use Toast;

    /** @var array<string, mixed> */
    public array $intent = [];

    // Mock gateway fields
    public string $card_name = '';

    public string $card_number = '';

    public string $card_expiry = '';

    public string $card_cvc = '';

    public function mount()
    {
        $this->intent = session('donation_intent', []);

        // No active donation in progress — send the donor back to start.
        if (empty($this->intent)) {
            return $this->redirect(route('donate'), navigate: true);
        }

        $this->card_name = $this->intent['donor_name'] ?? '';
    }

    protected function rules(): array
    {
        return [
            'card_name' => 'required|string|min:2',
            'card_number' => 'required|string|min:12|max:23',
            'card_expiry' => 'required|string|min:4|max:5',
            'card_cvc' => 'required|string|min:3|max:4',
        ];
    }

    public function pay(CreateDonationAction $action)
    {
        $this->validate();

        $intent = session('donation_intent');
        if (empty($intent)) {
            return $this->redirect(route('donate'), navigate: true);
        }

        // Mock gateway: we never charge a real card — we just record the gift.
        $donation = $action->execute(new DonationData(
            donor_name: $intent['donor_name'],
            donor_age: $intent['donor_age'] ?? null,
            donor_email: $intent['donor_email'],
            donor_phone: $intent['donor_phone'] ?? null,
            amount: (float) $intent['amount'],
            time_range_label: $intent['time_range_label'],
            time_range_days: $intent['time_range_days'] ?? null,
            is_custom_amount: (bool) ($intent['is_custom_amount'] ?? false),
            is_custom_range: (bool) ($intent['is_custom_range'] ?? false),
            payment_method: 'card',
        ));

        session()->forget('donation_intent');
        session()->put('last_donation_ref', $donation->reference);

        return $this->redirect(route('donate.thank-you'), navigate: true);
    }

    public function render()
    {
        return view('livewire.public.payment');
    }
}
