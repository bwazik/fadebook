<div class="pb-[calc(5rem+var(--safe-area-bottom)+64px)] relative min-h-screen">
    <!-- Sticky Back Button -->
    <x-sticky-back-button href="{{ route('home') }}" />

    <!-- HERO HEADER SECTION (Gallery Carousel) -->
    <div x-data="{ activeIndex: 0 }" class="relative h-64 -mx-4 -mt-6 bg-gray-200 dark:bg-gray-800 overflow-hidden"
        style="width: calc(100% + 2rem)">
        @php
            $gallery = $this->galleryImages->isEmpty()
                ? collect([$shop->getImage('banner')->first()])->filter()
                : $this->galleryImages;
            if ($gallery->isEmpty()) {
                $gallery = collect([$shop->getImage('logo')->first()])->filter();
            }
        @endphp

        @if ($gallery->isNotEmpty())
            <div @scroll="activeIndex = Math.abs(Math.round($event.target.scrollLeft / $event.target.clientWidth))"
                class="flex overflow-x-auto snap-x snap-mandatory h-full w-full [scrollbar-width:none] [&::-webkit-scrollbar]:hidden relative group">
                @foreach ($gallery as $img)
                    <div class="snap-center shrink-0 w-full h-full relative">
                        <img src="{{ Storage::url($img->path) }}" alt="{{ $shop->name }}"
                            class="w-full h-full object-cover">
                    </div>
                @endforeach

            </div>
            <!-- Gradient Overlay for Readability -->
            <div
                class="absolute inset-0 bg-gradient-to-b from-black/60 via-transparent to-black/60 pointer-events-none">
            </div>

            <!-- Image count indicator -->
            @if ($gallery->count() > 1)
                <div
                    class="absolute bottom-6 left-6 z-20 bg-black/40 backdrop-blur-md px-3 py-1 rounded-full text-white text-[10px] font-bold border border-white/20 flex items-center shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-3 h-3 me-1.5 text-banhafade-accent">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                    </svg>
                    {{ $gallery->count() }} {{ __('messages.images') }}
                </div>

                <!-- Swipe Pagination Dots (Interactive) -->
                <div class="absolute bottom-6 right-6 z-20 flex items-center gap-1.5 pointer-events-none">
                    @foreach ($gallery as $img)
                        <div class="w-1.5 h-1.5 rounded-full transition-all duration-300"
                            :class="activeIndex === {{ $loop->index }} ? 'bg-white w-4' : 'bg-white/40'">
                        </div>
                    @endforeach
                </div>
            @endif
        @else
            <div
                class="absolute inset-0 bg-gradient-to-b from-black/60 via-transparent to-black/60 pointer-events-none">
            </div>
        @endif
    </div>

    <!-- SEAMLESS HEADER -->
    <div class="pt-8 pb-5">
        <div class="flex items-start justify-between gap-4">
            <!-- Shop Details -->
            <div class="flex-1 order-2">
                <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight leading-tight uppercase">
                    {{ $shop->name }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 font-bold mt-1.5 flex items-center gap-1.5">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                        class="w-4 h-4 text-banhafade-accent">
                        <path fill-rule="evenodd"
                            d="m9.69 18.933.003.001C9.89 19.02 10 19 10 19s.11 0 .308-.066l.002-.001.006-.003.018-.008a5.741 5.741 0 0 0 .281-.14c.186-.096.446-.24.757-.433.62-.384 1.445-.966 2.274-1.765C15.302 14.988 17 12.493 17 9A7 7 0 1 0 3 9c0 3.492 1.698 5.988 3.355 7.584a13.731 13.731 0 0 0 2.273 1.765 11.842 11.842 0 0 0 .976.544l.062.029.018.008.006.003zM10 11.25a2.25 2.25 0 1 0 0-4.5 2.25 2.25 0 0 0 0 4.5z"
                            clip-rule="evenodd" />
                    </svg>
                    {{ $shop->area->name }}
                </p>
            </div>

            <!-- Sleek Logo -->
            <div class="shrink-0 relative order-1">
                @php
                    $logo = $shop->getImage('logo')->first();
                @endphp
                @if ($logo)
                    <img src="{{ Storage::url($logo->path) }}" alt="{{ $shop->name }}"
                        class="w-16 h-16 rounded-full object-cover border border-black/5 dark:border-white/10 shadow-sm bg-white dark:bg-[#1c1c1e]">
                @else
                    <div
                        class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center border border-black/5 dark:border-white/10 shadow-sm">
                        <span class="text-2xl text-gray-400 font-black">{{ mb_substr($shop->name, 0, 1) }}</span>
                    </div>
                @endif

                @if ($shop->is_online)
                    <div
                        class="absolute -top-1 -right-1 w-4.5 h-4.5 bg-green-500 rounded-full border-2 border-white dark:border-[#121212] shadow-sm animate-pulse">
                    </div>
                @endif
            </div>
        </div>

        <!-- Scrollable Stats Bar -->
        <x-shop.carousel gap="gap-3" pb="pb-4" class="mt-6">
            <!-- Rating Stat -->
            <x-shop.stat-bubble>
                @if (($shop->total_reviews ?? 0) > 0 && (float) $shop->average_rating > 0)
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                        class="w-4 h-4 text-yellow-500">
                        <path fill-rule="evenodd"
                            d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z"
                            clip-rule="evenodd" />
                    </svg>
                    <span
                        class="text-sm font-black text-gray-900 dark:text-white">{{ (float) $shop->average_rating }}</span>
                    <span class="text-[10px] text-gray-500 font-bold ms-0.5">({{ $shop->total_reviews }})</span>
                @else
                    <div
                        class="bg-blue-500/10 p-1.5 rounded-lg border border-blue-500/20 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-4 h-4 text-blue-500">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                        </svg>
                    </div>
                @endif
            </x-shop.stat-bubble>

            <!-- Views Stat -->
            <x-shop.stat-bubble>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                    stroke="currentColor" class="w-4 h-4 text-gray-400">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                <span
                    class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tighter">{{ number_format($shop->total_views) }}</span>
            </x-shop.stat-bubble>

            <!-- Status Stat -->
            <x-shop.stat-bubble>
                @if ($shop->is_online)
                    <div class="w-2 h-2 rounded-full bg-green-500 shadow-sm shadow-green-500/50"></div>
                    <span
                        class="text-[11px] font-black text-gray-900 dark:text-white uppercase">{{ __('messages.available') }}</span>
                @else
                    <div class="w-2 h-2 rounded-full bg-red-400 shadow-sm shadow-red-400/50"></div>
                    <span
                        class="text-[11px] font-black text-gray-900 dark:text-white uppercase">{{ __('messages.unavailable') }}</span>
                @endif
            </x-shop.stat-bubble>

            @if ($shop->barbers->where('is_active', true)->count() > 0)
                <x-shop.stat-bubble>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                        stroke="currentColor" class="w-4 h-4 text-gray-400">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                    <span
                        class="text-[11px] text-gray-900 dark:text-white font-black uppercase tracking-tighter">{{ $shop->barbers->where('is_active', true)->count() }}</span>
                    <span
                        class="text-[10px] text-gray-500 font-bold uppercase ms-0.5">{{ __('messages.artists') }}</span>
                </x-shop.stat-bubble>
            @endif
        </x-shop.carousel>
    </div>

    <!-- INFO HUB -->
    <div class="mt-4 space-y-4">
        @if ($shop->description)
            <div x-data="{ expanded: false }">
                <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed font-medium italic transition-all duration-300"
                    :class="expanded ? '' : 'line-clamp-3'">
                    "{!! nl2br(e($shop->description)) !!}"
                </p>
                @if (mb_strlen($shop->description) > 80)
                    <button @click="expanded = !expanded"
                        class="mt-2 text-[10px] font-black text-banhafade-accent uppercase tracking-[0.2em] cursor-pointer flex items-center gap-1">
                        <span
                            x-text="expanded ? '{{ __('messages.hide') }}' : '{{ __('messages.read_more') }}'"></span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3.5"
                            stroke="currentColor" class="w-2.5 h-2.5 transition-transform"
                            :class="expanded ? 'rotate-180' : ''">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                @endif
            </div>
        @endif

        <div class="space-y-3.5">
            <!-- Address Row -->
            <div class="flex items-start gap-4">
                <div
                    class="w-11 h-11 rounded-[1rem] bg-white dark:bg-gray-800 shadow-sm flex items-center justify-center shrink-0 border border-black/5">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5.5 h-5.5 text-gray-400">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                    </svg>
                </div>
                <div class="flex-1 pt-1.5">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">
                        {{ __('messages.address') }}</p>
                    <p class="text-sm font-bold text-gray-700 dark:text-gray-300 mt-1.5">{{ $shop->address }}</p>
                </div>
            </div>

            <!-- Phone Row -->
            <div class="flex items-start gap-4">
                <div
                    class="w-11 h-11 rounded-[1rem] bg-white dark:bg-gray-800 shadow-sm flex items-center justify-center shrink-0 border border-black/5">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5.5 h-5.5 text-gray-400">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                    </svg>
                </div>
                <div class="flex-1 pt-1.5">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">
                        {{ __('messages.contact') }}</p>
                    <a href="tel:{{ $shop->phone }}"
                        class="text-sm font-black text-banhafade-accent mt-1.5 inline-block liquid-button">{{ $shop->phone }}</a>
                </div>
            </div>

            <!-- Working Hours (Interactive) -->
            <div class="flex items-start gap-4" x-data="{ expanded: false }">
                <div
                    class="w-11 h-11 rounded-[1rem] bg-white dark:bg-gray-800 shadow-sm flex items-center justify-center shrink-0 border border-black/5">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-5.5 h-5.5 text-gray-400">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <div class="flex-1 pt-1.5">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none">
                            {{ __('messages.working_hours') }}</p>
                        <button @click="expanded = !expanded"
                            class="text-banhafade-accent text-xs font-black uppercase cursor-pointer">
                            <span
                                x-text="expanded ? '{{ __('messages.hide') }}' : '{{ __('messages.view_all') }}'"></span>
                        </button>
                    </div>

                    <!-- Show today + status when collapsed -->
                    <div x-show="!expanded" class="mt-2" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-1">
                        <div class="flex justify-between text-sm font-bold text-gray-900 dark:text-white">
                            <span>{{ __('messages.today') }}
                                ({{ __('messages.day_' . strtolower(now()->englishDayOfWeek)) }})</span>
                            <span
                                class="uppercase tracking-tighter {{ $shop->is_online ? 'text-green-500' : 'text-red-400' }}">
                                {{ $this->getFormattedHours(strtolower(now()->englishDayOfWeek)) }}
                            </span>
                        </div>
                    </div>

                    <!-- Full schedule when expanded -->
                    <div x-show="expanded" x-collapse class="mt-2 space-y-1.5">
                        @foreach (['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday'] as $dayKey)
                            <div
                                class="flex justify-between text-xs font-bold {{ $openingHours[$dayKey] ?? null ? 'text-gray-600 dark:text-gray-400' : 'text-red-400 opacity-70' }}">
                                <span class="capitalize">{{ __('messages.day_' . $dayKey) }}</span>
                                <span class="uppercase tracking-tighter">
                                    {{ $this->getFormattedHours($dayKey) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ARTISTS SECTION -->
    @if ($shop->barbers->where('is_active', true)->isNotEmpty())
        <div class="mt-12">
            <div class="px-2 mb-4">
                <h2 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">
                    {{ __('messages.banhafade_artists') }}</h2>
            </div>
            <x-shop.carousel gap="gap-4" pb="pb-6">
                @foreach ($shop->barbers->where('is_active', true) as $barber)
                    <x-shop.barber-card :barber="$barber" :totalServices="$shop->services->count()" wire:key="barber-{{ $barber->id }}" />
                @endforeach
            </x-shop.carousel>
        </div>
    @endif

    <!-- SERVICES MENU -->
    <div class="mt-8">
        <div class="flex items-center justify-between mb-2 px-2">
            <h2 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">
                {{ __('messages.services') }}</h2>
            <span
                class="text-[10px] font-black text-gray-400 bg-black/5 dark:bg-white/5 px-3 py-1 rounded-full uppercase tracking-[0.2em]">{{ $shop->services->count() }}
                {{ __('messages.services_ar') }}</span>
        </div>

        <!-- Category Chip Filter -->
        <div class="px-2 mb-6">
            <x-chip-group>
                <x-chip wire:key="cat-all" :active="$selectedCategory === null" wire:click="filterByServiceCategory(null)">
                    {{ __('messages.all') }}
                </x-chip>
                @foreach ($shop->serviceCategories as $category)
                    <x-chip wire:key="cat-{{ $category->id }}" :active="$selectedCategory === $category->id"
                        wire:click="filterByServiceCategory({{ $category->id }})">
                        {{ $category->name }}
                    </x-chip>
                @endforeach
            </x-chip-group>
        </div>

        <div class="space-y-8">
            @forelse($this->filteredServices->groupBy(fn($s) => $s->category?->name ?? __('messages.other')) as $categoryName => $services)
                <div class="space-y-3">
                    <div class="flex items-center gap-3 px-2">
                        <h3 class="text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                            {{ $categoryName }}</h3>
                        <div class="flex-1 h-px bg-black/5 dark:bg-white/5"></div>
                    </div>

                    <div class="space-y-4">
                        @foreach ($services as $service)
                            <div wire:key="service-{{ $service->id }}"
                                @if (!($shop->is_online && $service->is_active)) wire:click="showServiceBlockedToast({{ $shop->is_online ? 'true' : 'false' }}, {{ $service->is_active ? 'true' : 'false' }})" @endif>
                                <x-shop.service-card :service="$service" :selected="false" :href="$shop->is_online && $service->is_active
                                    ? route('booking.create', ['shopSlug' => $shop->slug, 'serviceId' => $service->id])
                                    : null"
                                    :unavailable="!($shop->is_online && $service->is_active)" :show-prices="$shop->show_service_prices" />
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="w-full">
                    <x-empty-state title="{{ __('messages.no_services_found') }}"
                        description="{{ __('messages.no_services_found_desc') }}">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m7.848 8.25 1.536.887M7.848 8.25a3 3 0 1 1-5.196-3 3 3 0 0 1 5.196 3Zm1.536.887a2.165 2.165 0 0 1 1.083 1.839c.005.351.054.695.14 1.024M9.384 9.137l2.077 1.199M7.848 15.75l1.536-.887m-1.536.887a3 3 0 1 1-5.196 3 3 3 0 0 1 5.196-3Zm1.536-.887a2.165 2.165 0 0 0 1.083-1.838c.005-.352.054-.695.14-1.025m-1.223 2.863 2.077-1.199m0-3.328a4.323 4.323 0 0 1 2.068-1.379l5.325-1.628a4.5 4.5 0 0 1 2.48-.044l.803.215-7.794 4.5m-2.882-1.664A4.33 4.33 0 0 0 10.607 12m3.736 0 7.794 4.5-.802.215a4.5 4.5 0 0 1-2.48-.043l-5.326-1.629a4.324 4.324 0 0 1-2.068-1.379M14.343 12l-2.882 1.664" />
                            </svg>
                        </x-slot>
                    </x-empty-state>
                </div>
            @endforelse
        </div>
    </div>

    <!-- WORDS REVIEWS -->
    <div class="mt-12 pb-12">
        <div class="flex items-center justify-between mb-6 px-1">
            <div>
                <h2 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">
                    {{ __('messages.true_word') }}</h2>
                <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1.5">
                    {{ __('messages.real_vibes') }}</p>
            </div>
            <x-ios-select wire:model.live="reviewSort" :options="[
                'newest' => __('messages.sort_newest'),
                'highest' => __('messages.sort_highest'),
                'lowest' => __('messages.sort_lowest'),
            ]" class="w-36" />
        </div>

        <x-shop.carousel wire:loading.class="opacity-60 transition-opacity">
            @forelse($this->sortedReviews as $review)
                <x-shop.review-card :review="$review" wire:key="review-{{ $review->id }}" />
            @empty
                <div class="w-full">
                    <x-empty-state title="{{ __('messages.no_reviews_yet') }}"
                        description="{{ __('messages.real_vibes') }}">
                        <x-slot name="icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.052-7.043.151C3.373 3.385 2.25 4.778 2.25 6.38v5.23Z" />
                            </svg>
                        </x-slot>
                    </x-empty-state>
                </div>
            @endforelse

            <!-- Horizontal Sentinel -->
            @if ($this->hasMoreReviews)
                <div wire:key="sentinel-{{ $this->reviewsPerPage }}" wire:intersect="loadMoreReviews"
                    class="snap-start shrink-0 w-24 flex items-center justify-center">
                    <div
                        class="w-12 h-12 rounded-full liquid-glass flex items-center justify-center animate-pulse border-white/40">
                        <svg class="animate-spin h-5 w-5 text-banhafade-accent" xmlns="http://www.w3.org/2000/svg"
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
        </x-shop.carousel>
    </div>

    <!-- FLOATING BOOKING CAPSULE -->
    <div
        class="fixed bottom-[calc(2rem+env(safe-area-inset-bottom))] left-1/2 -translate-x-1/2 z-[70] w-[calc(100%-2.5rem)] max-w-[360px]
                transition-all duration-300 ease-out">
        <div class="flex items-center p-1.5 rounded-[2rem] liquid-glass relative">
            <!-- Glass gradient overlay (Identical to bottom nav) -->
            <div
                class="absolute inset-0 rounded-[2rem] pointer-events-none bg-gradient-to-b from-white/70 via-white/20 to-white/5 dark:from-white/10 dark:via-white/5 dark:to-transparent">
            </div>

            <!-- Price Info -->
            @if ($shop->show_service_prices)
            <div class="shrink-0 px-4 relative z-10 flex flex-col justify-center">
                <p
                    class="text-[9px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest leading-none mb-1">
                    {{ __('messages.starts_from') }}</p>
                <p class="text-base font-black text-gray-900 dark:text-white tracking-tighter leading-none">
                    {{ number_format($this->startingPrice, 0) }} <small
                        class="text-[10px] font-bold">{{ __('messages.egp') }}</small>
                </p>
            </div>
            @endif

            <!-- Action Button -->
            <div class="flex-1 relative z-10">
                @auth
                    @if ($shop->is_online)
                        <a href="{{ route('booking.create', $shop->slug) }}" wire:navigate class="block w-full">
                            <button
                                class="w-full h-12 rounded-[1.5rem] bg-banhafade-accent text-white text-[11px] font-black uppercase tracking-[0.1em] cursor-pointer transition-all active:scale-[0.98] shadow-md shadow-banhafade-accent/30">
                                {{ __('messages.book_your_spot_now') }}
                            </button>
                        </a>
                    @else
                        <button
                            class="w-full h-12 rounded-[1.5rem] bg-gray-400/20 dark:bg-gray-700/50 text-gray-500 dark:text-gray-400 text-[11px] font-black uppercase tracking-[0.1em] cursor-not-allowed transition-all"
                            disabled>
                            {{ __('messages.closed_now') }}
                        </button>
                    @endif
                @else
                    <a href="{{ route('login') }}" wire:navigate class="block w-full">
                        <button
                            class="w-full h-12 rounded-[1.5rem] bg-banhafade-accent text-white text-[11px] font-black cursor-pointer transition-all active:scale-[0.98] shadow-md shadow-banhafade-accent/30">
                            {{ __('messages.login_to_book') }}
                        </button>
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Spacers -->
    <div class="h-20"></div>
</div>
