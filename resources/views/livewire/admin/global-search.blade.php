<div class="space-y-8 pb-20">

    {{-- Header --}}
    <div>
        <h1 class="text-3xl font-black mb-2">Global Search</h1>
        <div class="flex items-center gap-2 text-gray-500">
            <x-icon name="o-magnifying-glass" class="w-5 h-5" />
            <span>Results for: <span class="font-bold text-base-content">"{{ $search }}"</span></span>
        </div>
    </div>

    {{-- Main Search Input (In-Page) --}}
    <div class="bg-base-100 p-4 rounded-xl border border-base-200 shadow-sm">
        <x-input icon="o-magnifying-glass" placeholder="Search donors, donations, references, or admin users..."
            wire:model.live.debounce.300ms="search" class="w-full" autofocus />
    </div>

    @if ($search == '')
        <div class="text-center py-20 opacity-50">
            <x-icon name="o-magnifying-glass" class="w-16 h-16 mx-auto mb-4 opacity-20" />
            <p>Start typing to search across the system.</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-8">

            {{-- ========================================================= --}}
            {{-- 1. DONATIONS SECTION                                      --}}
            {{-- ========================================================= --}}
            <div class="space-y-4">
                <div class="flex justify-between items-center border-b pb-2">
                    <h2 class="font-bold flex items-center gap-2">
                        <x-icon name="o-banknotes" class="w-5 h-5 text-primary" /> Donations ({{ $donations->count() }})
                    </h2>
                </div>

                @forelse($donations as $donation)
                    <div
                        class="flex items-center justify-between p-3 bg-base-100 rounded-lg border border-base-200 hover:border-primary transition group">
                        <div class="flex items-center gap-3">
                            <x-icon name="o-user-circle" class="w-10 h-10 opacity-40" />
                            <div>
                                <div class="font-bold text-sm">{{ $donation->donor_name }}</div>
                                <div class="text-xs opacity-60">{{ $donation->donor_email }}</div>
                                <div class="text-xs opacity-60 font-mono mt-0.5">
                                    {{ $donation->reference }} · Rp {{ number_format((float) $donation->amount, 0, ',', '.') }}
                                    <span class="badge badge-xs {{ $donation->statusColor() }} align-middle">{{ $donation->status }}</span>
                                </div>
                            </div>
                        </div>
                        {{-- BUTTON DETAIL --}}
                        <x-button icon="o-eye" class="btn-xs btn-square btn-ghost text-primary"
                            link="{{ route('admin.donations.show', $donation) }}" tooltip="View donation" />
                    </div>
                @empty
                    <div class="text-xs opacity-50 italic py-2">No donations found.</div>
                @endforelse
            </div>

            {{-- ========================================================= --}}
            {{-- 2. DONORS SECTION                                         --}}
            {{-- ========================================================= --}}
            <div class="space-y-4">
                <div class="flex justify-between items-center border-b pb-2">
                    <h2 class="font-bold flex items-center gap-2">
                        <x-icon name="o-user-group" class="w-5 h-5 text-primary" /> Donors ({{ $donors->count() }})
                    </h2>
                </div>

                @forelse($donors as $donor)
                    <div
                        class="flex items-center justify-between p-3 bg-base-100 rounded-lg border border-base-200 hover:border-primary transition group">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold"
                                style="background-color:#52b788;">
                                {{ strtoupper(substr($donor->donor_name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-bold text-sm">{{ $donor->donor_name }}</div>
                                <div class="text-xs opacity-60">{{ $donor->donor_email }}</div>
                                <div class="text-xs opacity-60 mt-0.5">
                                    {{ $donor->donations_count }} gifts · Rp
                                    {{ number_format((float) $donor->total_amount, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>
                        {{-- BUTTON DETAIL --}}
                        <x-button icon="o-eye" class="btn-xs btn-square btn-ghost text-primary"
                            link="{{ route('admin.donors.show', ['email' => $donor->donor_email]) }}"
                            tooltip="View donor" />
                    </div>
                @empty
                    <div class="text-xs opacity-50 italic py-2">No donors found.</div>
                @endforelse
            </div>

            {{-- ========================================================= --}}
            {{-- 3. USERS SECTION                                          --}}
            {{-- ========================================================= --}}
            <div class="space-y-4">
                <div class="flex justify-between items-center border-b pb-2">
                    <h2 class="font-bold flex items-center gap-2">
                        <x-icon name="o-users" class="w-5 h-5 text-primary" /> Users ({{ $users->count() }})
                    </h2>
                    {{-- Filter Role --}}
                    <select wire:model.live="filterUserRole" class="select select-xs select-bordered">
                        <option value="">All Roles</option>
                        <option value="super_admin">Super Admin</option>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                </div>

                @forelse($users as $user)
                    <div
                        class="flex items-center justify-between p-3 bg-base-100 rounded-lg border border-base-200 hover:border-primary transition group">
                        <div class="flex items-center gap-3">
                            <x-avatar :image="$user->profile_photo ? asset('storage/' . $user->profile_photo) : null" class="w-10 h-10" />
                            <div>
                                <div class="font-bold text-sm">{{ $user->name }}</div>
                                <div class="text-xs opacity-60 uppercase">{{ $user->role }}</div>
                            </div>
                        </div>
                        {{-- BUTTON DETAIL --}}
                        <x-button icon="o-eye" class="btn-xs btn-square btn-ghost text-primary"
                            link="{{ route('admin.users') }}" tooltip="Manage users" />
                    </div>
                @empty
                    <div class="text-xs opacity-50 italic py-2">No users found.</div>
                @endforelse
            </div>

        </div>
    @endif
</div>
