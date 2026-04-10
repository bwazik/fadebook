{{-- Skeleton Loading Placeholder --}}
@props(['lines' => 3, 'circle' => false, 'height' => 'h-4', 'card' => false])

@if($circle)
    <div class="rounded-full bg-gray-200 dark:bg-white/10 animate-pulse {{ $height }} aspect-square"></div>
@elseif($card)
    <div class="bg-white/70 dark:bg-[#1c1c1e]/70 rounded-[2rem] p-5 space-y-3 animate-pulse">
        <div class="h-5 bg-gray-200 dark:bg-white/10 rounded-full w-3/4"></div>
        <div class="h-4 bg-gray-200 dark:bg-white/10 rounded-full w-full"></div>
        <div class="h-4 bg-gray-200 dark:bg-white/10 rounded-full w-2/3"></div>
    </div>
@else
    <div class="space-y-3 animate-pulse">
        @for($i = 0; $i < $lines; $i++)
            <div class="rounded-full bg-gray-200 dark:bg-white/10 {{ $height }}"
                 style="width: {{ $i === $lines - 1 ? '60%' : '100%' }}"></div>
        @endfor
    </div>
@endif
