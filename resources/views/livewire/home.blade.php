<div class="pb-24">
    <!-- Top Header Section (Mobile App Style) -->
    <div class="px-4 pt-[calc(1.5rem+var(--safe-area-top))] pb-6">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-black text-fadebook-accent tracking-tighter">{{ __('messages.app_name') }}</h1>
                <p class="text-xs font-black text-gray-500 dark:text-gray-400 mt-0.5 uppercase tracking-[0.1em]">
                    {{ __('messages.app_slogan') }}</p>
            </div>
            <!-- Optional User Avatar on right -->
            <a href="{{ route('profile.index') }}" wire:navigate
                class="w-12 h-12 rounded-full bg-gray-200 dark:bg-gray-800 border-2 border-white dark:border-gray-700 shadow-sm overflow-hidden flex items-center justify-center liquid-button">
                @auth
                    <span
                        class="text-gray-600 dark:text-gray-300 font-bold text-sm">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-6 h-6 text-gray-400">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                @endauth
            </a>
        </div>

        <!-- Greetings Section -->
        <div>
            @auth
                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                    {{ __('messages.home_welcome_back', ['name' => explode(' ', auth()->user()->name)[0]]) }}
                </h2>
            @else
                <h2 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                    {{ __('messages.home_welcome_guest') }}
                </h2>
            @endauth
            <p class="text-gray-500 dark:text-gray-400 font-bold mt-1">{{ __('messages.home_where_to_cut') }}</p>
        </div>
    </div>

    <!-- Shop Status Banner -->
    @if ($this->pendingShop)
        <div class="px-4 mt-2 mb-4">
            @if ($this->pendingShop->status === App\Enums\ShopStatus::Pending)
                <div class="liquid-glass border-amber-400/20 bg-amber-400/5 rounded-2xl p-4 flex items-center gap-4 relative">
                    <div class="w-10 h-10 rounded-full bg-amber-400/10 flex items-center justify-center text-amber-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-black text-gray-900 dark:text-white leading-tight">
                            {{ __('messages.shop_pending_title') }}</p>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 font-bold leading-tight mt-1">
                            {{ __('messages.shop_pending_subtitle') }}</p>
                    </div>
                    <button wire:click="dismissStatusBanner" class="p-1 -mr-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-all cursor-pointer hover:scale-110 active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @elseif($this->pendingShop->status === App\Enums\ShopStatus::Rejected)
                <div class="liquid-glass border-red-400/20 bg-red-400/5 rounded-2xl p-4 flex items-center gap-4 relative">
                    <div class="w-10 h-10 rounded-full bg-red-400/10 flex items-center justify-center text-red-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-black text-gray-900 dark:text-white leading-tight">
                            {{ __('messages.shop_rejected_title') }}</p>
                        <p class="text-[11px] text-gray-500 dark:text-gray-400 font-bold leading-tight mt-1">
                            {{ $this->pendingShop->rejection_reason ?? __('messages.shop_rejected_subtitle') }}</p>
                    </div>
                    <button wire:click="dismissStatusBanner" class="p-1 -mr-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-all cursor-pointer hover:scale-110 active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            @endif
        </div>
    @endif

    <!-- Info Banner (Upcoming Appointments) -->
    @if ($this->upcomingBookingsCount > 0)
        <div class="px-4 mt-3 mb-1">
            <a href="{{ route('bookings.index') }}" wire:navigate
                class="block w-full liquid-glass rounded-2xl p-3 flex items-center gap-3 liquid-button border border-fadebook-accent/20 bg-fadebook-accent/5">
                <div
                    class="w-8 h-8 rounded-full bg-fadebook-accent text-white flex items-center justify-center shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4">
                        <path fill-rule="evenodd"
                            d="M5.75 2a.75.75 0 0 1 .75.75V4h7V2.75a.75.75 0 0 1 1.5 0V4h.25A2.75 2.75 0 0 1 18 6.75v8.5A2.75 2.75 0 0 1 15.25 18H4.75A2.75 2.75 0 0 1 2 15.25v-8.5A2.75 2.75 0 0 1 4.75 4H5V2.75A.75.75 0 0 1 5.75 2Zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75Z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-bold text-gray-900 dark:text-white">
                        {{ __('messages.home_upcoming_bookings', ['count' => $this->upcomingBookingsCount]) }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('messages.home_view_details') }}</p>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-4 h-4 text-gray-400 rtl:rotate-180">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </a>
        </div>
    @endif

    <!-- Area Filters (Chips) -->
    <div class="px-4 py-4">
        <x-chip-group>
            <x-chip wire:key="area-all" :active="$selectedArea === null" wire:click="filterByArea(null)">
                {{ __('messages.home_filter_all') }}
            </x-chip>
            @foreach ($this->areas as $area)
                <x-chip wire:key="area-{{ $area->id }}" :active="$selectedArea === $area->id"
                    wire:click="filterByArea({{ $area->id }})">
                    {{ $area->name }}
                </x-chip>
            @endforeach
        </x-chip-group>
    </div>

    <!-- Main Content Area -->
    <div class="px-4 space-y-5">
        <div class="flex justify-end mb-2">
            <x-ios-select wire:model.live="sortBy" :options="['rating' => __('messages.home_rating_sort'), 'newest' => __('messages.home_newest_sort')]" class="w-40" />
        </div>

        <!-- Shops List (1 Column, Stacked) -->
        <div class="space-y-6">
            @forelse($this->shops as $shop)
                <a href="{{ route('shop.show', ['areaSlug' => $shop->area->slug, 'shopSlug' => $shop->slug]) }}"
                    wire:navigate wire:key="shop-{{ $shop->id }}"
                    class="block liquid-glass rounded-[2rem] overflow-hidden relative shadow-xl dark:shadow-2xl/20 border border-black/5 dark:border-white/10 group flex flex-col transform active:scale-[0.98] hover:scale-[0.99] transition-all duration-300">
                    @php
                        $banner = $shop->images->where('collection', 'banner')->first();
                        $logo = $shop->images->where('collection', 'logo')->first();
                    @endphp

                    <!-- Shorter Banner (Facebook Style) -->
                    <div class="h-28 w-full bg-gray-200 dark:bg-gray-800 relative flex-shrink-0">
                        @if ($banner)
                            <img src="{{ Storage::url($banner->path) }}" alt="{{ $shop->name }}"
                                class="w-full h-full object-cover">
                        @endif

                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>

                        <!-- Status Badge (Top Left - away from logo) -->
                        <div class="absolute top-3 left-3 z-10">
                            @if ($shop->is_online)
                                <x-badge color="success"
                                    class="liquid-glass rounded-xl shadow-md border-0 text-[10px] py-0.5">
                                    {{ __('messages.available') }}
                                </x-badge>
                            @else
                                <x-badge color="gray"
                                    class="liquid-glass rounded-xl shadow-md border-0 text-[10px] py-0.5">
                                    {{ __('messages.closed') }}
                                </x-badge>
                            @endif
                        </div>

                        <!-- View Count (Beautifully integrated in banner) -->
                        <div
                            class="absolute bottom-2 left-3 z-10 flex items-center gap-1 text-white/90 text-[10px] font-medium bg-black/20 backdrop-blur-sm px-2 py-0.5 rounded-xl border border-white/10">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="2.5" stroke="currentColor" class="w-3 h-3">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>
                            {{ number_format($shop->total_views) }}
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="p-4 pt-4 relative flex-1 flex flex-col">
                        <!-- Logo floating inside banner area (Moved to Right) -->
                        <div class="absolute -top-10 right-4 z-20">
                            @if ($logo)
                                <img src="{{ Storage::url($logo->path) }}" alt="{{ $shop->name }}"
                                    class="w-16 h-16 rounded-full object-cover border-4 border-white dark:border-[#1c1c1e] shadow-lg bg-white dark:bg-[#1c1c1e]">
                            @else
                                <div
                                    class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center border-4 border-white dark:border-[#1c1c1e] shadow-lg">
                                    <span
                                        class="text-xl text-gray-500 font-bold">{{ mb_substr($shop->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Content Row -->
                        <div class="flex justify-between items-start">
                            <div class="flex-1 mr-20 pl-2"> <!-- mr-20 to clear the logo on the right -->
                                <h3 class="text-base font-bold text-gray-900 dark:text-white leading-tight">
                                    {{ $shop->name }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                        class="w-3 h-3 text-fadebook-accent">
                                        <path fill-rule="evenodd"
                                            d="M9.69 18.933l.003.001C9.89 19.02 10 19 10 19s.11 0 .308-.066l.002-.001.006-.003.018-.008a5.741 5.741 0 00.281-.14c.186-.096.446-.24.757-.433.62-.384 1.445-.966 2.274-1.765C15.302 14.988 17 12.493 17 9A7 7 0 103 9c0 3.492 1.698 5.988 3.355 7.584a13.731 13.731 0 002.273 1.765 11.842 11.842 0 00.976.544l.062.029.018.008.006.003zM10 11.25a2.25 2.25 0 100-4.5 2.25 2.25 0 000 4.5z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    {{ $shop->area->name }}
                                </p>
                            </div>

                            <div class="flex flex-col items-center shrink-0">
                                <div
                                    class="flex items-center gap-1 bg-yellow-400/10 px-2 py-0.5 rounded-xl border border-yellow-400/20">
                                    <span
                                        class="text-xs font-bold text-yellow-600 dark:text-yellow-500">{{ (float) $shop->average_rating }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                        class="w-3 h-3 text-yellow-500">
                                        <path fill-rule="evenodd"
                                            d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <span class="text-[10px] text-gray-400 font-bold mt-1 text-center">
                                    {{ __('messages.home_review_count', ['count' => $shop->total_reviews ?? 0]) }}
                                </span>
                            </div>
                        </div>

                        <!-- Footer: Barber availability + Book Button -->
                        <div
                            class="mt-4 flex items-center justify-between pt-3 border-t border-gray-100 dark:border-gray-800">
                            <div class="flex items-center gap-1.5 text-[10px] text-gray-500 dark:text-gray-400">
                                <div class="flex -space-x-1.5 rtl:space-x-reverse">
                                    @foreach ($shop->barbers->take(3) as $barber)
                                        @if ($barber->images->isNotEmpty())
                                            <img src="{{ Storage::url($barber->images->first()->path) }}"
                                                alt="{{ $barber->name }}"
                                                class="w-5 h-5 rounded-full border border-white dark:border-gray-900 object-cover">
                                        @else
                                            <div
                                                class="w-5 h-5 rounded-full border border-white dark:border-gray-900 bg-gray-200 dark:bg-gray-800 flex items-center justify-center text-[7px] font-bold text-gray-600 dark:text-gray-300">
                                                {{ mb_substr($barber->name, 0, 1) }}
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                <span class="mr-1">
                                    {{ __('messages.home_barbers_available', ['count' => $shop->barbers->count()]) }}
                                </span>
                            </div>

                            <div
                                class="py-1.5 px-4 rounded-xl font-bold text-xs transition-all flex items-center gap-1 cursor-pointer {{ $shop->is_online ? 'bg-fadebook-accent text-white shadow-md' : 'bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500' }}">
                                {{ __('messages.book_now') }}
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="3" stroke="currentColor" class="w-3 h-3 rtl:rotate-180">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full">
                    <x-empty-state title="{{ __('messages.home_no_shops') }}"
                        description="{{ __('messages.home_no_shops_desc') }}">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m7.848 8.25 1.536.887M7.848 8.25a3 3 0 1 1-5.196-3 3 3 0 0 1 5.196 3Zm1.536.887a2.165 2.165 0 0 1 1.083 1.839c.005.351.054.695.14 1.024M9.384 9.137l2.077 1.199M7.848 15.75l1.536-.887m-1.536.887a3 3 0 1 1-5.196 3 3 3 0 0 1 5.196-3Zm1.536-.887a2.165 2.165 0 0 0 1.083-1.838c.005-.352.054-.695.14-1.025m-1.223 2.863 2.077-1.199m0-3.328a4.323 4.323 0 0 1 2.068-1.379l5.325-1.628a4.5 4.5 0 0 1 2.48-.044l.803.215-7.794 4.5m-2.882-1.664A4.33 4.33 0 0 0 10.607 12m3.736 0 7.794 4.5-.802.215a4.5 4.5 0 0 1-2.48-.043l-5.326-1.629a4.324 4.324 0 0 1-2.068-1.379M14.343 12l-2.882 1.664" />
                            </svg>
                        </x-slot>
                    </x-empty-state>
                </div>
            @endforelse
        </div>

        <!-- Infinite Scroll Sentinel -->
        @if ($this->hasMore)
            <div wire:key="sentinel-{{ $this->perPage }}" wire:intersect="loadMore"
                class="flex justify-center py-8">
                <div
                    class="flex items-center gap-2 px-4 py-2 rounded-full border border-black/5 dark:border-white/10 bg-white/40 dark:bg-white/5 backdrop-blur-xl">
                    <span class="text-xs font-bold text-gray-500">{{ __('messages.home_loading_more') }}</span>
                    <svg class="animate-spin h-4 w-4 text-fadebook-accent" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                </div>
            </div>
        @else
            <div wire:key="sentinel-end" class="flex justify-center py-12">
                <p class="text-[10px] text-gray-400 font-medium">{{ __('messages.home_end_of_list') }}</p>
            </div>
        @endif
    </div>
</div>
