<div class="bg-white text-slate-900 min-h-screen flex flex-col">
    <section class="relative hero-mint flex-1 flex flex-col">
        <x-public-nav active="donate" />

        <div class="flex-1 flex items-center justify-center px-6 py-32">
            <div class="card-soft max-w-xl w-full p-10 lg:p-12 text-center">
                <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 text-4xl text-white"
                    style="background-color:#52b788;">✓</div>

                <h1 class="text-4xl font-extrabold mb-3">Thank you, {{ \Illuminate\Support\Str::before($donation->donor_name, ' ') }}!</h1>
                <p class="text-slate-600 mb-8">
                    @if ($donation->status === \App\Models\Donation::STATUS_SUCCESSFUL)
                        Your generosity means the world to us. Your gift has been confirmed.
                    @elseif ($donation->status === \App\Models\Donation::STATUS_FAILED)
                        We couldn't confirm your payment. Please try again, or contact us if you were charged.
                    @else
                        Your generosity means the world to us. We're confirming your payment now —
                        you'll get an email as soon as it's done.
                    @endif
                </p>

                <div class="bg-mint-50 rounded-2xl p-6 text-left space-y-3 mb-8" style="background:#ecf9f1;">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Reference</span>
                        <span class="font-bold">{{ $donation->reference }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Amount</span>
                        <span class="font-bold text-mint">Rp {{ number_format((float) $donation->amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Frequency</span>
                        <span class="font-bold">{{ $donation->time_range_label }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Status</span>
                        <span class="badge {{ $donation->statusColor() }} text-white">{{ ucfirst($donation->status) }}</span>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('home') }}" wire:navigate
                        class="btn-pill text-white font-semibold px-8 py-3" style="background-color:#52b788;">
                        Back to Home
                    </a>
                    <a href="{{ route('donate') }}" wire:navigate
                        class="btn-pill border-2 border-slate-300 font-semibold px-8 py-3 hover:border-mint transition">
                        Give Again
                    </a>
                </div>
            </div>
        </div>
    </section>
</div>
