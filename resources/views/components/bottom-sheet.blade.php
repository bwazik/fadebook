@props(['title' => null, 'icon' => null])

<div x-data="{ open: @entangle($attributes->wire('model')).live }" x-show="open" x-init="$watch('open', val => {
    $dispatch(val ? 'hide-bottom-nav' : 'show-bottom-nav')
})" style="display: none;"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-[60] bg-black/40 backdrop-blur-md flex items-end justify-center overflow-hidden"
    @click.self="open = false">

    {{-- Bottom Sheet Modal Container --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-400"
        x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
        x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-y-0"
        x-transition:leave-end="translate-y-full"
        class="bg-white/80 dark:bg-[#1c1c1e]/80 backdrop-blur-3xl border-t border-white/50 dark:border-white/10 rounded-t-[2rem] w-full max-w-md shadow-2xl relative max-h-[85vh] flex flex-col mb-0"
        @click.stop>

        {{-- Drag Handle --}}
        <div class="flex justify-center pt-4 pb-2 shrink-0 cursor-grab active:cursor-grabbing select-none">
            <div class="w-10 h-1.5 rounded-full bg-gray-300 dark:bg-white/20"></div>
        </div>

        <div class="p-6 pt-2 pb-[calc(1.5rem+env(safe-area-inset-bottom))] overflow-y-auto no-scrollbar flex-1">
            {{-- Header --}}
            @if ($title)
                <div class="flex flex-col items-center mb-6 text-center">
                    @if ($icon)
                        <div
                            class="w-16 h-16 rounded-2xl bg-banhafade-accent/10 flex items-center justify-center mb-4 shadow-inner text-banhafade-accent">
                            {{ $icon }}
                        </div>
                    @else
                        <div class="w-12"></div>
                    @endif

                    <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">
                        {{ $title }}
                    </h3>
                </div>
            @endif

            {{-- Content Slot --}}
            <div class="relative">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
