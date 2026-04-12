@props([
    'type' => 'default',
    'color' => null
])

@php
$type = $color ?? $type;

$bgStyles = [
    'available'   => 'bg-green-500/15',
    'success'     => 'bg-green-500/15',
    'unavailable' => 'bg-gray-400/15',
    'gray'        => 'bg-gray-400/15',
    'pending'     => 'bg-yellow-500/15',
    'warning'     => 'bg-yellow-500/15',
    'confirmed'   => 'bg-blue-500/15',
    'info'        => 'bg-blue-500/15',
    'in_progress' => 'bg-indigo-500/15',
    'completed'   => 'bg-green-500/15',
    'cancelled'   => 'bg-red-500/15',
    'danger'      => 'bg-red-500/15',
    'no_show'     => 'bg-orange-500/15',
];

$textStyles = [
    'available'   => 'text-green-600 dark:text-green-400',
    'success'     => 'text-green-700 dark:text-green-400',
    'unavailable' => 'text-gray-500 dark:text-gray-400',
    'gray'        => 'text-gray-500 dark:text-gray-400',
    'pending'     => 'text-yellow-600 dark:text-yellow-400',
    'warning'     => 'text-yellow-600 dark:text-yellow-400',
    'confirmed'   => 'text-blue-600 dark:text-blue-400',
    'info'        => 'text-blue-600 dark:text-blue-400',
    'in_progress' => 'text-indigo-600 dark:text-indigo-400',
    'completed'   => 'text-green-600 dark:text-green-400',
    'cancelled'   => 'text-red-500',
    'danger'      => 'text-red-500',
    'no_show'     => 'text-orange-600 dark:text-orange-400',
];

$bgStyle = $bgStyles[$type] ?? 'bg-gray-400/15';
$textStyle = $textStyles[$type] ?? 'text-gray-500 dark:text-gray-400';

// If a bg- class is already in attributes, don't use the default bg style
$hasCustomBg = str_contains($attributes->get('class', ''), 'bg-');
$style = ($hasCustomBg ? '' : "$bgStyle ") . $textStyle;
@endphp

<span {{ $attributes->merge([
    'class' => "inline-flex items-center px-2.5 py-1 rounded-xl text-xs font-semibold $style"
]) }}>
    {{ $slot }}
</span>
