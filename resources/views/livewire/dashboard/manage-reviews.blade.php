<div class="pb-[calc(5rem+var(--safe-area-bottom)+64px)] relative min-h-screen pt-4 px-4 overflow-y-auto">
    <!-- Header -->
    <div class="mb-6 mt-4">
        <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">
            {{ __('messages.manage_reviews_title') }}
        </h1>
        <p class="text-sm text-gray-500 font-bold mt-1">
            {{ __('messages.manage_reviews_desc') }}
        </p>
    </div>

    <!-- Rating Overview Card -->
    <div
        class="liquid-glass rounded-[1.5rem] p-6 border border-white/30 dark:border-white/10 shadow-sm mb-8 relative overflow-hidden transition-all hover:scale-[1.01]">
        <div class="absolute inset-0 bg-yellow-400/5 pointer-events-none"></div>
        <div class="flex items-center justify-between relative z-10">
            <div>
                <p class="text-[10px] font-black text-yellow-500 uppercase tracking-[0.2em] mb-2 leading-none">
                    {{ __('messages.rating') }}
                </p>
                <div class="flex items-end gap-2">
                    <p class="text-4xl font-black text-gray-900 dark:text-white leading-none">
                        {{ (float) $this->stats['average_rating'] }}
                    </p>
                    <div class="flex flex-col mb-1">
                        <div class="flex items-center gap-0.5 mb-1 relative h-3.5">
                            <!-- Background Stars (Gray) -->
                            <div class="flex items-center gap-0.5">
                                @for ($i = 1; $i <= 5; $i++)
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                        class="w-3.5 h-3.5 text-gray-200 dark:text-gray-700">
                                        <path fill-rule="evenodd"
                                            d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @endfor
                            </div>

                            <!-- Foreground Stars (Gold) - Fills from right to left -->
                            <div class="flex items-center gap-0.5 absolute top-0 right-0 overflow-hidden h-3.5"
                                style="width: {{ ($this->stats['average_rating'] / 5) * 100 }}%">
                                <div class="flex items-center gap-0.5">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                            class="w-3.5 h-3.5 text-yellow-400 shrink-0">
                                            <path fill-rule="evenodd"
                                                d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    @endfor
                                </div>
                            </div>
                        </div>
                        <p class="text-[10px] text-gray-400 font-black uppercase tracking-widest leading-none">
                            {{ $this->stats['total_reviews'] }} {{ __('messages.reviews') }}
                        </p>
                    </div>
                </div>
            </div>
            <div
                class="w-14 h-14 rounded-2xl bg-yellow-400/10 border border-yellow-400/20 flex items-center justify-center">
                @if ($this->stats['average_rating'] > 0)
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-8 h-8 text-yellow-500">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.562.562 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.563.563 0 00-.182-.557l-4.204-3.602a.562.562 0 01 .321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                    </svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-8 h-8 text-blue-500">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                    </svg>
                @endif
            </div>
        </div>
    </div>

    <!-- Feed -->
    <div class="space-y-4">
        @forelse($this->reviews as $review)
            <div wire:key="review-{{ $review->id }}"
                class="liquid-glass rounded-[1.5rem] p-4 border border-white/30 dark:border-white/10 shadow-sm animate-in fade-in slide-in-from-bottom-4 duration-500">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-2xl bg-banhafade-accent/10 flex items-center justify-center border border-banhafade-accent/20">
                            <span class="text-xs font-black text-banhafade-accent uppercase">
                                {{ mb_substr($review->user->name ?? __('messages.anonymous_user'), 0, 1) }}
                            </span>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <p class="text-sm font-black text-gray-900 dark:text-white uppercase leading-none">
                                    {{ $review->user->name ?? __('messages.anonymous_user') }}
                                </p>
                                @if ($review->booking)
                                    <span
                                        class="text-[9px] font-black bg-banhafade-accent/10 text-banhafade-accent px-1.5 py-0.5 rounded-md border border-banhafade-accent/20">
                                        #{{ $review->booking->booking_code }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">
                                {{ $review->created_at->translatedFormat('d M Y') }} •
                                {{ $review->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                    <div
                        class="flex items-center gap-1 bg-yellow-400/10 px-2.5 py-1 rounded-xl border border-yellow-400/20">
                        <span
                            class="text-xs font-black text-yellow-600 dark:text-yellow-500">{{ (float) $review->rating }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                            class="w-3 h-3 text-yellow-500">
                            <path fill-rule="evenodd"
                                d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>

                @if ($review->comment)
                    <div class="relative bg-black/5 dark:bg-white/5 rounded-2xl p-4 mb-4">
                        <svg class="absolute -top-2 right-4 w-6 h-6 text-gray-200 dark:text-gray-800"
                            fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M14.017 21L14.017 18C14.017 16.8954 14.9124 16 16.017 16H19.017C20.1216 16 21.017 16.8954 21.017 18V21C21.017 22.1046 20.1216 23 19.017 23H16.017C14.9124 23 14.017 22.1046 14.017 21ZM14.017 21L14.017 18C14.017 16.8954 14.9124 16 16.017 16H13.017C11.9124 16 11.017 16.8954 11.017 18V21C11.017 22.1046 11.9124 23 13.017 23H16.017C17.1216 23 18.017 22.1046 18.017 21Z"
                                class="opacity-10" />
                        </svg>
                        <p class="text-[13px] text-gray-700 dark:text-gray-300 font-medium leading-relaxed">
                            {{ $review->comment }}
                        </p>
                    </div>
                @endif

                @if ($review->booking && $review->booking->barber)
                    <div class="flex items-center justify-between pt-4 border-t border-black/5 dark:border-white/5">
                        <div class="flex items-center gap-2">
                            <div
                                class="w-7 h-7 rounded-lg bg-black/5 dark:bg-white/5 flex items-center justify-center shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                            </div>
                            <div>
                                <p
                                    class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">
                                    {{ __('messages.review_barber_rating') }}</p>
                                <p class="text-xs font-black text-banhafade-accent uppercase leading-none">
                                    {{ $review->booking->barber->name }}</p>
                            </div>
                        </div>

                        @if ($barberRating = $this->getBarberRating($review))
                            <div
                                class="flex items-center gap-1 bg-black/5 dark:bg-white/5 px-2 py-1 rounded-lg border border-black/5">
                                <span
                                    class="text-[10px] font-black text-gray-600 dark:text-gray-400">{{ (float) $barberRating }}</span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                    class="w-2.5 h-2.5 text-yellow-500">
                                    <path fill-rule="evenodd"
                                        d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @empty
            <x-empty-state title="{{ __('messages.no_reviews_yet') }}"
                description="{{ __('messages.no_reviews_yet_desc') }}">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-12 h-12 opacity-30">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.562.562 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.563.563 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                    </svg>
                </x-slot>
            </x-empty-state>
        @endforelse

        <!-- Infinite Scroll Sentinel -->
        @if ($this->hasMore)
            <div wire:key="sentinel-{{ $this->perPage }}" wire:intersect="loadMore"
                class="flex justify-center py-8">
                <div
                    class="flex items-center gap-2 px-4 py-2 rounded-full border border-black/5 dark:border-white/10 bg-white/40 dark:bg-white/5 backdrop-blur-xl">
                    <span class="text-xs font-bold text-gray-500">{{ __('messages.loading_more_dashboard') }}</span>
                    <svg class="animate-spin h-4 w-4 text-banhafade-accent" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </div>
            </div>
        @endif
    </div>
</div>
