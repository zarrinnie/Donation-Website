<?php

namespace App\Livewire\Public;

use App\Actions\Doku\GenerateDokuPaymentAction;
use App\Actions\Donation\CreateDonationAction;
use App\DTOs\Donation\DonationData;
use App\Exceptions\Doku\DokuRequestFailedException;
use App\Models\DonationSubscription;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mary\Traits\Toast;

#[Layout('layouts.guest')]
#[Title('Renew Your Gift — Grace Community Church')]
class SubscriptionReview extends Component
{
    use Toast;

    public DonationSubscription $subscription;

    public float $amount = 0;

    /**
     * Route-bound and gated by the `signed` route middleware (see
     * routes/web.php) — the link is only ever reached via the monthly
     * renewal email, never guessable.
     */
    public function mount(DonationSubscription $subscription): void
    {
        abort_if($subscription->status !== DonationSubscription::STATUS_ACTIVE, 410);

        $this->subscription = $subscription;
        $this->amount = (float) $subscription->amount;
    }

    protected function rules(): array
    {
        return [
            'amount' => 'required|numeric|min:10000',
        ];
    }

    /**
     * Only the amount is adjustable here — donor identity comes from the
     * subscription itself, not the form, so it can't be tampered with.
     */
    public function payAgain(CreateDonationAction $createDonation, GenerateDokuPaymentAction $generatePayment)
    {
        $this->validate();

        $donation = $createDonation->execute(new DonationData(
            donor_name: $this->subscription->donor_name,
            donor_age: null,
            donor_email: $this->subscription->donor_email,
            donor_phone: $this->subscription->donor_phone,
            amount: $this->amount,
            time_range_label: 'Monthly',
            time_range_days: 30,
            payment_method: 'doku',
        ), $this->subscription->id);

        try {
            $donation = $generatePayment->execute($donation);
        } catch (DokuRequestFailedException $e) {
            Log::error('doku.generate_payment_failed', ['donation_id' => $donation->id, 'message' => $e->getMessage()]);
            $this->error('We could not reach the payment provider — please try again shortly.');

            return null;
        }

        return $this->redirect($donation->doku_payment_url);
    }

    public function render()
    {
        return view('livewire.public.subscription-review');
    }
}
