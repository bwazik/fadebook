{{-- Horizontal scrollable chip group --}}
<div {{ $attributes->merge([
    'class' => 'flex items-center gap-2 overflow-x-auto pb-1 -mx-4 px-4
                scrollbar-none [&::-webkit-scrollbar]:hidden
                [-webkit-overflow-scrolling:touch]'
]) }}>
    {{ $slot }}
</div>
