<div>
    <x-header title="{{ $donorName }}" subtitle="{{ $email }}" separator>
        <x-slot:actions>
            <x-button label="{{ __('Back to Donors') }}" icon="o-arrow-left" link="{{ route('admin.donors') }}"
                class="btn-ghost" />
        </x-slot:actions>
    </x-header>

    {{-- STAT CARDS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-stat title="{{ __('Total Given') }}" value="Rp {{ number_format($totalGiven, 0, ',', '.') }}"
            icon="o-banknotes" color="text-primary" />
        <x-stat title="{{ __('Times Donated') }}" value="{{ $donationsCount }}" icon="o-gift"
            color="text-secondary" />
        <x-stat title="{{ __('Confirmed Total') }}" value="Rp {{ number_format($successfulTotal, 0, ',', '.') }}"
            icon="o-check-circle" color="text-success" />
        <x-stat title="{{ __('Recurring') }}" value="{{ $subscription ? __('Active') : __('None') }}"
            icon="o-arrow-path" color="{{ $subscription ? 'text-info' : 'text-gray-400' }}" />
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- CONTACT + CAMPAIGNS --}}
        <div class="space-y-6">
            <x-card class="bg-base-100 shadow-sm" title="{{ __('Contact') }}">
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-gray-500">{{ __('Phone') }}</dt>
                        <dd>{{ $donorPhone ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('Age') }}</dt>
                        <dd>{{ $donorAge ?? '—' }}</dd>
                    </div>
                </dl>
            </x-card>

            <x-card class="bg-base-100 shadow-sm" title="{{ __('Support Periods Contributed To') }}">
                @if ($supportPeriods->isEmpty())
                    <p class="text-sm text-gray-500">{{ __('None recorded.') }}</p>
                @else
                    <div class="flex flex-wrap gap-2">
                        @foreach ($supportPeriods as $period)
                            <span class="badge badge-outline">{{ $period }}</span>
                        @endforeach
                    </div>
                @endif
            </x-card>
        </div>

        {{-- DONATION HISTORY --}}
        <x-card class="bg-base-100 shadow-sm lg:col-span-2" title="{{ __('Donation History') }}">
            <div class="overflow-x-auto">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>{{ __('Reference') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Support Period') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($donations as $donation)
                            <tr wire:key="donhist-{{ $donation->id }}">
                                <td class="font-mono text-xs">{{ $donation->reference }}</td>
                                <td class="font-bold">Rp {{ number_format((float) $donation->amount, 0, ',', '.') }}</td>
                                <td>{{ $donation->time_range_label ?? '—' }}</td>
                                <td>
                                    <span class="badge {{ $donation->statusColor() }} text-white badge-sm">
                                        {{ ucfirst($donation->status) }}
                                    </span>
                                </td>
                                <td class="text-xs text-gray-500">{{ $donation->created_at->format('d M Y') }}</td>
                                <td class="text-right">
                                    <x-button icon="o-eye" link="{{ route('admin.donations.show', $donation) }}"
                                        class="btn-ghost btn-xs" />
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</div>
