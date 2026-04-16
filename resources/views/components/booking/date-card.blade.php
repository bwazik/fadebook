@props(['date', 'active' => false])

@php
    $isToday = $date->isToday();
@endphp

<button
    {{ $attributes->merge(['class' => 'shrink-0 snap-center min-w-[6.5rem] py-2 rounded-xl border flex flex-col items-center justify-center transition-all duration-300 relative group overflow-hidden cursor-pointer']) }}
    :class="{{ $active }}
        ?
        'border-banhafade-accent/50 bg-banhafade-accent text-white shadow-[0_8px_20px_rgba(1,101,225,0.2)]' :
        'border-white/50 dark:border-white/10 liquid-glass text-gray-700 dark:text-gray-300'">

    <span class="text-[8px] uppercase font-black tracking-widest mb-0.5 transition-opacity"
        :class="{{ $active }} ? 'opacity-90' : 'opacity-40'">
        {{ $date->translatedFormat('D') }}
    </span>

    <div class="flex items-center gap-1.5">
        <span class="text-lg font-black tracking-tighter leading-none">{{ $date->format('d') }}</span>
        <span class="text-[9px] uppercase font-black tracking-tight transition-opacity"
            :class="{{ $active }} ? 'opacity-90' : 'opacity-40'">
            {{ $date->translatedFormat('M') }}
        </span>
    </div>

    @if ($isToday)
        <div class="absolute top-1.5 right-1.5 w-1 h-1 rounded-full"
            :class="{{ $active }} ? 'bg-white' : 'bg-banhafade-accent'">
        </div>
    @endif

    {{-- Pulse effect for today --}}
    @if ($isToday && !$active)
        <div class="absolute inset-0 bg-banhafade-accent/5 animate-pulse pointer-events-none"></div>
    @endif
</button>
