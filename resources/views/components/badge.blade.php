{{-- Status Badge Component --}}
@props(['type' => 'default'])

@php
$styles = [
    'available'   => 'bg-green-500/15 text-green-600 dark:text-green-400',
    'unavailable' => 'bg-gray-400/15 text-gray-500 dark:text-gray-400',
    'pending'     => 'bg-yellow-500/15 text-yellow-600 dark:text-yellow-400',
    'confirmed'   => 'bg-blue-500/15 text-blue-600 dark:text-blue-400',
    'in_progress' => 'bg-indigo-500/15 text-indigo-600 dark:text-indigo-400',
    'completed'   => 'bg-green-500/15 text-green-600 dark:text-green-400',
    'cancelled'   => 'bg-red-500/15 text-red-500',
    'no_show'     => 'bg-orange-500/15 text-orange-600 dark:text-orange-400',
];
$style = $styles[$type] ?? 'bg-gray-400/15 text-gray-500 dark:text-gray-400';
@endphp

<span {{ $attributes->merge([
    'class' => "inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold $style"
]) }}>
    {{ $slot }}
</span>
