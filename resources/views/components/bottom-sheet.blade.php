{{-- Bottom Sheet Modal Component --}}
{{-- Usage: <x-bottom-sheet :show="$showModal" @close="$set('showModal', false)">Content</x-bottom-sheet> --}}
@props(['title' => ''])

<div
    x-data="{ open: @entangle($attributes->wire('model')).live }"
    x-show="open"
    x-init="$watch('open', val => $dispatch(val ? 'hide-bottom-nav' : 'show-bottom-nav'))"
    style="display: none;"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-out duration-300"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-50 bg-black/40 backdrop-blur-md flex items-end justify-center"
    @click.self="open = false"
>
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-y-full"
        x-transition:enter-end="translate-y-0"
        x-transition:leave="transition ease-out duration-300"
        x-transition:leave-start="translate-y-0"
        x-transition:leave-end="translate-y-full"
        class="bg-white/80 dark:bg-[#1c1c1e]/80 backdrop-blur-3xl border-t border-white/50 dark:border-white/10 p-6 rounded-t-[2rem] w-full max-w-lg shadow-2xl"
        style="padding-bottom: calc(1.5rem + env(safe-area-inset-bottom));"
        @click.stop
    >
        {{-- Drag Handle --}}
        <div class="flex justify-center mb-4">
            <div class="w-10 h-1 rounded-full bg-gray-300 dark:bg-white/20"></div>
        </div>

        @if($title)
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">{{ $title }}</h3>
        @endif

        {{ $slot }}
    </div>
</div>
