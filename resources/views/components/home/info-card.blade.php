@props([
    'href' => null,
    'title',
    'subtitle' => null,
    'color' => 'banhafade-accent', // amber, red, banhafade-accent
    'dismissible' => false,
    'wireClickDismiss' => null,
])

@php
    $colorMap = [
        'banhafade-accent' => [
            'border' => 'border-banhafade-accent/20',
            'bg' => 'bg-banhafade-accent/5',
            'iconBg' => 'bg-banhafade-accent',
            'iconText' => 'text-white',
            'iconShadow' => 'shadow-banhafade-accent/20',
        ],
        'amber' => [
            'border' => 'border-amber-400/20',
            'bg' => 'bg-amber-400/5',
            'iconBg' => 'bg-amber-400',
            'iconText' => 'text-white',
            'iconShadow' => 'shadow-amber-400/20',
        ],
        'red' => [
            'border' => 'border-red-400/20',
            'bg' => 'bg-red-400/5',
            'iconBg' => 'bg-red-400',
            'iconText' => 'text-white',
            'iconShadow' => 'shadow-red-400/20',
        ],
    ];

    // Tailwind 4 fallback / mapping if specific colors aren't defined as variants
// Using hex or standard tailwind classes
$colors = $colorMap[$color] ?? $colorMap['banhafade-accent'];
@endphp

<div {{ $attributes->merge(['class' => 'px-4']) }}>
    @if ($href)
        <a href="{{ $href }}" wire:navigate
            class="block w-full liquid-glass rounded-2xl p-3 flex items-center gap-3 liquid-button border {{ $colors['border'] }} {{ $colors['bg'] }} transition-all active:scale-[0.98]">
            <div
                class="w-8 h-8 rounded-full {{ $colors['iconBg'] }} {{ $colors['iconText'] }} flex items-center justify-center shadow-sm">
                {{ $icon }}
            </div>
            <div class="flex-1">
                <p class="text-sm font-black text-gray-900 dark:text-white leading-tight">
                    {{ $title }}
                </p>
                @if ($subtitle)
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 font-bold leading-tight mt-0.5">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                stroke="currentColor" class="w-3.5 h-3.5 text-gray-400 rtl:rotate-180">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
        </a>
    @else
        <div
            class="w-full liquid-glass rounded-2xl p-3 flex items-center gap-3 border {{ $colors['border'] }} {{ $colors['bg'] }} relative">
            <div
                class="w-8 h-8 rounded-full {{ $colors['iconBg'] }} {{ $colors['iconText'] }} flex items-center justify-center shadow-sm">
                {{ $icon }}
            </div>
            <div class="flex-1">
                <p class="text-sm font-black text-gray-900 dark:text-white leading-tight">
                    {{ $title }}
                </p>
                @if ($subtitle)
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 font-bold leading-tight mt-0.5">
                        {{ $subtitle }}
                    </p>
                @endif
            </div>
            @if ($dismissible)
                <button wire:click="{{ $wireClickDismiss }}"
                    class="p-1.5 -mr-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-all cursor-pointer hover:scale-110 active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            @endif
        </div>
    @endif
</div>
