@props(['title' => '', 'color' => 'text-banhafade-accent'])

<div class="flex items-center gap-3 px-1 mb-5">
    <h2 class="text-[11px] font-black {{ $color }} uppercase tracking-[0.2em] whitespace-nowrap">
        {{ $title }}</h2>
    <div class="flex-1 h-px {{ str_replace('text-', 'bg-', $color) }}/10"></div>
</div>
