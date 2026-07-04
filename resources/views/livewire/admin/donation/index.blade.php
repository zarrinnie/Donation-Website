<div>
    <x-header title="{{ __('Donations Ledger') }}" subtitle="{{ __('Manage status and notify donors') }}"
        separator progress-indicator />

    {{-- FILTER BAR --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 items-end">
        <x-input placeholder="{{ __('Search name, email or ref') }}..." wire:model.live.debounce="search"
            icon="o-magnifying-glass" />

        <x-select wire:model.live="filterStatus" :options="[
            ['id' => '', 'name' => __('All Statuses')],
            ['id' => 'successful', 'name' => __('Successful')],
            ['id' => 'pending', 'name' => __('Pending')],
            ['id' => 'failed', 'name' => __('Failed')],
        ]" icon="o-flag" />

        <x-select wire:model.live="sortBy" :options="[
            ['id' => 'latest', 'name' => __('Newest First')],
            ['id' => 'oldest', 'name' => __('Oldest First')],
            ['id' => 'amount_high', 'name' => __('Amount (High-Low)')],
            ['id' => 'amount_low', 'name' => __('Amount (Low-High)')],
        ]" icon="o-arrows-up-down" />

        <x-button label="{{ __('Clear') }}" wire:click="clearFilters" icon="o-x-mark" class="btn-ghost text-gray-500" />
    </div>

    <x-card class="bg-base-100 shadow-sm">
        <div class="overflow-x-auto">
            <table class="table table-zebra align-middle">
                <thead>
                    <tr>
                        <th>{{ __('Reference') }}</th>
                        <th>{{ __('Donor') }}</th>
                        <th>{{ __('Amount') }}</th>
                        <th>{{ __('Frequency') }}</th>
                        <th>{{ __('Date') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-right">{{ __('Notify') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($donations as $donation)
                        <tr wire:key="don-{{ $donation->id }}">
                            <td class="font-mono text-xs">{{ $donation->reference }}</td>
                            <td>
                                <div class="font-bold">{{ $donation->donor_name }}</div>
                                <div class="text-xs text-gray-500">{{ $donation->donor_email }}</div>
                            </td>
                            <td class="font-bold">Rp {{ number_format((float) $donation->amount, 0, ',', '.') }}</td>
                            <td>
                                {{ $donation->time_range_label }}
                                @if ($donation->subscription_id)
                                    <span class="badge badge-info badge-xs text-white ml-1">{{ __('Recurring') }}</span>
                                @endif
                            </td>
                            <td class="text-xs text-gray-500">{{ $donation->created_at->format('d M Y') }}</td>

                            {{-- STATUS SELECT --}}
                            <td>
                                <select
                                    class="select select-sm select-bordered w-36"
                                    wire:change="setStatus({{ $donation->id }}, $event.target.value)">
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status }}" @selected($donation->status === $status)>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="mt-1">
                                    <span class="badge {{ $donation->statusColor() }} text-white badge-xs">
                                        {{ ucfirst($donation->status) }}
                                    </span>
                                </div>
                            </td>

                            {{-- SEND EMAIL --}}
                            <td class="text-right">
                                <x-button label="{{ __('Send Email') }}" icon="o-envelope"
                                    wire:click="sendEmail({{ $donation->id }})"
                                    spinner="sendEmail({{ $donation->id }})"
                                    class="btn-sm btn-outline btn-primary" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-gray-500">
                                {{ __('No donations found.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $donations->links() }}</div>
    </x-card>
</div>
