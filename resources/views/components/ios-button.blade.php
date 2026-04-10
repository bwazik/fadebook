{{-- iOS Button Component --}}
@props(['target' => null, 'variant' => 'primary'])

@php
$variants = [
    'primary'   => 'bg-fadebook-accent text-white shadow-md',
    'secondary' => 'bg-black/5 dark:bg-white/10 text-gray-700 dark:text-white/70',
    'ghost'     => 'bg-transparent text-fadebook-accent border border-fadebook-accent/30',
    'danger'    => 'bg-red-500 text-white shadow-md shadow-red-500/30',
];
$variantClass = $variants[$variant] ?? $variants['primary'];
@endphp

<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => "liquid-button w-full py-4 px-4 rounded-2xl font-bold text-sm
                disabled:opacity-50 disabled:cursor-not-allowed
                flex justify-center items-center gap-2
                $variantClass"
]) }}>
    @if($target)
        <span wire:loading.remove wire:target="{{ $target }}">{{ $slot }}</span>
        <span wire:loading wire:target="{{ $target }}"
              class="w-5 h-5 border-2 border-current border-t-transparent rounded-full animate-spin"></span>
    @else
        {{ $slot }}
    @endif
</button>
