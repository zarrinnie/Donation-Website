{{-- Footer styled after reference #3: teal gradient + dark "Have A Question" card --}}
<footer class="footer-teal text-white">
    <div class="max-w-7xl mx-auto px-6 lg:px-10 py-16 grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">

        {{-- Left: menu + locations + socials --}}
        <div class="grid grid-cols-2 gap-10 pt-6">
            <div>
                <h4 class="uppercase tracking-[0.2em] text-sm text-white/70 mb-5">Menu</h4>
                <ul class="space-y-3 text-lg">
                    <li><a href="{{ route('about') }}" class="hover:text-mint-300 transition">About</a></li>
                    <li><a href="{{ route('donate') }}" class="hover:text-mint-300 transition">Donate</a></li>
                    <li><a href="{{ route('home') }}" class="hover:text-mint-300 transition">Home</a></li>
                    <li><a href="{{ route('home') }}#mission" class="hover:text-mint-300 transition">Mission</a></li>
                </ul>
            </div>
            <div>
                <h4 class="uppercase tracking-[0.2em] text-sm text-white/70 mb-5">Locations</h4>
                <ul class="space-y-3 text-lg">
                    <li>Main Campus</li>
                    <li>Downtown</li>
                    <li>Riverside</li>
                    <li>Online</li>
                </ul>
            </div>

            <div class="col-span-2 flex items-center gap-4 mt-4">
                @foreach (['facebook', 'twitter', 'instagram'] as $social)
                    <span class="w-11 h-11 rounded-full flex items-center justify-center"
                        style="background-color:#52b788;">
                        <span class="text-white font-bold text-sm">{{ strtoupper(substr($social, 0, 1)) }}</span>
                    </span>
                @endforeach
            </div>

            <div class="col-span-2 text-2xl font-extrabold mt-4">
                Grace <span class="text-mint-300">Church</span>
            </div>
        </div>

        {{-- Right: dark contact card --}}
        <div class="form-card p-8 lg:p-10">
            <h3 class="text-3xl font-bold text-center mb-8">Have A Question</h3>
            <form class="space-y-5" onsubmit="event.preventDefault();">
                <div>
                    <label class="block mb-2 font-medium">Email</label>
                    <input type="email" placeholder="Enter your email"
                        class="w-full rounded-xl px-4 py-3 bg-white text-slate-800 placeholder-slate-400 focus:outline-none">
                </div>
                <div>
                    <label class="block mb-2 font-medium">Message</label>
                    <textarea rows="3" placeholder="What would you like to ask?"
                        class="w-full rounded-xl px-4 py-3 bg-white text-slate-800 placeholder-slate-400 focus:outline-none"></textarea>
                </div>
                <button type="submit"
                    class="w-full rounded-xl py-3.5 font-semibold text-white transition"
                    style="background-color:#52b788;">
                    Send Question
                </button>
            </form>
        </div>
    </div>

    <div class="border-t border-white/10">
        <div class="max-w-7xl mx-auto px-6 lg:px-10 py-6 text-center text-white/60 text-sm">
            &copy; {{ date('Y') }} Grace Community Church. All rights reserved.
        </div>
    </div>
</footer>
