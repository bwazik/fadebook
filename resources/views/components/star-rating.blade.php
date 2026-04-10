{{-- Static Star Rating Display --}}
@props(['rating' => 0, 'count' => null, 'size' => 'sm'])

@php
$sizeClass = $size === 'lg' ? 'w-5 h-5' : 'w-3.5 h-3.5';
$rating = (float) $rating;
@endphp

<div class="flex items-center gap-1">
    <div class="flex items-center gap-0.5">
        @for($i = 1; $i <= 5; $i++)
            <svg class="{{ $sizeClass }} {{ $i <= round($rating) ? 'text-yellow-400' : 'text-gray-300 dark:text-white/20' }}"
                 fill="currentColor" viewBox="0 0 24 24">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
        @endfor
    </div>
    @if($count !== null)
        <span class="text-xs text-gray-500 dark:text-white/40 font-medium">({{ $count }})</span>
    @endif
</div>
