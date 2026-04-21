{{-- iOS Toggle Component --}}
@props(['label' => '', 'description' => ''])

@php
    $inputAttributes = $attributes->whereStartsWith(['wire:', 'x-model', 'x-on:', '@', 'name', 'value', 'checked']);
    $wrapperAttributes = $attributes->whereDoesntStartWith(['wire:', 'x-model', 'x-on:', '@', 'name', 'value', 'checked']);
@endphp

<div {{ $wrapperAttributes->class([
    'flex items-center justify-between py-1 px-4' => $label || $description,
    'inline-flex' => !($label || $description),
]) }}>
    @if ($label || $description)
        <div class="flex-1">
            <label class="text-sm font-black text-gray-900 dark:text-white pointer-events-none">{{ $label }}</label>
            @if ($description)
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest pointer-events-none">{{ $description }}</p>
            @endif
        </div>
    @endif

    <label class="relative inline-flex items-center cursor-pointer">
        <input type="checkbox" {{ $inputAttributes->merge(['class' => 'sr-only peer']) }}>
        <div class="w-11 h-6 bg-gray-200/50 dark:bg-white/20 peer-focus:outline-none rounded-full peer 
                    peer-checked:bg-banhafade-accent dark:peer-checked:bg-banhafade-accent transition-all duration-300
                    after:content-[''] after:absolute after:top-[2px] after:start-[2px] 
                    after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all after:duration-300 after:shadow-sm
                    peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full 
                    peer-checked:after:border-white active:after:w-6 transition-all">
        </div>
    </label>
</div>
