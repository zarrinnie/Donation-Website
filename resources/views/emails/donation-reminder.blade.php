@component('mail::message')
# We'd love to see you give again

Dear {{ $donation->donor_name }},

A little while ago you supported {{ config('app.name') }} with a **{{ $donation->time_range_label }}** gift — thank you again for your generosity. That support period has now come to an end.

If you'd like to continue giving, it only takes a moment to set up your next gift.

@component('mail::panel')
**Your previous gift:** Rp {{ number_format((float) $donation->amount, 0, ',', '.') }}
**Frequency:** {{ $donation->time_range_label }}
**Reference:** {{ $donation->reference }}
@endcomponent

@component('mail::button', ['url' => url(route('donate'))])
Donate Again
@endcomponent

There's no obligation — we're simply grateful for every gift, of any size.

With gratitude,<br>
{{ config('app.name') }}
@endcomponent
