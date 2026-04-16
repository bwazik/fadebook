{{-- Avatar Component with initials fallback --}}
@props(['src' => null, 'name' => '', 'size' => 'md'])

@php
    $sizes = [
        'xs' => 'w-8 h-8 text-xs',
        'sm' => 'w-10 h-10 text-sm',
        'md' => 'w-12 h-12 text-base',
        'lg' => 'w-16 h-16 text-lg',
        'xl' => 'w-20 h-20 text-xl',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
    $initials = collect(explode(' ', $name))->take(2)->map(fn($w) => mb_substr($w, 0, 1))->join('');
@endphp

<div {{ $attributes->merge(['class' => "relative rounded-full overflow-hidden shrink-0 $sizeClass"]) }}>
    @if ($src)
        <img src="{{ $src }}" alt="{{ $name }}" class="w-full h-full object-cover"
            referrerpolicy="no-referrer">
    @else
        <div class="w-full h-full bg-[--color-banhafade-accent]/10 flex items-center justify-center">
            <span class="font-bold text-[--color-banhafade-accent]">{{ $initials ?: '?' }}</span>
        </div>
    @endif
</div>
