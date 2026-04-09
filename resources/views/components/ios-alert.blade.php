{{-- iOS Alert Component --}}
@props(['type' => 'info'])

@php
    $styles = [
        'success' => 'bg-green-100/80 dark:bg-green-900/40 text-green-800 dark:text-green-300 border-green-200/50 dark:border-green-800/50',
        'error' => 'bg-red-100/80 dark:bg-red-900/40 text-red-800 dark:text-red-300 border-red-200/50 dark:border-red-800/50',
        'warning' => 'bg-yellow-100/80 dark:bg-yellow-900/40 text-yellow-800 dark:text-yellow-300 border-yellow-200/50 dark:border-yellow-800/50',
        'info' => 'bg-blue-100/80 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300 border-blue-200/50 dark:border-blue-800/50',
    ];
    $style = $styles[$type] ?? $styles['info'];
@endphp

<div {{ $attributes->merge([
    'class' => "p-4 rounded-2xl border backdrop-blur-xl flex gap-3 items-center " . $style
]) }}>
    <div class="flex-1 text-[15px] font-medium leading-relaxed">
        {{ $slot }}
    </div>
</div>
