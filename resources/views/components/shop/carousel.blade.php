@props(['gap' => 'gap-4', 'pb' => 'pb-6'])

<div {{ $attributes->merge(['class' => "flex overflow-x-auto $gap $pb snap-x [scrollbar-width:none] [&::-webkit-scrollbar]:hidden -mx-4 px-4 scroll-px-4"]) }}>
    {{ $slot }}
</div>
