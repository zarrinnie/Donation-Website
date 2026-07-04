<div class="bg-white text-slate-900 min-h-screen">
    <section class="relative hero-mint">
        <x-public-nav active="donate" />
        <div class="max-w-5xl mx-auto px-6 lg:px-10 pt-36 pb-20 text-center">
            <h1 class="text-5xl font-extrabold tracking-tight text-slate-900">Renew Your Gift</h1>
            <p class="text-slate-800/80 text-lg mt-4">Thank you for supporting us every month — review the amount below.</p>
        </div>
    </section>

    <div class="max-w-5xl mx-auto px-6 lg:px-10 -mt-10 pb-24 relative z-10 grid grid-cols-1 lg:grid-cols-5 gap-8">

        {{-- Summary --}}
        <div class="lg:col-span-2 card-soft p-8 h-fit">
            <h2 class="text-xl font-bold mb-6">Your Recurring Plan</h2>
            <dl class="space-y-4 text-sm">
                <div class="flex justify-between">
                    <dt class="text-slate-500">Donor</dt>
                    <dd class="font-semibold text-right">{{ $subscription->donor_name }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Email</dt>
                    <dd class="font-semibold text-right">{{ $subscription->donor_email }}</dd>
                </div>
                <div class="border-t border-slate-200 pt-4 flex justify-between items-center">
                    <dt class="text-slate-500">Previous gift</dt>
                    <dd class="text-3xl font-extrabold text-mint">
                        Rp {{ number_format((float) $subscription->amount, 0, ',', '.') }}
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Adjustable amount + pay --}}
        <div class="lg:col-span-3 form-card p-8 lg:p-10">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold">Confirm This Month's Gift</h2>
                <span class="text-xs px-3 py-1 rounded-full bg-white/10 text-white/70">🔒 Powered by DOKU</span>
            </div>

            <div class="space-y-5">
                <x-input label="Amount (Rp)" wire:model="amount" type="number" icon="o-banknotes" />

                <x-button label="Pay Rp {{ number_format($amount, 0, ',', '.') }}"
                    wire:click="payAgain" spinner="payAgain"
                    class="w-full rounded-xl py-4 font-semibold text-white text-lg btn-primary" />

                <a href="{{ route('home') }}" wire:navigate
                    class="block text-center text-white/60 hover:text-white text-sm">← Not this month</a>
            </div>
        </div>
    </div>

    <x-public-footer />
</div>
