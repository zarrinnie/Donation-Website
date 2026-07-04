<div>
    <x-header title="{{ __('Donor Directory') }}"
        subtitle="{{ __('Every unique person who has donated, with their biodata') }}" separator progress-indicator />

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 items-end">
        <x-input placeholder="{{ __('Search name or email') }}..." wire:model.live.debounce="search"
            icon="o-magnifying-glass" class="md:col-span-2" />
        <x-select wire:model.live="sortBy" :options="[
            ['id' => 'recent', 'name' => __('Most Recent')],
            ['id' => 'top', 'name' => __('Top Donors')],
            ['id' => 'name', 'name' => __('Name (A-Z)')],
        ]" icon="o-arrows-up-down" />
    </div>

    <x-card class="bg-base-100 shadow-sm">
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>{{ __('Name') }}</th>
                        <th>{{ __('Age') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Phone') }}</th>
                        <th>{{ __('Gifts') }}</th>
                        <th>{{ __('Total Given') }}</th>
                        <th>{{ __('Last Donation') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($donors as $donor)
                        <tr wire:key="donor-{{ $donor->donor_email }}">
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold"
                                        style="background-color:#52b788;">
                                        {{ strtoupper(substr($donor->donor_name, 0, 1)) }}
                                    </div>
                                    <span class="font-bold">{{ $donor->donor_name }}</span>
                                </div>
                            </td>
                            <td>{{ $donor->donor_age ?? '—' }}</td>
                            <td class="text-gray-500">{{ $donor->donor_email }}</td>
                            <td class="text-gray-500">{{ $donor->donor_phone ?? '—' }}</td>
                            <td><span class="badge badge-ghost">{{ $donor->donations_count }}</span></td>
                            <td class="font-bold text-primary">Rp {{ number_format((float) $donor->total_amount, 0, ',', '.') }}</td>
                            <td class="text-xs text-gray-500">
                                {{ \Illuminate\Support\Carbon::parse($donor->last_donation)->format('d M Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-gray-500">{{ __('No donors yet.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $donors->links() }}</div>
    </x-card>
</div>
