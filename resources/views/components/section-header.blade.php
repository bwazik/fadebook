{{-- Section Header with optional "See All" link --}}
@props(['title' => '', 'href' => null, 'linkLabel' => 'الكل'])

<div class="flex items-center justify-between mb-3">
    <h2 class="text-base font-bold text-gray-900 dark:text-white">{{ $title }}</h2>
    @if($href)
        <a href="{{ $href }}" wire:navigate
           class="text-sm font-semibold text-[--color-fadebook-accent] active:opacity-70 transition">
            {{ $linkLabel }}
        </a>
    @endif
</div>
