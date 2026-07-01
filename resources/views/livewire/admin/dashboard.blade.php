<div>
    <x-header title="{{ __('Latest Donations') }}" subtitle="{{ __('Overview of giving across the church') }}"
        separator progress-indicator>
        <x-slot:actions>
            <x-button label="{{ __('Full Ledger') }}" icon="o-banknotes" link="{{ route('admin.donations') }}"
                class="btn-primary" />
        </x-slot:actions>
    </x-header>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <x-stat title="{{ __('Total Raised') }}" value="${{ number_format($totalRaised, 2) }}"
            icon="o-banknotes" color="text-primary" />
        <x-stat title="{{ __('Successful') }}" value="{{ $countSuccessful }}" icon="o-check-circle"
            color="text-success" />
        <x-stat title="{{ __('Pending') }}" value="{{ $countPending }}" icon="o-clock" color="text-warning" />
        <x-stat title="{{ __('Failed') }}" value="{{ $countFailed }}" icon="o-x-circle" color="text-error" />
    </div>

    {{-- LATEST DONATIONS TABLE --}}
    <x-card class="bg-base-100 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-lg">{{ __('Most Recent') }}</h3>
            <span class="text-sm text-gray-500">{{ __('Showing latest 10 of') }} {{ $totalCount }}</span>
        </div>
        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>{{ __('Reference') }}</th>
                        <th>{{ __('Donor') }}</th>
                        <th>{{ __('Amount') }}</th>
                        <th>{{ __('Frequency') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($donations as $donation)
                        <tr wire:key="dash-{{ $donation->id }}">
                            <td class="font-mono text-xs">{{ $donation->reference }}</td>
                            <td>
                                <div class="font-bold">{{ $donation->donor_name }}</div>
                                <div class="text-xs text-gray-500">{{ $donation->donor_email }}</div>
                            </td>
                            <td class="font-bold">${{ number_format((float) $donation->amount, 2) }}</td>
                            <td>{{ $donation->time_range_label }}</td>
                            <td>
                                <span class="badge {{ $donation->statusColor() }} text-white badge-sm">
                                    {{ ucfirst($donation->status) }}
                                </span>
                            </td>
                            <td class="text-xs text-gray-500">{{ $donation->created_at->format('d M Y, H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-gray-500">{{ __('No donations yet.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-card>
</div>
