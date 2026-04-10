{{-- Filter Chip Component --}}
@props(['active' => false])

<button {{ $attributes->merge([
    'type' => 'button',
    'class' => 'inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-sm font-semibold
                transition-all duration-200 active:scale-95 whitespace-nowrap shrink-0 '
              . ($active
                  ? 'bg-[--color-fadebook-accent] text-white shadow-md'
                  : 'bg-black/5 dark:bg-white/10 text-gray-600 dark:text-white/70')
]) }}>
    {{ $slot }}
</button>
