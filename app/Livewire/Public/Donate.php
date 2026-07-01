<?php

namespace App\Livewire\Public;

use App\Actions\Donation\RememberDonorAction;
use App\Models\DonationSetting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.guest')]
#[Title('Make a Donation — Grace Community Church')]
class Donate extends Component
{
    // Preset options (loaded from DonationSetting)
    public array $amountOptions = [];

    public array $rangeOptions = [];

    // Selection — holds a preset value/id, or the literal string "custom"
    public ?string $amountChoice = null;

    public ?float $customAmount = null;

    public ?string $rangeChoice = null;

    public ?int $customRangeValue = null;

    public string $customRangeUnit = 'days'; // days | months | years

    // Donor biodata
    public string $donor_name = '';

    public ?int $donor_age = null;

    public string $donor_email = '';

    public string $donor_phone = '';

    public bool $remember = false;

    public function mount(RememberDonorAction $remember): void
    {
        $this->amountOptions = DonationSetting::query()->amounts()->active()->ordered()->get()
            ->map(fn ($o) => ['id' => (string) $o->id, 'label' => $o->label, 'value' => $o->value])
            ->all();

        $this->rangeOptions = DonationSetting::query()->timeRanges()->active()->ordered()->get()
            ->map(fn ($o) => ['id' => (string) $o->id, 'label' => $o->label, 'value' => (int) $o->value])
            ->all();

        // Default selections (first preset of each), for a friendly starting state.
        $this->amountChoice = $this->amountOptions[0]['value'] ?? 'custom';
        $this->rangeChoice = $this->rangeOptions[0]['id'] ?? 'custom';

        // Pre-fill biodata from the "Remember Me" cookie, if present.
        if ($saved = $remember->read()) {
            $this->donor_name = $saved['donor_name'] ?? '';
            $this->donor_age = isset($saved['donor_age']) ? (int) $saved['donor_age'] : null;
            $this->donor_email = $saved['donor_email'] ?? '';
            $this->donor_phone = $saved['donor_phone'] ?? '';
            $this->remember = true;
        }
    }

    public function selectAmount(string $value): void
    {
        $this->amountChoice = $value;
    }

    public function selectRange(string $id): void
    {
        $this->rangeChoice = $id;
    }

    protected function rules(): array
    {
        return [
            'amountChoice' => 'required',
            'customAmount' => 'required_if:amountChoice,custom|nullable|numeric|min:1',
            'rangeChoice' => 'required',
            'customRangeValue' => 'required_if:rangeChoice,custom|nullable|integer|min:1',
            'customRangeUnit' => 'required|in:days,months,years',
            'donor_name' => 'required|string|min:2|max:120',
            'donor_age' => 'nullable|integer|min:1|max:150',
            'donor_email' => 'required|email|max:160',
            'donor_phone' => 'required|string|min:6|max:40',
        ];
    }

    public function continueToPayment(RememberDonorAction $remember)
    {
        $this->validate();

        // Resolve amount
        $isCustomAmount = $this->amountChoice === 'custom';
        $amount = $isCustomAmount ? (float) $this->customAmount : (float) $this->amountChoice;

        // Resolve time range
        $isCustomRange = $this->rangeChoice === 'custom';
        if ($isCustomRange) {
            $multiplier = ['days' => 1, 'months' => 30, 'years' => 365][$this->customRangeUnit];
            $days = (int) $this->customRangeValue * $multiplier;
            $label = 'Custom: '.$this->customRangeValue.' '.$this->customRangeUnit;
        } else {
            $setting = collect($this->rangeOptions)->firstWhere('id', $this->rangeChoice);
            $days = $setting['value'] ?? null;
            $label = $setting['label'] ?? 'One-time';
        }

        $biodata = [
            'donor_name' => $this->donor_name,
            'donor_age' => $this->donor_age,
            'donor_email' => $this->donor_email,
            'donor_phone' => $this->donor_phone,
        ];

        // Persist (or clear) the Remember Me cookie.
        $remember->execute($biodata, $this->remember);

        // Hand the intent to the Payment page via the session.
        session()->put('donation_intent', array_merge($biodata, [
            'amount' => $amount,
            'time_range_label' => $label,
            'time_range_days' => $days,
            'is_custom_amount' => $isCustomAmount,
            'is_custom_range' => $isCustomRange,
        ]));

        return $this->redirect(route('donate.payment'), navigate: true);
    }

    public function render()
    {
        return view('livewire.public.donate');
    }
}
