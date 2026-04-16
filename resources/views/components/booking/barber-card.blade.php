@props(['barber', 'selected' => false])

<button
    {{ $attributes->merge(['class' => 'w-full liquid-glass liquid-button rounded-[1.5rem] p-4 flex items-center gap-4 border-2 transition-all shadow-sm text-right group']) }}
    :class="{{ $selected }} ? 'border-banhafade-accent bg-banhafade-accent/5' : 'border-white/50 dark:border-white/10'">

    @php $img = $barber->images->first(); @endphp
    <div class="shrink-0">
        @if ($img)
            <img src="{{ Storage::url($img->path) }}" alt="{{ $barber->name }}"
                class="w-14 h-14 rounded-full object-cover shadow-sm bg-white dark:bg-[#1c1c1e]">
        @else
            <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center shadow-sm">
                <span class="text-xl text-gray-400 font-black uppercase">{{ mb_substr($barber->name, 0, 1) }}</span>
            </div>
        @endif
    </div>

    <div class="flex-1">
        <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase leading-tight">
            {{ $barber->name }}
        </h3>
        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mt-1">
            {{ $barber->services->count() > 0 ? $barber->services->pluck('name')->join(' • ') : __('messages.top_artist') }}
        </p>
    </div>

    {{-- Selected Indicator --}}
    <div x-show="{{ $selected }}" x-transition:enter="transition transform duration-300"
        x-transition:enter-start="scale-0" x-transition:enter-end="scale-100" class="shrink-0">
        <div
            class="w-6 h-6 rounded-full bg-banhafade-accent flex items-center justify-center shadow-[0_0_10px_rgba(1,101,225,0.4)]">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3"
                stroke="currentColor" class="w-3.5 h-3.5 text-white">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
        </div>
    </div>
</button>
