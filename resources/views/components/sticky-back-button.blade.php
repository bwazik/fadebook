@props([
    'href' => route('home'),
    'scrolledOffset' => 50,
])

<div class="sticky top-0 z-50 h-0 pointer-events-none" x-data="{ scrolled: false }"
    @scroll.window="scrolled = window.scrollY > {{ $scrolledOffset }}">
    <div class="flex justify-end px-2 pointer-events-auto transition-all duration-300"
        :class="scrolled ? 'pt-4' : 'pt-[calc(1rem+var(--safe-area-top))]'">
        <a href="{{ $href }}" wire:navigate
            class="inline-flex items-center justify-center w-11 h-11 rounded-full liquid-glass !bg-black/20 !border-white/20 text-white shadow-xl transition-all active:scale-90 liquid-button"
            {{ $attributes }}>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
        </a>
    </div>
</div>
