@props(['label', 'value', 'border' => false, 'valueClass' => 'text-gray-900 dark:text-white'])

<div {{ $attributes->merge(['class' => 'flex justify-between items-center ' . ($border ? 'pb-4 border-b border-gray-100 dark:border-gray-800' : '')]) }}>
    <span class="text-sm text-gray-500 font-bold tracking-tight">{{ $label }}</span>
    <span class="text-sm font-black {{ $valueClass }}">{{ $value }}</span>
</div>
