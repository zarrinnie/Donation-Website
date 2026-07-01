<div class="bg-white text-slate-900">
    {{-- ============================ HERO (reference #1) ============================ --}}
    <section class="relative hero-mint overflow-hidden">
        <x-public-nav active="home" />

        <div class="max-w-7xl mx-auto px-6 lg:px-10 pt-36 pb-24 grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
            {{-- Left copy --}}
            <div>
                <h1 class="text-6xl lg:text-7xl font-extrabold leading-[1.05] tracking-tight text-slate-900">
                    We're here<br>to help you<br>to give
                </h1>
                <div class="w-56 h-1.5 rounded-full bg-white/70 my-8"></div>
                <p class="text-lg text-slate-800/80 max-w-md mb-10">
                    Grace Community Church helps you turn generosity into real impact for
                    families and communities in need around the world.
                </p>
                <div class="flex items-center gap-6">
                    <a href="{{ route('donate') }}"
                        class="btn-pill text-white font-semibold px-9 py-4 text-lg transition"
                        style="background-color:#52b788;">
                        Donate Now
                    </a>
                    <a href="{{ route('about') }}" class="flex items-center gap-3 font-semibold text-slate-800">
                        <span class="w-12 h-12 rounded-full border-2 border-slate-800 flex items-center justify-center">▶</span>
                        Our Story
                    </a>
                </div>
            </div>

            {{-- Right image + overlapping Total Donated card --}}
            <div class="relative">
                <div class="rounded-3xl overflow-hidden shadow-2xl aspect-[4/3] bg-mint-300"
                    style="background:linear-gradient(135deg,#8fd9b6,#3f9e73);">
                    <div class="w-full h-full flex items-center justify-center text-white/90 text-7xl">🤲</div>
                </div>
                <div class="card-soft absolute -bottom-8 left-8 px-7 py-5">
                    <p class="text-sm text-slate-500">Total Donated</p>
                    <p class="text-3xl font-extrabold text-slate-900">
                        ${{ number_format($totalDonated, 2) }}
                        <span class="text-mint text-lg">▮▮▮</span>
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================ MISSION / INTRO ============================ --}}
    <section id="mission" class="max-w-7xl mx-auto px-6 lg:px-10 py-24">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <p class="uppercase tracking-[0.25em] text-mint font-semibold mb-3">Our Mission</p>
            <h2 class="text-4xl font-extrabold mb-5">Faith in action, generosity in motion</h2>
            <p class="text-slate-600 text-lg">
                Every gift to Grace Community Church goes toward feeding, sheltering, and
                uplifting people who need it most — guided by compassion and accountability.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @php
                $cards = [
                    ['icon' => '🍞', 'title' => 'Feed Families', 'text' => 'Weekly food programs for families facing hardship in our communities.'],
                    ['icon' => '🏠', 'title' => 'Shelter & Care', 'text' => 'Safe spaces and support for the homeless and those in crisis.'],
                    ['icon' => '📚', 'title' => 'Educate & Equip', 'text' => 'Scholarships and mentorship that open doors for the next generation.'],
                ];
            @endphp
            @foreach ($cards as $card)
                <div class="card-soft p-8">
                    <div class="text-4xl mb-4">{{ $card['icon'] }}</div>
                    <h3 class="text-xl font-bold mb-2">{{ $card['title'] }}</h3>
                    <p class="text-slate-600">{{ $card['text'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- Stats strip --}}
        <div class="grid grid-cols-2 md:grid-cols-3 gap-6 mt-16 text-center">
            <div class="card-soft py-8">
                <p class="text-4xl font-extrabold text-mint">${{ number_format($totalDonated, 0) }}</p>
                <p class="text-slate-500 mt-1">Raised together</p>
            </div>
            <div class="card-soft py-8">
                <p class="text-4xl font-extrabold text-mint">{{ number_format($donorCount) }}</p>
                <p class="text-slate-500 mt-1">Generous donors</p>
            </div>
            <div class="card-soft py-8 col-span-2 md:col-span-1">
                <p class="text-4xl font-extrabold text-mint">{{ number_format($donationCount) }}</p>
                <p class="text-slate-500 mt-1">Gifts received</p>
            </div>
        </div>
    </section>

    {{-- ============================ DARK SECTION (reference #2) ============================ --}}
    <section class="section-dark">
        <div class="max-w-7xl mx-auto px-6 lg:px-10 py-24 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <h2 class="text-5xl font-extrabold leading-tight">
                    <span class="text-mint">People are saying</span><br>
                    <span class="text-mint">about Grace</span> <span class="text-white/30">”</span>
                </h2>
                <a href="{{ route('donate') }}"
                    class="btn-pill inline-block mt-10 text-white font-semibold px-9 py-4 text-lg"
                    style="background-color:#52b788;">
                    Start Giving
                </a>
            </div>
            <div class="space-y-6">
                <p class="text-xl text-white/80 leading-relaxed">
                    Everything you want to give, you can give with Grace. We help your
                    generosity reach the people who need it the most.
                </p>
                <div class="border-l-4 pl-5" style="border-color:#52b788;">
                    <p class="text-lg text-white/70">
                        “I am very helped by Grace — I know my gift goes safely to people in
                        need, and they run so many programs we can support.”
                    </p>
                    <p class="mt-4 font-semibold text-white">— Zack James</p>
                </div>
            </div>
        </div>
    </section>

    <x-public-footer />
</div>
