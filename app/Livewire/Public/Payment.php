<?php

namespace App\Livewire\Public;

use App\Actions\Doku\GenerateDokuPaymentAction;
use App\Actions\Donation\CreateDonationAction;
use App\DTOs\Donation\DonationData;
use App\Exceptions\Doku\DokuRequestFailedException;
use Illuminate\Support\Facades\Log;
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

    public function mount()
    {
        $this->intent = session('donation_intent', []);

        // No active donation in progress — send the donor back to start.
        if (empty($this->intent)) {
            return $this->redirect(route('donate'), navigate: true);
        }
    }

    /**
     * Create the pending donation, then generate a real DOKU hosted-checkout
     * link on demand (never pre-generated) and send the donor there.
     */
    public function pay(CreateDonationAction $createDonation, GenerateDokuPaymentAction $generatePayment)
    {
        $intent = session('donation_intent');
        if (empty($intent)) {
            return $this->redirect(route('donate'), navigate: true);
        }

        $donation = $createDonation->execute(new DonationData(
            donor_name: $intent['donor_name'],
            donor_age: $intent['donor_age'] ?? null,
            donor_email: $intent['donor_email'],
            donor_phone: $intent['donor_phone'] ?? null,
            amount: (float) $intent['amount'],
            time_range_label: $intent['time_range_label'],
            time_range_days: $intent['time_range_days'] ?? null,
            is_custom_amount: (bool) ($intent['is_custom_amount'] ?? false),
            is_custom_range: (bool) ($intent['is_custom_range'] ?? false),
            payment_method: 'doku',
        ));

        try {
            $donation = $generatePayment->execute($donation);
        } catch (DokuRequestFailedException $e) {
            Log::error('doku.generate_payment_failed', ['donation_id' => $donation->id, 'message' => $e->getMessage()]);
            $this->error('We could not reach the payment provider — please try again shortly.');

            return null;
        }

        session()->forget('donation_intent');

        // External redirect (DOKU's own domain) — Livewire's SPA `navigate`
        // is same-origin only, so this must be a plain full-page redirect.
        return $this->redirect($donation->doku_payment_url);
    }

    public function render()
    {
        return view('livewire.public.payment');
    }
}
