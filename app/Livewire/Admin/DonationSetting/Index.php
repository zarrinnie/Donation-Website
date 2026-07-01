<?php

namespace App\Livewire\Admin\DonationSetting;

use App\Actions\DonationSetting\CreateDonationSettingAction;
use App\Actions\DonationSetting\DeleteDonationSettingAction;
use App\Actions\DonationSetting\UpdateDonationSettingAction;
use App\DTOs\Donation\DonationSettingData;
use App\Models\DonationSetting;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Mary\Traits\Toast;

#[Layout('layouts.app')]
#[Title('Donation Settings')]
class Index extends Component
{
    use Toast;

    public bool $modalOpen = false;

    public bool $deleteModalOpen = false;

    public ?int $editingId = null;

    public ?int $toDeleteId = null;

    // Form fields
    public string $type = DonationSetting::TYPE_AMOUNT;

    public string $label = '';

    public string $value = '';

    public bool $is_active = true;

    public int $sort_order = 0;

    protected function rules(): array
    {
        return [
            'type' => 'required|in:amount,time_range',
            'label' => 'required|string|max:60',
            'value' => 'required|numeric|min:0',
            'is_active' => 'boolean',
            'sort_order' => 'integer|min:0',
        ];
    }

    public function create(): void
    {
        $this->reset(['editingId', 'label', 'value', 'sort_order']);
        $this->type = DonationSetting::TYPE_AMOUNT;
        $this->is_active = true;
        $this->modalOpen = true;
    }

    public function edit(DonationSetting $setting): void
    {
        $this->editingId = $setting->id;
        $this->type = $setting->type;
        $this->label = $setting->label;
        $this->value = $setting->value;
        $this->is_active = $setting->is_active;
        $this->sort_order = $setting->sort_order;
        $this->modalOpen = true;
    }

    public function save(CreateDonationSettingAction $create, UpdateDonationSettingAction $update): void
    {
        $this->validate();

        $data = new DonationSettingData(
            type: $this->type,
            label: $this->label,
            value: $this->value,
            is_active: $this->is_active,
            sort_order: $this->sort_order,
        );

        if ($this->editingId) {
            $update->execute(DonationSetting::findOrFail($this->editingId), $data);
            $this->success('Preset updated.');
        } else {
            $create->execute($data);
            $this->success('Preset added.');
        }

        $this->modalOpen = false;
    }

    public function confirmDelete(int $id): void
    {
        $this->toDeleteId = $id;
        $this->deleteModalOpen = true;
    }

    public function delete(DeleteDonationSettingAction $action): void
    {
        if ($this->toDeleteId) {
            $action->execute(DonationSetting::findOrFail($this->toDeleteId));
            $this->success('Preset deleted.');
        }
        $this->deleteModalOpen = false;
    }

    public function render()
    {
        return view('livewire.admin.donation-setting.index', [
            'amounts' => DonationSetting::amounts()->ordered()->get(),
            'ranges' => DonationSetting::timeRanges()->ordered()->get(),
        ]);
    }
}
