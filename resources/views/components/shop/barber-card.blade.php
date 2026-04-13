@props(['barber'])

<div {{ $attributes->merge(['class' => 'snap-start shrink-0 w-48 liquid-glass rounded-[2rem] p-6 text-center shadow-xl dark:shadow-black/20 border-white/40 dark:border-white/5 transition-all active:scale-[0.98]']) }}>
    <div class="mx-auto w-24 h-24 mb-4 relative">
        @php $img = $barber->images->first(); @endphp
        @if ($img)
            <img src="{{ Storage::url($img->path) }}" alt="{{ $barber->name }}"
                class="w-full h-full rounded-full object-cover border-2 border-fadebook-accent p-1 shadow-md bg-white dark:bg-gray-800">
        @else
            <div
                class="w-full h-full rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center border-2 border-fadebook-accent/10">
                <span
                    class="text-2xl text-gray-400 font-black uppercase">{{ mb_substr($barber->name, 0, 1) }}</span>
            </div>
        @endif
        <div
            class="absolute -bottom-1 left-1/2 -translate-x-1/2 bg-yellow-400 text-white text-[10px] font-black px-2.5 py-0.5 rounded-full shadow-md flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                class="w-3 h-3">
                <path fill-rule="evenodd"
                    d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                    clip-rule="evenodd" />
            </svg>
            {{ (float) $barber->average_rating }}
        </div>
    </div>
    <h3 class="font-black text-gray-900 dark:text-white text-base truncate leading-none">
        {{ $barber->name }}</h3>
    <p
        class="text-[10px] text-fadebook-accent font-black uppercase mt-2 tracking-widest leading-none whitespace-nowrap overflow-hidden">
        {{ $barber->services->count() > 0 ? implode(' • ', $barber->services->pluck('name')->toArray()) : __('messages.top_artist') }}
    </p>
</div>
