{{-- Page Header — Sticky, glass, with optional back button and right slot --}}
@props(['title' => '', 'backRoute' => null])

<header
    class="sticky top-0 z-40 -mx-4 px-5 py-3
               flex items-center gap-3
               bg-white/80 dark:bg-[#1c1c1e]/80
               backdrop-blur-2xl
               border-b border-black/5 dark:border-white/10
               transition-colors duration-300">

    @if ($backRoute)
        <a href="{{ route($backRoute) }}" wire:navigate
            class="p-2 -mr-1 rounded-full active:bg-black/5 dark:active:bg-white/10 transition-all text-[--color-banhafade-accent] shrink-0">
            {{-- Right arrow for RTL (goes back) --}}
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
            </svg>
        </a>
    @endif

    <h1 class="flex-1 text-lg font-bold text-gray-900 dark:text-white truncate">{{ $title }}</h1>

    @if (isset($actions))
        <div class="flex items-center gap-2 shrink-0">{{ $actions }}</div>
    @endif
</header>
