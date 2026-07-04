<div class="bg-white text-slate-900 min-h-screen">
    <section class="relative hero-mint">
        <x-public-nav active="donate" />
        <div class="max-w-5xl mx-auto px-6 lg:px-10 pt-36 pb-20 text-center">
            <h1 class="text-5xl font-extrabold tracking-tight text-slate-900">Checkout</h1>
            <p class="text-slate-800/80 text-lg mt-4">Review your gift and complete your secure payment.</p>
        </div>
    </section>

    <div class="max-w-5xl mx-auto px-6 lg:px-10 -mt-10 pb-24 relative z-10 grid grid-cols-1 lg:grid-cols-5 gap-8">

        {{-- Summary --}}
        <div class="lg:col-span-2 card-soft p-8 h-fit">
            <h2 class="text-xl font-bold mb-6">Donation Summary</h2>
            <dl class="space-y-4 text-sm">
                <div class="flex justify-between">
                    <dt class="text-slate-500">Donor</dt>
                    <dd class="font-semibold text-right">{{ $intent['donor_name'] ?? '' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Email</dt>
                    <dd class="font-semibold text-right">{{ $intent['donor_email'] ?? '' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Phone</dt>
                    <dd class="font-semibold text-right">{{ $intent['donor_phone'] ?? '' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Frequency</dt>
                    <dd class="font-semibold text-right">{{ $intent['time_range_label'] ?? '' }}</dd>
                </div>
                <div class="border-t border-slate-200 pt-4 flex justify-between items-center">
                    <dt class="text-slate-500">Total</dt>
                    <dd class="text-3xl font-extrabold text-mint">
                        Rp {{ number_format((float) ($intent['amount'] ?? 0), 0, ',', '.') }}
                    </dd>
                </div>
            </dl>
        </div>

        {{-- DOKU checkout --}}
        <div class="lg:col-span-3 form-card p-8 lg:p-10 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold">Secure Payment</h2>
                    <span class="text-xs px-3 py-1 rounded-full bg-white/10 text-white/70">🔒 Powered by DOKU</span>
                </div>
                <p class="text-white/70 text-sm mb-8">
                    You'll be redirected to DOKU's secure checkout page to complete your payment.
                    Nothing is charged until you finish there.
                </p>
            </div>

            <div class="space-y-5">
                <x-button label="Pay Rp {{ number_format((float) ($intent['amount'] ?? 0), 0, ',', '.') }}"
                    wire:click="pay" spinner="pay"
                    class="w-full rounded-xl py-4 font-semibold text-white text-lg btn-primary" />

                <a href="{{ route('donate') }}" wire:navigate
                    class="block text-center text-white/60 hover:text-white text-sm">← Back to donation</a>
            </div>
        </div>
    </div>

    <x-public-footer />
</div>
