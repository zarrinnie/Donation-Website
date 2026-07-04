<div>
    <x-header title="{{ __('Donation Settings') }}"
        subtitle="{{ __('Manage the preset amounts and time ranges shown on the public donation page') }}"
        separator progress-indicator>
        <x-slot:actions>
            <x-button label="{{ __('Add Preset') }}" icon="o-plus" class="btn-primary" wire:click="create" />
        </x-slot:actions>
    </x-header>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- PRESET AMOUNTS --}}
        <x-card title="{{ __('Preset Amounts') }}" class="bg-base-100 shadow-sm">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('Label') }}</th>
                        <th>{{ __('Amount') }}</th>
                        <th>{{ __('Active') }}</th>
                        <th class="text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($amounts as $row)
                        <tr wire:key="amt-{{ $row->id }}">
                            <td>{{ $row->sort_order }}</td>
                            <td class="font-bold">{{ $row->label }}</td>
                            <td>Rp {{ number_format((float) $row->value, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge {{ $row->is_active ? 'badge-success text-white' : 'badge-ghost' }} badge-sm">
                                    {{ $row->is_active ? __('Yes') : __('No') }}
                                </span>
                            </td>
                            <td class="text-right">
                                <x-button icon="o-pencil-square" wire:click="edit({{ $row->id }})"
                                    class="btn-sm btn-ghost text-blue-500" />
                                <x-button icon="o-trash" wire:click="confirmDelete({{ $row->id }})"
                                    class="btn-sm btn-ghost text-red-500" />
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-6 text-gray-500">{{ __('No preset amounts.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </x-card>

        {{-- PRESET TIME RANGES --}}
        <x-card title="{{ __('Preset Time Ranges') }}" class="bg-base-100 shadow-sm">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ __('Label') }}</th>
                        <th>{{ __('Days') }}</th>
                        <th>{{ __('Active') }}</th>
                        <th class="text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ranges as $row)
                        <tr wire:key="rng-{{ $row->id }}">
                            <td>{{ $row->sort_order }}</td>
                            <td class="font-bold">{{ $row->label }}</td>
                            <td>{{ $row->value }}</td>
                            <td>
                                <span class="badge {{ $row->is_active ? 'badge-success text-white' : 'badge-ghost' }} badge-sm">
                                    {{ $row->is_active ? __('Yes') : __('No') }}
                                </span>
                            </td>
                            <td class="text-right">
                                <x-button icon="o-pencil-square" wire:click="edit({{ $row->id }})"
                                    class="btn-sm btn-ghost text-blue-500" />
                                <x-button icon="o-trash" wire:click="confirmDelete({{ $row->id }})"
                                    class="btn-sm btn-ghost text-red-500" />
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-6 text-gray-500">{{ __('No preset time ranges.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </x-card>
    </div>

    {{-- MODAL FORM --}}
    <x-modal wire:model="modalOpen" :title="$editingId ? __('Edit Preset') : __('Add Preset')" separator>
        <x-form wire:submit="save">
            <x-select label="{{ __('Type') }}" wire:model.live="type" :options="[
                ['id' => 'amount', 'name' => __('Amount')],
                ['id' => 'time_range', 'name' => __('Time Range')],
            ]" icon="o-tag" />

            <x-input label="{{ __('Label') }}" wire:model="label" icon="o-pencil"
                hint="{{ __('e.g. Rp 50,000 or 1 Month') }}" />

            <x-input label="{{ $type === 'amount' ? __('Amount (IDR)') : __('Number of days') }}"
                wire:model="value" type="number" icon="o-calculator" />

            <div class="grid grid-cols-2 gap-4 items-center">
                <x-input label="{{ __('Sort Order') }}" wire:model="sort_order" type="number" icon="o-bars-3" />
                <x-checkbox label="{{ __('Active') }}" wire:model="is_active" class="mt-6" />
            </div>

            <x-slot:actions>
                <x-button label="{{ __('Cancel') }}" @click="$wire.modalOpen = false" />
                <x-button label="{{ __('Save') }}" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-modal>

    {{-- DELETE CONFIRM --}}
    <x-modal-confirm wire:model="deleteModalOpen" title="{{ __('Delete Preset?') }}"
        text="{{ __('This option will no longer appear on the donation page.') }}"
        confirm-text="{{ __('Yes, Delete') }}" method="delete" />
</div>
