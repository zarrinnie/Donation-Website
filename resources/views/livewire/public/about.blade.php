<div class="bg-white text-slate-900">
    {{-- Hero --}}
    <section class="relative hero-mint">
        <x-public-nav active="about" />
        <div class="max-w-7xl mx-auto px-6 lg:px-10 pt-40 pb-24">
            <p class="uppercase tracking-[0.25em] text-slate-700 font-semibold mb-3">About Us</p>
            <h1 class="text-6xl font-extrabold tracking-tight text-slate-900 max-w-3xl">
                A community built on faith, hope & generosity
            </h1>
        </div>
    </section>

    {{-- Mission --}}
    <section class="max-w-7xl mx-auto px-6 lg:px-10 py-24 grid grid-cols-1 lg:grid-cols-2 gap-14 items-center">
        <div class="rounded-3xl overflow-hidden aspect-[4/3] shadow-2xl"
            style="background:linear-gradient(135deg,#8fd9b6,#2f7d5b);">
            <div class="w-full h-full flex items-center justify-center text-white text-8xl">⛪</div>
        </div>
        <div>
            <p class="uppercase tracking-[0.25em] text-mint font-semibold mb-3">Our Mission</p>
            <h2 class="text-4xl font-extrabold mb-5">Loving God, serving people</h2>
            <p class="text-slate-600 text-lg mb-4">
                Grace Community Church exists to share hope and practical help with our
                neighbours near and far. For over 30 years we've channelled the generosity
                of our congregation into food, shelter, education, and disaster relief.
            </p>
            <p class="text-slate-600 text-lg">
                We believe giving should be transparent and joyful. Every donation is
                tracked, accounted for, and directed to the programs that need it most.
            </p>
        </div>
    </section>

    {{-- Values --}}
    <section class="bg-mint-50" style="background:#ecf9f1;">
        <div class="max-w-7xl mx-auto px-6 lg:px-10 py-20 grid grid-cols-1 md:grid-cols-3 gap-8">
            @php
                $values = [
                    ['t' => 'Compassion', 'd' => 'We lead with empathy, meeting people where they are.'],
                    ['t' => 'Integrity', 'd' => 'Every gift is handled with full transparency and care.'],
                    ['t' => 'Community', 'd' => 'We grow stronger when we serve shoulder to shoulder.'],
                ];
            @endphp
            @foreach ($values as $v)
                <div class="card-soft p-8">
                    <h3 class="text-xl font-bold text-mint mb-2">{{ $v['t'] }}</h3>
                    <p class="text-slate-600">{{ $v['d'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Team --}}
    <section class="max-w-7xl mx-auto px-6 lg:px-10 py-24">
        <div class="text-center max-w-2xl mx-auto mb-14">
            <p class="uppercase tracking-[0.25em] text-mint font-semibold mb-3">Our Team</p>
            <h2 class="text-4xl font-extrabold">The people behind the mission</h2>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            @php
                $team = [
                    ['n' => 'Pastor David Lee', 'r' => 'Lead Pastor'],
                    ['n' => 'Sarah Mensah', 'r' => 'Outreach Director'],
                    ['n' => 'Michael Tan', 'r' => 'Finance & Stewardship'],
                    ['n' => 'Grace Owino', 'r' => 'Volunteer Coordinator'],
                ];
            @endphp
            @foreach ($team as $member)
                <div class="text-center">
                    <div class="w-full aspect-square rounded-2xl mb-4"
                        style="background:linear-gradient(135deg,#8fd9b6,#3f9e73);"></div>
                    <h3 class="font-bold">{{ $member['n'] }}</h3>
                    <p class="text-slate-500 text-sm">{{ $member['r'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Dark CTA --}}
    <section class="section-dark">
        <div class="max-w-7xl mx-auto px-6 lg:px-10 py-20 text-center">
            <h2 class="text-4xl font-extrabold mb-4">Ready to make a <span class="text-mint">difference</span>?</h2>
            <p class="text-white/70 mb-8 max-w-xl mx-auto">Join thousands of donors turning compassion into action.</p>
            <a href="{{ route('donate') }}" class="btn-pill inline-block text-white font-semibold px-9 py-4 text-lg"
                style="background-color:#52b788;">Donate Today</a>
        </div>
    </section>

    <x-public-footer />
</div>
