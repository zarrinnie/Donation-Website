@component('mail::message')
# {{ $heading }}

Dear {{ $donation->donor_name }},

{{ $body }}

@component('mail::panel')
**Reference:** {{ $donation->reference }}
**Amount:** Rp {{ number_format((float) $donation->amount, 0, ',', '.') }}
**Frequency:** {{ $donation->time_range_label }}
**Status:** {{ ucfirst($donation->status) }}
@endcomponent

@if ($donation->status === \App\Models\Donation::STATUS_FAILED)
@component('mail::button', ['url' => url('/donate')])
Try Again
@endcomponent
@else
@component('mail::button', ['url' => $receiptUrl])
View Receipt
@endcomponent
@endif

With gratitude,<br>
{{ config('app.name') }}
@endcomponent
