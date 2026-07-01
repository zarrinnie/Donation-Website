@props(['active' => 'home'])

@php
    $links = [
        ['label' => 'Home', 'route' => 'home', 'key' => 'home'],
        ['label' => 'About', 'route' => 'about', 'key' => 'about'],
        ['label' => 'Donate', 'route' => 'donate', 'key' => 'donate'],
    ];
@endphp

<header class="absolute top-0 left-0 right-0 z-30">
    <nav class="max-w-7xl mx-auto px-6 lg:px-10 py-6 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <span class="text-2xl font-extrabold tracking-tight text-slate-900">Grace</span>
            <span class="w-3 h-3 rounded-full bg-white border-2 border-slate-900 inline-block"></span>
            <span class="text-2xl font-extrabold tracking-tight text-slate-900">Church</span>
        </a>

        <div class="hidden md:flex items-center gap-8 font-medium text-slate-800">
            @foreach ($links as $link)
                <a href="{{ route($link['route']) }}"
                    class="hover:text-mint transition {{ $active === $link['key'] ? 'text-mint font-semibold' : '' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </div>

        <a href="{{ route('donate') }}"
            class="btn-pill bg-mint-500 hover:bg-mint-600 text-white font-semibold px-6 py-2.5 transition"
            style="background-color:#52b788;">
            Start Giving
        </a>
    </nav>
</header>
