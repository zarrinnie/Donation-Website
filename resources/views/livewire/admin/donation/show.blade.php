<div>
    <x-header title="{{ __('Donation') }} {{ $donation->reference }}"
        subtitle="{{ __('Received') }} {{ $donation->created_at->format('d M Y, H:i') }}" separator>
        <x-slot:actions>
            <x-button label="{{ __('Back to Ledger') }}" icon="o-arrow-left" link="{{ route('admin.donations') }}"
                class="btn-ghost" />
        </x-slot:actions>
    </x-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- DONATION SUMMARY --}}
        <x-card class="bg-base-100 shadow-sm lg:col-span-2">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <div class="text-sm text-gray-500">{{ __('Amount') }}</div>
                    <div class="text-3xl font-bold text-primary">
                        Rp {{ number_format((float) $donation->amount, 0, ',', '.') }}
                    </div>
                </div>
                <div class="text-right space-y-1">
                    <span class="badge {{ $donation->statusColor() }} text-white">
                        {{ ucfirst($donation->status) }}
                    </span>
                    @if ($donation->isNewForAdmin())
                        <span class="badge badge-success badge-xs text-white block">{{ __('NEW') }}</span>
                    @endif
                </div>
            </div>

            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                <div>
                    <dt class="text-gray-500">{{ __('Reference') }}</dt>
                    <dd class="font-mono">{{ $donation->reference }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">{{ __('Support Period') }}</dt>
                    <dd>
                        {{ $donation->time_range_label ?? '—' }}
                        @if ($donation->subscription_id)
                            <span class="badge badge-info badge-xs text-white ml-1">{{ __('Recurring') }}</span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500">{{ __('Status Source') }}</dt>
                    <dd>{{ $donation->status_source ? str_replace('_', ' ', ucfirst($donation->status_source)) : '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">{{ __('Payment Method') }}</dt>
                    <dd>{{ $donation->payment_method ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">{{ __('Paid At') }}</dt>
                    <dd>{{ $donation->paid_at ? $donation->paid_at->format('d M Y, H:i') : '—' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">{{ __('DOKU Invoice') }}</dt>
                    <dd class="font-mono text-xs">{{ $donation->doku_invoice_number ?? '—' }}</dd>
                </div>
            </dl>

            {{-- EMAIL STATE --}}
            <div class="mt-6 pt-4 border-t border-base-200">
                <div class="flex items-center gap-2 text-sm">
                    @if ($donation->status_email_sent_at)
                        <x-icon name="o-check-circle" class="w-5 h-5 text-success" />
                        <span>{{ __('Email sent to donor') }}
                            {{ $donation->status_email_sent_at->format('d M Y, H:i') }}</span>
                    @else
                        <x-icon name="o-x-circle" class="w-5 h-5 text-gray-400" />
                        <span class="text-gray-500">{{ __('No status email sent yet') }}</span>
                    @endif
                </div>
            </div>
        </x-card>

        {{-- DONOR + ACTIONS --}}
        <div class="space-y-6">
            <x-card class="bg-base-100 shadow-sm" title="{{ __('Donor') }}">
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-gray-500">{{ __('Name') }}</dt>
                        <dd class="font-bold">{{ $donation->donor_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('Email') }}</dt>
                        <dd>
                            <a href="{{ route('admin.donors.show', ['email' => $donation->donor_email]) }}"
                                class="link link-primary">{{ $donation->donor_email }}</a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('Phone') }}</dt>
                        <dd>{{ $donation->donor_phone ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">{{ __('Age') }}</dt>
                        <dd>{{ $donation->donor_age ?? '—' }}</dd>
                    </div>
                </dl>
            </x-card>

            <x-card class="bg-base-100 shadow-sm" title="{{ __('Actions') }}">
                <div class="space-y-3">
                    <div>
                        <label class="text-xs text-gray-500">{{ __('Change Status') }}</label>
                        <select class="select select-sm select-bordered w-full mt-1"
                            wire:change="setStatus($event.target.value)">
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}" @selected($donation->status === $status)>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <x-button label="{{ __('Resend Status Email') }}" icon="o-envelope" wire:click="resendEmail"
                        spinner="resendEmail" class="btn-outline btn-primary btn-sm w-full" />
                </div>
            </x-card>
        </div>
    </div>
</div>
