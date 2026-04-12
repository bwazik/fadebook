{{-- Filter Chip Component --}}
@props(['active' => false])

<button
    {{ $attributes->merge([
        'type' => 'button',
        'class' =>
            'inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-black
                    liquid-button whitespace-nowrap shrink-0 shadow-none outline-0 focus:outline-hidden ring-0 ring-offset-0 ' .
            ($active
                ? 'bg-fadebook-accent text-white scale-105 z-10 border border-fadebook-accent'
                : 'liquid-glass bg-white/80 dark:bg-[#2c2c2e]/80 text-gray-500 dark:text-gray-400 !shadow-none !border-transparent'),
    ]) }}>
    {{ $slot }}
</button>
