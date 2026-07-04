<div class="bg-white text-slate-900 min-h-screen">
    {{-- Hero strip --}}
    <section class="relative hero-mint">
        <x-public-nav active="donate" />
        <div class="max-w-5xl mx-auto px-6 lg:px-10 pt-36 pb-20 text-center">
            <h1 class="text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900">Make a Donation</h1>
            <p class="text-slate-800/80 text-lg mt-4 max-w-xl mx-auto">
                Choose how much and how long you'd like to give. Every gift makes a difference.
            </p>
        </div>
    </section>

    <form wire:submit="continueToPayment" class="max-w-5xl mx-auto px-6 lg:px-10 -mt-10 pb-24 relative z-10">
        <div class="card-soft p-8 lg:p-12 space-y-12">

            {{-- ============ AMOUNT ============ --}}
            <div>
                <div class="flex items-baseline justify-between mb-5">
                    <h2 class="text-2xl font-bold">1. Choose an amount</h2>
                    <span class="text-sm text-slate-400">IDR</span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @foreach ($amountOptions as $opt)
                        <button type="button" wire:click="selectAmount('{{ $opt['value'] }}')"
                            class="donate-tile {{ $amountChoice === $opt['value'] ? 'is-active' : '' }}">
                            <span class="tile-title">Rp {{ number_format((float) $opt['value'], 0, ',', '.') }}</span>
                            <span class="tile-sub">one gift</span>
                        </button>
                    @endforeach

                    {{-- Custom amount tile --}}
                    <button type="button" wire:click="selectAmount('custom')"
                        class="donate-tile {{ $amountChoice === 'custom' ? 'is-active' : '' }}">
                        <span class="tile-title">Custom</span>
                        <span class="tile-sub">your amount</span>
                    </button>
                </div>

                @if ($amountChoice === 'custom')
                    <div class="mt-5 max-w-xs">
                        <label class="block text-sm font-medium mb-2">Enter custom amount (Rp)</label>
                        <input type="number" min="1" step="1" wire:model="customAmount"
                            placeholder="e.g. 75"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:outline-none focus:border-mint">
                        @error('customAmount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                @endif
            </div>

            {{-- ============ TIME RANGE ============ --}}
            <div>
                <h2 class="text-2xl font-bold mb-5">2. Choose a time range</h2>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @foreach ($rangeOptions as $opt)
                        <button type="button" wire:click="selectRange('{{ $opt['id'] }}')"
                            class="donate-tile {{ $rangeChoice === $opt['id'] ? 'is-active' : '' }}">
                            <span class="tile-title">{{ $opt['label'] }}</span>
                            <span class="tile-sub">{{ $opt['value'] }} days</span>
                        </button>
                    @endforeach

                    {{-- Custom range tile --}}
                    <button type="button" wire:click="selectRange('custom')"
                        class="donate-tile {{ $rangeChoice === 'custom' ? 'is-active' : '' }}">
                        <span class="tile-title">Custom</span>
                        <span class="tile-sub">your range</span>
                    </button>
                </div>

                @if ($rangeChoice === 'custom')
                    <div class="mt-5 flex flex-wrap gap-4 items-end">
                        <div>
                            <label class="block text-sm font-medium mb-2">Duration</label>
                            <input type="number" min="1" wire:model="customRangeValue" placeholder="e.g. 14"
                                class="w-32 rounded-xl border border-slate-300 px-4 py-3 focus:outline-none focus:border-mint">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Unit</label>
                            <select wire:model="customRangeUnit"
                                class="rounded-xl border border-slate-300 px-4 py-3 focus:outline-none focus:border-mint">
                                <option value="days">Days</option>
                                <option value="months">Months</option>
                                <option value="years">Years</option>
                            </select>
                        </div>
                        @error('customRangeValue') <span class="text-red-500 text-sm w-full">{{ $message }}</span> @enderror
                    </div>
                @endif
            </div>

            {{-- ============ BIODATA (dark slate card, reference #3) ============ --}}
            <div class="form-card p-8 lg:p-10">
                <h2 class="text-2xl font-bold mb-1">3. Your details</h2>
                <p class="text-white/60 mb-6 text-sm">We'll use this to send your donation receipt.</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block mb-2 font-medium">Full Name</label>
                        <input type="text" wire:model="donor_name" placeholder="Jane Doe"
                            class="w-full rounded-xl px-4 py-3 bg-white text-slate-800 placeholder-slate-400 focus:outline-none">
                        @error('donor_name') <span class="text-red-300 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-2 font-medium">Age</label>
                        <input type="number" min="1" wire:model="donor_age" placeholder="30"
                            class="w-full rounded-xl px-4 py-3 bg-white text-slate-800 placeholder-slate-400 focus:outline-none">
                        @error('donor_age') <span class="text-red-300 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-2 font-medium">Email</label>
                        <input type="email" wire:model="donor_email" placeholder="jane@example.com"
                            class="w-full rounded-xl px-4 py-3 bg-white text-slate-800 placeholder-slate-400 focus:outline-none">
                        @error('donor_email') <span class="text-red-300 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block mb-2 font-medium">Phone Number</label>
                        <input type="text" wire:model="donor_phone" placeholder="+1 (555) 123-4567"
                            class="w-full rounded-xl px-4 py-3 bg-white text-slate-800 placeholder-slate-400 focus:outline-none">
                        @error('donor_phone') <span class="text-red-300 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>

                <label class="flex items-center gap-3 mt-6 cursor-pointer select-none">
                    <input type="checkbox" wire:model="remember" class="w-5 h-5 rounded accent-emerald-400">
                    <span class="text-white/90">Remember me on this device (saves my details for next time)</span>
                </label>
            </div>

            {{-- ============ SUBMIT ============ --}}
            <div class="flex justify-end">
                <button type="submit"
                    class="btn-pill text-white font-semibold px-10 py-4 text-lg transition"
                    style="background-color:#52b788;">
                    Continue to Payment →
                </button>
            </div>
        </div>
    </form>

    <x-public-footer />
</div>
