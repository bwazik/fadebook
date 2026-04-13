@props(['status'])

@php
    $classes = match($status) {
        \App\Enums\BookingStatus::Pending => 'bg-yellow-50 text-yellow-700 border-yellow-200 dark:bg-yellow-900/20 dark:text-yellow-500 dark:border-yellow-900/30',
        \App\Enums\BookingStatus::Confirmed => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/20 dark:text-blue-500 dark:border-blue-900/30',
        \App\Enums\BookingStatus::InProgress => 'bg-purple-50 text-purple-700 border-purple-200 dark:bg-purple-900/20 dark:text-purple-500 dark:border-purple-900/30',
        \App\Enums\BookingStatus::Completed => 'bg-green-50 text-green-700 border-green-200 dark:bg-green-900/20 dark:text-green-500 dark:border-green-900/30',
        \App\Enums\BookingStatus::Cancelled => 'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/20 dark:text-red-500 dark:border-red-900/30',
        \App\Enums\BookingStatus::NoShow => 'bg-gray-50 text-gray-700 border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700',
        default => 'bg-gray-50 text-gray-700 border-gray-200 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-700',
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center justify-center px-2 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border shadow-sm $classes"]) }}>
    {{ $status->getLabel() }}
</span>
