@props(['review'])

<div {{ $attributes->merge(['class' => 'snap-start shrink-0 w-80 liquid-glass rounded-[1.2rem] p-5 border-white/30 dark:border-white/10 shadow-sm transition-transform active:scale-[0.99]']) }}>
    <div class="flex justify-between items-start mb-4">
        <div class="flex items-center gap-3.5">
            <x-avatar :src="$review->user->profile_photo_url ?? null" :name="$review->user->name" size="xs" />
            <div>
                <h4 class="text-sm font-black text-gray-900 dark:text-white leading-none">
                    {{ $review->user->name }}</h4>
                <p class="text-[10px] text-gray-400 font-bold mt-1.5">
                    {{ $review->created_at->diffForHumans() }}</p>
            </div>
        </div>
        <div
            class="flex items-center gap-1 bg-yellow-400/10 px-2 py-0.5 rounded-xl border border-yellow-400/20">
            <span
                class="text-[11px] font-black text-yellow-600 dark:text-yellow-500">{{ (float) $review->rating }}</span>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                class="w-3 h-3 text-yellow-500">
                <path fill-rule="evenodd"
                    d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                    clip-rule="evenodd" />
            </svg>
        </div>
    </div>
    <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed font-bold">
        {{ $review->comment }}</p>
</div>
