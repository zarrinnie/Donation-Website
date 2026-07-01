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
                        ${{ number_format((float) ($intent['amount'] ?? 0), 2) }}
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Mock gateway --}}
        <div class="lg:col-span-3 form-card p-8 lg:p-10">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold">Payment Details</h2>
                <span class="text-xs px-3 py-1 rounded-full bg-white/10 text-white/70">🔒 Mock Gateway</span>
            </div>

            <form wire:submit="pay" class="space-y-5">
                <div>
                    <label class="block mb-2 font-medium">Name on Card</label>
                    <input type="text" wire:model="card_name" placeholder="Jane Doe"
                        class="w-full rounded-xl px-4 py-3 bg-white text-slate-800 placeholder-slate-400 focus:outline-none">
                    @error('card_name') <span class="text-red-300 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block mb-2 font-medium">Card Number</label>
                    <input type="text" wire:model="card_number" placeholder="4242 4242 4242 4242"
                        class="w-full rounded-xl px-4 py-3 bg-white text-slate-800 placeholder-slate-400 focus:outline-none">
                    @error('card_number') <span class="text-red-300 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block mb-2 font-medium">Expiry</label>
                        <input type="text" wire:model="card_expiry" placeholder="MM/YY"
                            class="w-full rounded-xl px-4 py-3 bg-white text-slate-800 placeholder-slate-400 focus:outline-none">
                        @error('card_expiry') <span class="text-red-300 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-2 font-medium">CVC</label>
                        <input type="text" wire:model="card_cvc" placeholder="123"
                            class="w-full rounded-xl px-4 py-3 bg-white text-slate-800 placeholder-slate-400 focus:outline-none">
                        @error('card_cvc') <span class="text-red-300 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <button type="submit"
                    class="w-full rounded-xl py-4 font-semibold text-white text-lg transition mt-2"
                    style="background-color:#52b788;" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="pay">Donate ${{ number_format((float) ($intent['amount'] ?? 0), 2) }}</span>
                    <span wire:loading wire:target="pay">Processing…</span>
                </button>

                <a href="{{ route('donate') }}" wire:navigate
                    class="block text-center text-white/60 hover:text-white text-sm">← Back to donation</a>
            </form>
        </div>
    </div>

    <x-public-footer />
</div>
