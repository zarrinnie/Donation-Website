@component('mail::message')
# Time for your monthly gift

Dear {{ $subscription->donor_name }},

Thank you for being a monthly supporter of {{ config('app.name') }} — your generosity makes a real difference. It's time for this month's gift.

@component('mail::panel')
**Suggested amount:** Rp {{ number_format((float) $subscription->amount, 0, ',', '.') }}
@endcomponent

You can adjust the amount before paying, or continue with the amount above — nothing is charged until you confirm.

@component('mail::button', ['url' => $reviewUrl])
Review & Pay
@endcomponent

With gratitude,<br>
{{ config('app.name') }}
@endcomponent
