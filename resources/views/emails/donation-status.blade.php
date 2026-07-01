@component('mail::message')
# {{ $heading }}

Dear {{ $donation->donor_name }},

{{ $body }}

@component('mail::panel')
**Reference:** {{ $donation->reference }}
**Amount:** ${{ number_format((float) $donation->amount, 2) }}
**Frequency:** {{ $donation->time_range_label }}
**Status:** {{ ucfirst($donation->status) }}
@endcomponent

@if ($donation->status === \App\Models\Donation::STATUS_FAILED)
@component('mail::button', ['url' => url('/donate')])
Try Again
@endcomponent
@endif

With gratitude,<br>
{{ config('app.name') }}
@endcomponent
