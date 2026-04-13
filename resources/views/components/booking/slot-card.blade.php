@props(['time', 'active' => false])

@php
    if (is_string($time)) {
        $time = \Carbon\Carbon::createFromFormat('H:i', $time);
    }
@endphp

<button {{ $attributes->merge(['class' => 'py-2.5 rounded-xl border text-xs font-black transition-all duration-300 liquid-button relative overflow-hidden group/btn cursor-pointer']) }}
    :class="{{ $active }}
        ? 'border-fadebook-accent bg-fadebook-accent/10 text-fadebook-accent'
        : 'border-white/40 dark:border-white/10 liquid-glass text-gray-600 dark:text-gray-400'">
    
    <div class="flex items-center justify-center gap-1.5">
        <span class="tracking-tighter text-sm">{{ $time->format('g:i') }}</span>
        <span class="text-[9px] opacity-60 uppercase font-black"
            :class="{{ $active }} ? 'text-fadebook-accent' : ''">
            {{ $time->format('a') === 'am' ? __('messages.time_am') : __('messages.time_pm') }}
        </span>
    </div>

    {{-- Highlight Layer --}}
    <div x-show="{{ $active }}"
        class="absolute inset-0 bg-fadebook-accent/5 pointer-events-none"
        x-transition:enter="transition opacity duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100">
    </div>
</button>
