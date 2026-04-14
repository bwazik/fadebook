<div class="pb-[calc(5rem+var(--safe-area-bottom)+64px)] relative min-h-screen pt-4 px-4">
    <!-- Header -->
    <x-sticky-back-button href="{{ route('booking.show', $booking->uuid) }}" />

    <div class="mb-6 mt-4">
        <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">
            {{ __('messages.review_title') }}
        </h1>
        <p class="text-sm text-gray-500 font-bold mt-1">
            {{ __('messages.review_subtitle') }}
        </p>
    </div>

    <form wire:submit="submit" class="space-y-6">
        
        <!-- Shop Rating -->
        <div class="liquid-glass rounded-2xl p-5 border border-white/20 shadow-sm flex flex-col items-center">
            <h2 class="text-sm font-black text-gray-900 dark:text-white mb-1">{{ __('messages.review_shop_rating') }}</h2>
            <p class="text-[10px] font-bold text-gray-500 mb-4">{{ $booking->shop->name }}</p>
            
            <div class="flex items-center gap-2" x-data="{ rating: @entangle('shopRating') }">
                <template x-for="i in 5">
                    <button type="button" @click="rating = i; $wire.setShopRating(i)" class="p-1 transition-transform active:scale-90 outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5"
                            :class="i <= rating ? 'fill-yellow-400 stroke-yellow-400' : 'fill-transparent stroke-gray-300 dark:stroke-gray-600'"
                            class="w-10 h-10 transition-colors">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                        </svg>
                    </button>
                </template>
            </div>
        </div>

        <!-- Barber Rating (Optional) -->
        @if($booking->barber)
            <div class="liquid-glass rounded-2xl p-5 border border-white/20 shadow-sm flex flex-col items-center">
                <h2 class="text-sm font-black text-gray-900 dark:text-white mb-1">{{ __('messages.review_barber_rating') }}</h2>
                <p class="text-[10px] font-bold text-gray-500 mb-4">{{ $booking->barber->name }}</p>
                
                <div class="flex items-center gap-2" x-data="{ rating: @entangle('barberRating') }">
                    <template x-for="i in 5">
                        <button type="button" @click="rating = i; $wire.setBarberRating(i)" class="p-1 transition-transform active:scale-90 outline-none">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="1.5"
                                :class="i <= rating ? 'fill-yellow-400 stroke-yellow-400' : 'fill-transparent stroke-gray-300 dark:stroke-gray-600'"
                                class="w-8 h-8 transition-colors">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                            </svg>
                        </button>
                    </template>
                </div>
            </div>
        @endif

        <!-- Comment -->
        <div class="liquid-glass rounded-2xl p-5 border border-white/20 shadow-sm">
            <x-ios-textarea :label="__('messages.review_comment_label')" wire:model="comment" :placeholder="__('messages.review_comment_placeholder')" rows="4" />
        </div>

        <!-- Submit Button -->
        <div class="pt-4">
            <x-ios-button type="submit" wire:loading.attr="disabled">
                <span wire:loading.remove>{{ __('messages.review_submit') }}</span>
                <span wire:loading>{{ __('messages.review_submitting') }}</span>
            </x-ios-button>
        </div>

    </form>
</div>