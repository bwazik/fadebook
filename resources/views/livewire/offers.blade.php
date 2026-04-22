@section('title', __('messages.offers_title'))
@section('canonical', route('offers'))

<div class="pb-[calc(5rem+var(--safe-area-bottom)+64px)] min-h-screen pt-4 px-4 relative">
    {{-- ═══════════════════════════════ --}}
    {{-- 1. HEADING & DISCOVERY HEADER   --}}
    {{-- ═══════════════════════════════ --}}
    <div class="mb-6 mt-4">
        <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">
            {{ __('messages.offers_title') }}
        </h1>
    </div>

    {{-- ═══════════════════════════════ --}}
    {{-- 2. FEATURED OFFERS CAROUSEL     --}}
    {{-- ═══════════════════════════════ --}}
    @if ($promotionalOffers->where('is_active', true)->take(5)->isNotEmpty())
        <div class="mb-10">
            <div class="flex overflow-x-auto gap-4 pb-4 no-scrollbar -mx-4 px-4 snap-x">
                @foreach ($promotionalOffers->take(5) as $offer)
                    <div wire:key="featured-{{ $offer->id }}" wire:click="openOffer({{ $offer->id }})"
                        class="flex-shrink-0 w-[85vw] md:w-[400px] snap-center relative aspect-[16/9] rounded-[2.5rem] overflow-hidden group cursor-pointer shadow-2xl transition-transform active:scale-[0.97]">

                        {{-- Background Image (Shop Banner) --}}
                        @if ($offer->shop->banner_url)
                            <img src="{{ $offer->shop->banner_url }}"
                                class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                alt="">
                        @else
                            <div class="absolute inset-0 bg-gradient-to-br from-banhafade-accent/40 to-purple-600/40">
                            </div>
                        @endif

                        {{-- Overlay Gradient --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/30 to-transparent"></div>

                        {{-- Content --}}
                        <div class="absolute inset-0 p-6 flex flex-col justify-end">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-8 h-8 rounded-full border border-white/20 overflow-hidden shadow-lg">
                                    <img src="{{ $offer->shop->logo_url }}" class="w-full h-full object-cover">
                                </div>
                                <span
                                    class="text-[10px] font-black text-white/80 uppercase tracking-widest">{{ $offer->shop->name }}</span>
                            </div>

                            <h3
                                class="text-xl font-black text-white leading-tight mb-2 group-hover:translate-x-1 transition-transform">
                                {{ $offer->title }}
                            </h3>

                            <div class="flex items-center justify-between">
                                <div
                                    class="px-3 py-1 rounded-full bg-white/20 backdrop-blur-md border border-white/20 text-white text-[10px] font-black uppercase">
                                    @if ($offer->coupon)
                                        @if ($offer->coupon->discount_type === \App\Enums\DiscountType::Percentage)
                                            {{ __('messages.offers_discount_percentage', ['value' => $offer->coupon->discount_value]) }}
                                        @else
                                            @if ($offer->shop->show_service_prices)
                                                {{ __('messages.offers_discount_fixed', ['value' => $offer->coupon->discount_value]) }}
                                            @else
                                                {{ __('messages.offers_discount_hidden') }}
                                            @endif
                                        @endif
                                    @else
                                        {{ __('messages.offers_featured_reward') }}
                                    @endif
                                </div>
                                <div class="text-[9px] font-bold text-white/60">
                                    {{ $offer->end_date ? __('messages.offers_ends_label') . ' ' . $offer->end_date->diffForHumans() : '' }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════ --}}
    {{-- 3. EXPLORE CATEGORIES / TABS    --}}
    {{-- ═══════════════════════════════ --}}
    <div class="px-4 space-y-8 mt-2">
        <x-section-header :title="__('messages.offers_discovery_title')" />

        {{-- Discovery List --}}
        <div class="space-y-6">
            @forelse($promotionalOffers as $offer)
                <div wire:key="offer-{{ $offer->id }}" wire:click="openOffer({{ $offer->id }})"
                    class="group flex items-center gap-4 p-4 rounded-[2rem] liquid-glass border border-white/40 dark:border-white/5 shadow-xl transition-all hover:translate-y-[-4px] active:scale-[0.98] cursor-pointer">

                    {{-- Small Visual --}}
                    <div class="flex-shrink-0 w-24 h-24 rounded-3xl overflow-hidden relative shadow-inner">
                        <img src="{{ $offer->shop->logo_url }}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent"></div>
                        {{-- Discount Badge --}}
                        <div
                            class="absolute top-2 left-2 px-1.5 py-0.5 rounded-lg bg-banhafade-accent text-white text-[8px] font-black shadow-lg">
                            @if ($offer->coupon)
                                @if ($offer->coupon->discount_type === \App\Enums\DiscountType::Percentage)
                                    -{{ $offer->coupon->discount_value }}%
                                @else
                                    @if ($offer->shop->show_service_prices)
                                        -{{ $offer->coupon->discount_value }} {{ __('messages.egp') }}
                                    @else
                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                            </path>
                                        </svg>
                                    @endif
                                @endif
                            @else
                                <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            @endif
                        </div>
                    </div>

                    <div class="flex-grow min-w-0 space-y-1">
                        <div class="flex items-center gap-1.5">
                            <span
                                class="text-[9px] font-black text-banhafade-accent uppercase tracking-wider">{{ $offer->shop->name }}</span>
                            <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-white/20"></span>
                            <span
                                class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">{{ $offer->shop->area->name }}</span>
                        </div>

                        <h4
                            class="text-sm font-black text-gray-900 dark:text-white leading-snug group-hover:text-banhafade-accent transition-colors">
                            {{ $offer->title }}
                        </h4>

                        <p class="text-[10px] text-gray-500 dark:text-gray-400 font-medium line-clamp-1">
                            {{ $offer->description }}
                        </p>

                        <div class="flex items-center justify-between pt-2">
                            <span class="text-[9px] font-bold text-gray-400">
                                {{ $offer->end_date ? $offer->end_date->translatedFormat('d M') : '' }}
                            </span>
                            <div
                                class="w-7 h-7 rounded-full bg-black/5 dark:bg-white/5 flex items-center justify-center text-gray-400 group-hover:bg-banhafade-accent group-hover:text-white transition-all">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <x-empty-state title="{{ __('messages.offers_no_shop_offers') }}"
                    description="{{ __('messages.offers_no_shop_offers_sub') }}">
                    <x-slot name="icon">
                        <svg class="w-12 h-12 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z">
                            </path>
                        </svg>
                    </x-slot>
                </x-empty-state>
            @endforelse
        </div>
    </div>

    {{-- ═══════════════════════════════ --}}
    {{-- 4. DETAILS BOTTOM SHEET        --}}
    {{-- ═══════════════════════════════ --}}
    <x-bottom-sheet wire:model="selectedOfferId" title="{{ __('messages.offers_modal_details') }}">
        <x-slot:icon>
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z">
                </path>
            </svg>
        </x-slot:icon>

        @if ($selectedOffer)
            <div class="space-y-8 animate-in zoom-in-95 duration-300">
                {{-- Shop Header Card --}}
                <div class="relative rounded-[2.5rem] overflow-hidden group">
                    <div class="h-40 bg-gray-200 dark:bg-gray-800">
                        @if ($selectedOffer->shop->banner_url)
                            <img src="{{ $selectedOffer->shop->banner_url }}" class="w-full h-full object-cover">
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent"></div>
                    </div>

                    <div class="absolute bottom-6 left-6 right-6 flex items-center gap-4">
                        <div
                            class="w-14 h-14 rounded-2xl border-4 border-white dark:border-gray-900 shadow-xl overflow-hidden shrink-0">
                            <img src="{{ $selectedOffer->shop->logo_url }}" class="w-full h-full object-cover">
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-lg font-black text-white leading-none">{{ $selectedOffer->shop->name }}
                            </h3>
                            <p class="text-white/60 text-[10px] font-bold uppercase mt-1">
                                {{ $selectedOffer->shop->area->name }}</p>
                        </div>
                        <a href="{{ route('shop.show', ['areaSlug' => $selectedOffer->shop->area?->slug ?? 'default', 'shopSlug' => $selectedOffer->shop->slug ?? 'default']) }}"
                            wire:navigate
                            class="ms-auto w-10 h-10 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white border border-white/20 active:scale-90 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                    d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- Offer Body --}}
                <div class="text-center space-y-2 px-2">
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white leading-tight">
                        {{ $selectedOffer->title }}</h2>
                    <p class="text-sm font-bold text-gray-500 dark:text-gray-400 leading-relaxed">
                        {{ $selectedOffer->description }}</p>
                </div>

                {{-- Stats Grid --}}
                <div class="grid grid-cols-2 gap-4">
                    <div
                        class="p-5 rounded-[2rem] bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/5 flex flex-col items-center justify-center space-y-1">
                        <span
                            class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">{{ __('messages.offers_modal_discount') }}</span>
                        <span class="text-2xl font-black text-banhafade-accent">
                            @if ($selectedOffer->coupon)
                                @if ($selectedOffer->coupon->discount_type === \App\Enums\DiscountType::Percentage)
                                    {{ $selectedOffer->coupon->discount_value }}%
                                @else
                                    @if ($selectedOffer->shop->show_service_prices)
                                        {{ $selectedOffer->coupon->discount_value }} {{ __('messages.egp') }}
                                    @else
                                        <span class="text-xs font-black text-banhafade-accent/80 text-nowrap">{{ __('messages.offers_discount_hidden') }}</span>
                                    @endif
                                @endif
                            @else
                                <span
                                    class="text-xl font-black text-banhafade-accent/80 text-nowrap">{{ __('messages.offers_referral_reward_label') }}</span>
                            @endif
                        </span>
                    </div>
                    <div
                        class="p-5 rounded-[2rem] bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/5 flex flex-col items-center justify-center space-y-1">
                        <span
                            class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">{{ __('messages.offers_modal_expires') }}</span>
                        <span class="text-sm font-black text-gray-700 dark:text-gray-300">
                            @if ($selectedOffer->end_date)
                                {{ $selectedOffer->end_date->translatedFormat('d F Y') }}
                            @else
                                <span class="text-2xl leading-none">∞</span>
                            @endif
                        </span>
                    </div>
                </div>

                {{-- Action Card: Voucher OR Referral --}}
                @if ($selectedOffer->coupon)
                    <div class="relative group">
                        {{-- Coupon Card (Compact & Elegant) --}}
                        <div
                            class="relative flex items-center bg-gradient-to-br from-indigo-600 to-purple-700 rounded-3xl p-6 overflow-hidden border border-white/20 shadow-2xl">
                            {{-- Punch holes --}}
                            <div
                                class="absolute -left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-white dark:bg-gray-900 shadow-inner">
                            </div>
                            <div
                                class="absolute -right-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-white dark:bg-gray-900 shadow-inner">
                            </div>

                            {{-- Left: Code --}}
                            <div class="flex-grow ps-2">
                                <span
                                    class="text-[9px] font-black text-white/50 uppercase tracking-[0.2em] block mb-1">{{ __('messages.offers_modal_code') }}</span>
                                <div class="text-2xl font-black text-white tracking-widest">
                                    {{ $selectedOffer->coupon->code }}</div>
                            </div>

                            {{-- Center: Divider --}}
                            <div class="h-10 w-px bg-dashed border-l-2 border-dashed border-white/20 mx-4"></div>

                            {{-- Right: Copy --}}
                            <div class="shrink-0 pe-2">
                                <x-copy-button :value="$selectedOffer->coupon->code"
                                    class="!bg-white !text-indigo-600 !w-12 !h-12 !rounded-2xl shadow-lg border-0 cursor-pointer" />
                            </div>
                        </div>
                    </div>
                @else
                    <div
                        class="relative p-6 rounded-[2.5rem] bg-gradient-to-br from-banhafade-accent to-pink-600 border border-white/20 shadow-xl overflow-hidden group">
                        {{-- Decorative --}}
                        <div class="absolute -top-10 -right-10 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>

                        <div class="relative z-10 flex flex-col items-center text-center space-y-4">
                            <div
                                class="w-12 h-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4z">
                                    </path>
                                </svg>
                            </div>
                            <div class="space-y-1">
                                <h4 class="text-lg font-black text-white tracking-tight leading-none">
                                    {{ __('messages.offers_referral_title') }}</h4>
                                <p class="text-[10px] font-bold text-white/70 leading-relaxed px-4">
                                    {{ __('messages.offers_referral_desc') }}</p>
                            </div>
                            <x-ios-button href="{{ route('profile.referral') }}" wire:navigate
                                class="!h-10 px-6 bg-white !text-banhafade-accent !rounded-xl font-black shadow-xl active:scale-95 transition-all text-[11px] uppercase">
                                {{ __('messages.offers_referral_btn') }}
                            </x-ios-button>
                        </div>
                    </div>
                @endif

                <p class="text-[10px] font-medium text-gray-400 text-center px-8">
                    @if ($selectedOffer->coupon)
                        {{ __('messages.offers_modal_use_at_footer', ['shop' => $selectedOffer->shop->name]) }}
                    @else
                        {{ __('messages.offers_referral_footer_hint') }}
                    @endif
                </p>
            </div>
        @endif
    </x-bottom-sheet>
</div>
