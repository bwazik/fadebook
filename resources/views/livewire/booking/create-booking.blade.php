<div class="pb-[calc(5rem+var(--safe-area-bottom)+64px)] relative min-h-screen pt-4 px-4" x-data="{
    step: @entangle('step'),
    selectedBarberId: @entangle('selectedBarberId'),
    selectedDate: @entangle('selectedDate'),
    selectedSlot: @entangle('selectedSlot')
}">
    <!-- Sticky Back Button -->
    <x-sticky-back-button wire:click="goBack" />

    @if (!$shop->is_online)
        <div class="mt-8 liquid-glass rounded-2xl p-8 border border-red-400/20 bg-red-400/5 text-center space-y-4">
            <div class="w-16 h-16 mx-auto rounded-full bg-red-400/10 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-8 h-8 text-red-500">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
            </div>
            <h2 class="text-xl font-black text-gray-900 dark:text-white">{{ __('messages.shop_closed') }}</h2>
            <p class="text-sm text-gray-500 font-bold">{{ __('messages.booking_shop_offline') }}</p>
            <x-ios-button href="{{ route('shop.show', ['areaSlug' => $shop->area->slug, 'shopSlug' => $shop->slug]) }}"
                wire:navigate>
                {{ __('messages.back_to_shop') }}
            </x-ios-button>
        </div>
    @else
        <div class="mb-6 mt-4">
            <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">
                {{ __('messages.booking_title') }}
            </h1>
            <p class="text-sm text-gray-500 font-bold mt-1">
                {{ $shop->name }}
            </p>
        </div>

        <!-- Step Progress Indicator -->
        <x-booking.step-indicator :step="$step" :total="$this->totalSteps" />

        <!-- Step 1: Select Service -->
        <div x-show="step === 1" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0"
            style="display: none;">
            <h2 class="text-lg font-black text-gray-900 dark:text-white mb-4">
                {{ __('messages.booking_select_service') }}</h2>

            <!-- Category Chip Filter -->
            <div class="mb-6">
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
                        <div class="flex items-center gap-3 px-1">
                            <h3
                                class="text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                                {{ $categoryName }}</h3>
                            <div class="flex-1 h-px bg-black/5 dark:bg-white/5"></div>
                        </div>

                        <div class="space-y-4">
                            @foreach ($services as $service)
                                <div wire:key="service-{{ $service->id }}"
                                    @if ($service->is_active) @click="$wire.selectService({{ $service->id }})"
                                    @else
                                    wire:click="showServiceBlockedToast({{ $shop->is_online ? 'true' : 'false' }}, false)" @endif>
                                    <x-shop.service-card :service="$service" :selected="$selectedServiceId === $service->id" :unavailable="!$service->is_active" :show-prices="$shop->show_service_prices" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="w-full">
                        <x-empty-state title="{{ __('messages.booking_no_services') }}"
                            description="{{ __('messages.booking_no_services_desc') }}">
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

        <!-- Step 2: Select Barber -->
        <div x-show="step === 2" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0"
            style="display: none;">
            <h2 class="text-lg font-black text-gray-900 dark:text-white mb-4">
                {{ __('messages.booking_select_barber') }}</h2>
            <div class="space-y-3">
                @forelse($this->availableBarbers as $barber)
                    <x-booking.barber-card wire:key="barber-{{ $barber->id }}" :barber="$barber"
                        :totalServices="$shop->services->count()"
                        :selected="'selectedBarberId === ' . $barber->id"
                        wire:click="selectBarber({{ $barber->id }})"
                    />
                @empty
                    <div class="w-full">
                        <x-empty-state title="{{ __('messages.booking_no_barbers') }}"
                            description="{{ __('messages.booking_no_barbers_desc') }}">
                            <x-slot name="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                </svg>
                            </x-slot>
                        </x-empty-state>
                    </div>
@endforelse
            </div>
        </div>

        <!-- Step 3: Select Date & Time -->
        <div x-show="step === 3" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0"
            style="display: none;">

            <header class="mb-6">
                <h2 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">
                    {{ __('messages.booking_select_date_time') }}</h2>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mt-1">
                    {{ __('messages.available_slots_hint') }}</p>
            </header>

            <!-- Date Selector (Premium Horizontal Scroll) -->
            <div class="flex overflow-x-auto gap-3 pb-6 no-scrollbar -mx-4 px-4 snap-x">
                @for ($i = 0; $i < min(14, $shop->advance_booking_days ?? 30); $i++)
                    @php $date = now()->addDays($i); @endphp
                    <x-booking.date-card
                        wire:key="date-{{ $date->format('Y-m-d') }}"
                        :date="$date"
                        :active="'selectedDate === \'' .
                        $date->format('Y-m-d') .
                        '\''"
                        wire:click="selectDate('{{ $date->format('Y-m-d') }}')" />
                @endfor
            </div>

            @if ($selectedDate)
                <div class="space-y-8 mt-2 pb-10">
                    @php $hasSlots = false; @endphp
                    @foreach (['morning', 'afternoon', 'evening'] as $period)
                        @if (!empty($this->groupedSlots[$period]))
                            @php $hasSlots = true; @endphp
                            <section x-data="{ open: true }" class="space-y-4">
                                <div class="flex items-center gap-2 px-1">
                                    <div
                                        class="w-7 h-7 rounded-full liquid-glass flex items-center justify-center border border-white/20 shadow-sm">
                                        @if ($period === 'morning')
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="2.5" stroke="currentColor"
                                                class="w-3.5 h-3.5 text-orange-400">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 3v2.25m6.364.386l-1.591 1.591M21 12h-2.25m-.386 6.364l-1.591-1.591M12 18.75V21m-4.773-4.227l-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                                            </svg>
                                        @elseif($period === 'afternoon')
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="2.5" stroke="currentColor"
                                                class="w-3.5 h-3.5 text-blue-400">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-.778.099-1.533.284-2.253" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="2.5" stroke="currentColor"
                                                class="w-3.5 h-3.5 text-indigo-400">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                                            </svg>
                                        @endif
                                    </div>
                                    <h3
                                        class="text-[11px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-[0.15em]">
                                        {{ __('messages.period_' . $period) }}
                                    </h3>
                                    <div class="flex-1 h-px bg-black/5 dark:bg-white/5 ms-2 rounded-full"></div>
                                    <span
                                        class="text-[9px] font-bold text-gray-400 opacity-60">{{ count($this->groupedSlots[$period]) }}
                                        {{ __('messages.slots') }}</span>
                                </div>

                                <div class="grid grid-cols-3 gap-2.5 px-1">
                                    @foreach ($this->groupedSlots[$period] as $slot)
                                        <x-booking.slot-card wire:key="slot-{{ $slot }}" :time="$slot"
                                            :active="'selectedSlot === \'' . $slot . '\''" wire:click="selectSlot('{{ $slot }}')" />
                                    @endforeach
                                </div>
                            </section>
                        @endif
                    @endforeach

                    @if (!$hasSlots)
                        <x-empty-state title="{{ __('messages.booking_no_slots') }}"
                            description="{{ __('messages.booking_empty_state_hint') }}">
                            <x-slot name="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor" class="w-8 h-8 opacity-60">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </x-slot>
                        </x-empty-state>
                    @endif
                </div>
            @else
                <x-empty-state title="{{ __('messages.booking_select_date_prompt') }}"
                    description="{{ __('messages.booking_select_date_prompt_desc') }}">
                    <x-slot name="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-8 h-8 text-banhafade-accent">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                        </svg>
                    </x-slot>
                </x-empty-state>
            @endif
        </div>

        <!-- Step 4: Review Booking -->
        <div x-show="step === 4" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0"
            style="display: none;">
            <h2 class="text-lg font-black text-gray-900 dark:text-white mb-4">
                {{ __('messages.booking_confirm_title') }}</h2>

            <div class="liquid-glass rounded-[1.5rem] p-5 border border-white/20 shadow-sm mb-6 space-y-4">
                <x-booking.summary-row :label="__('messages.booking_label_shop')" :value="$shop->name" border />
                <x-booking.summary-row :label="__('messages.booking_label_service')" :value="$this->shop->services->firstWhere('id', $selectedServiceId)?->name" border />
                <x-booking.summary-row :label="__('messages.booking_label_barber')" :value="$this->availableBarbers->firstWhere('id', $selectedBarberId)?->name ??
                    __('messages.any_barber')" border />
                <x-booking.summary-row :label="__('messages.booking_label_date')" :value="\Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y')" border />
                <x-booking.summary-row :label="__('messages.booking_label_time')" :value="\Carbon\Carbon::parse($selectedSlot)->format('g:i') . ' ' . (__(\Carbon\Carbon::parse($selectedSlot)->format('a') === 'am' ? 'messages.time_am' : 'messages.time_pm'))" border />

                @if ($shop->show_service_prices)
                <div class="pt-2 border-t border-gray-100 dark:border-gray-800 space-y-2">
                    <x-booking.summary-row :label="__('messages.booking_label_total')" :value="number_format($this->totalBeforeDiscount, 0) . ' ' . __('messages.egp')" :class="$discountAmount > 0 ? 'line-through opacity-50' : ''" />

                    <x-booking.summary-row :label="__('messages.tax')" :value="'0 ' . __('messages.egp')" />

                    @if ($discountAmount > 0)
                        <div class="transition-all animate-in fade-in slide-in-from-top-1">
                            <x-booking.summary-row :label="__('messages.booking_discount')" :value="'-' . number_format($discountAmount, 0) . ' ' . __('messages.egp')"
                                value-class="text-green-500" />
                        </div>
                    @endif

                    <div class="flex justify-between items-center pt-2 border-t border-gray-100 dark:border-gray-800">
                        <span
                            class="text-sm text-gray-900 dark:text-white font-black">{{ __('messages.booking_label_final_total') }}</span>
                        <span class="text-xl font-black text-banhafade-accent">
                            {{ number_format($finalAmount, 0) }} <small
                                class="text-[10px]">{{ __('messages.egp') }}</small>
                        </span>
                    </div>
                </div>
                @endif
            </div>

            {{-- Coupon Section --}}
            <div class="mb-8">
                <div class="flex gap-3">
                    <div class="flex-1">
                        <x-ios-input type="text" wire:model="couponCode" dir="ltr"
                            placeholder="{{ __('messages.booking_coupon_placeholder') }}" />
                    </div>
                    @if ($selectedCouponId)
                        <button wire:click="removeCoupon"
                            class="w-12 h-12 rounded-2xl bg-red-500/10 text-red-500 flex items-center justify-center border border-red-500/20 active:scale-95 transition-transform cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    @else
                        <div class="w-24">
                            <x-ios-button wire:click="applyCoupon" target="applyCoupon" variant="secondary"
                                class="!py-3.5">
                                <span wire:loading.remove
                                    wire:target="applyCoupon">{{ __('messages.booking_apply_button') }}</span>
                                <span wire:loading wire:target="applyCoupon"
                                    class="w-4 h-4 border-2 border-current border-t-transparent rounded-full animate-spin"></span>
                            </x-ios-button>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mb-8 py-4 px-2">
                <div class="flex items-center gap-1 text-center justify-center flex-wrap">
                    <span class="text-xs text-gray-500 font-bold">
                        {{ __('messages.booking_terms_agreement_text') }}
                    </span>
                    <button type="button" wire:click="toggleTermsModal"
                        class="text-xs text-banhafade-accent font-black underline transition-colors hover:text-banhafade-accent/80 cursor-pointer">
                        {{ __('messages.booking_terms_link') }}
                    </button>
                    @if ($policyAccepted)
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                            class="w-4 h-4 text-green-500 transition-all animate-in zoom-in ms-1">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4.13-5.5z"
                                clip-rule="evenodd" />
                        </svg>
                    @endif
                </div>
            </div>

            <div class="flex gap-4">
                <x-ios-button wire:click="goToPayment"
                    class="!rounded-[1.5rem] py-4 shadow-lg shadow-banhafade-accent/20">
                    {{ $depositAmount > 0 ? __('messages.next') : __('messages.booking_confirm_button') }}
                </x-ios-button>
            </div>
        </div>

        <!-- Step 5: Payment Details -->
        <div x-show="step === 5" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0"
            style="display: none;">

            <header class="mb-6">
                <h2 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">
                    {{ __('messages.booking_payment_method_title') }}</h2>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest mt-1">
                    {{ __('messages.booking_payment_instruction') }}
                </p>
            </header>

            {{-- Manual Payment Alert --}}
            <div
                class="mb-6 p-4 rounded-[1.5rem] bg-banhafade-accent/10 border border-banhafade-accent/20 backdrop-blur-3xl animate-in fade-in slide-in-from-top-4 duration-700">
                <div class="flex gap-4">
                    <div
                        class="w-10 h-10 rounded-xl bg-banhafade-accent/20 flex items-center justify-center shrink-0 shadow-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="2.5" stroke="currentColor" class="w-5 h-5 text-banhafade-accent">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xs font-black text-banhafade-accent uppercase tracking-widest">
                            {{ __('messages.booking_manual_payment_alert_title') }}</h3>
                        <p class="text-[11px] text-gray-700 dark:text-gray-300 font-bold leading-relaxed mt-1">
                            {{ __('messages.booking_manual_payment_alert_desc') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Payment Method Selection Grid --}}
            <div class="grid grid-cols-2 gap-4 pb-2">
                @foreach ($this->paymentMethods as $method)
                    <button wire:click="selectPaymentMethod({{ $method->id }})"
                        class="relative liquid-panel p-5 rounded-[2rem] border transition-all duration-300 group overflow-hidden flex flex-col items-center gap-3 active:scale-95 {{ $selectedPaymentMethodId === $method->id ? 'border-banhafade-accent ring-2 ring-banhafade-accent/10' : 'border-white/20' }} cursor-pointer">

                        {{-- Icon Backdrop --}}
                        <div
                            class="w-12 h-12 rounded-2xl flex items-center justify-center {{ $method->type_enum->getColorClass() }} shadow-inner transition-transform group-hover:scale-110">
                            @if ($method->type_enum->getIcon() === 'wallet')
                                <x-icons.vodafone-cash class="w-8 h-8" />
                            @else
                                <x-icons.instapay class="w-10 h-10" />
                            @endif
                        </div>

                        <span
                            class="text-xs font-black text-gray-900 dark:text-white text-center break-words leading-tight">
                            {{ $method->type_enum->getLabel() }}
                        </span>

                        {{-- Active Indicator --}}
                        @if ($selectedPaymentMethodId === $method->id)
                            <div
                                class="absolute top-3 left-3 w-5 h-5 rounded-full bg-banhafade-accent flex items-center justify-center text-white animate-in zoom-in-50">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                                    class="w-3-3">
                                    <path fill-rule="evenodd"
                                        d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        @endif
                    </button>
                @endforeach
            </div>

            {{-- Selected Method Instruction Panel --}}
            @if ($this->selectedPaymentMethod)
                <div class="space-y-6 animate-in slide-in-from-bottom-2 duration-500">
                    <div
                        class="liquid-glass rounded-[2rem] p-6 border border-white/20 shadow-xl space-y-5 relative overflow-hidden">
                        {{-- Subtle background decoration --}}
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-banhafade-accent/5 rounded-full blur-3xl">
                        </div>

                        <div class="grid grid-cols-1 gap-4">
                            @if ($this->selectedPaymentMethod->account_name)
                                <div class="flex items-center gap-4 group">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-white/5 flex items-center justify-center text-gray-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest">
                                            {{ __('messages.booking_payment_account_name') }}</p>
                                        <p class="text-[13px] text-gray-900 dark:text-white font-black truncate">
                                            {{ $this->selectedPaymentMethod->account_name }}</p>
                                    </div>
                                </div>
                            @endif

                            <div class="flex items-center gap-4 group">
                                <div
                                    class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-white/5 flex items-center justify-center text-gray-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M10.5 1.5H8.25A2.25 2.25 0 0 0 6 3.75v16.5a2.25 2.25 0 0 0 2.25 2.25h7.5A2.25 2.25 0 0 0 18 20.25V3.75a2.25 2.25 0 0 0-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[9px] text-gray-400 font-black uppercase tracking-widest">
                                        {{ __('messages.phone') }}</p>
                                    <p class="text-[13px] text-gray-900 dark:text-white font-black whitespace-nowrap"
                                        dir="rtl">
                                        {{ $this->selectedPaymentMethod->phone_number }}</p>
                                </div>
                                <x-copy-button :value="$this->selectedPaymentMethod->phone_number" class="shrink-0 cursor-pointer" />
                            </div>
                        </div>

                        @if ($this->selectedPaymentMethod->pay_link)
                            <a href="{{ $this->selectedPaymentMethod->pay_link }}" target="_blank"
                                class="flex items-center justify-center gap-3 w-full p-4 rounded-2xl bg-banhafade-accent text-white active:scale-[0.98] transition-all font-black text-xs shadow-lg shadow-banhafade-accent/20 cursor-pointer">
                                {{ __('messages.booking_payment_pay_link') }}
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2.5" stroke="currentColor" class="w-4 h-4 rtl:rotate-180">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                </svg>
                            </a>
                        @endif

                        {{-- Reference Input Styled --}}
                        <div class="pt-2" x-data="{ showHint: false }">
                            <label
                                class="text-[10px] text-gray-400 font-extrabold uppercase tracking-widest mb-2 flex justify-between items-center px-1">
                                <span>
                                    @if ($this->selectedPaymentMethod->type_enum === \App\Enums\PaymentMethodType::InstaPay)
                                        {{ __('messages.booking_payment_ref_instapay') }}
                                    @else
                                        {{ __('messages.booking_payment_ref_vfcash') }}
                                    @endif
                                </span>
                                <button type="button" @click="showHint = !showHint"
                                    class="text-banhafade-accent underline decoration-dotted font-black text-[9px] cursor-pointer active:scale-95 transition-transform uppercase">
                                    {{ __('messages.booking_payment_show_hint') }}
                                </button>
                            </label>

                            <x-ios-input wire:model="paymentReference" dir="rtl" type="tel"
                                inputmode="numeric" maxlength="12" x-mask="999999999999"
                                placeholder="{{ $this->selectedPaymentMethod->type_enum === \App\Enums\PaymentMethodType::InstaPay ? __('messages.booking_payment_ref_placeholder_instapay') : __('messages.booking_payment_ref_placeholder_vfcash') }}" />

                            {{-- Help Image Hint --}}
                            <div x-show="showHint" x-collapse x-cloak class="mt-4">
                                <div
                                    class="relative rounded-2xl overflow-hidden border border-banhafade-accent/10 bg-white dark:bg-black p-2 shadow-inner">
                                    <img src="{{ $this->selectedPaymentMethod->type_enum === \App\Enums\PaymentMethodType::InstaPay ? asset('images/help/instapay-ref.png') : asset('images/help/vf-cash-ref.png') }}"
                                        alt="Help" class="w-full rounded-xl opacity-90">
                                    <div
                                        class="absolute inset-x-0 bottom-0 p-3 bg-gradient-to-t from-black/80 to-transparent">
                                        <p class="text-[9px] text-white font-bold leading-tight">
                                            {{ $this->selectedPaymentMethod->type_enum === \App\Enums\PaymentMethodType::InstaPay ? __('messages.booking_payment_ref_helper_instapay') : __('messages.booking_payment_ref_helper_vfcash') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Final Amounts Card --}}
                        <div class="p-4 rounded-2xl bg-black/5 dark:bg-white/5 space-y-3">
                            <div class="flex justify-between items-center group">
                                <span
                                    class="text-[10px] text-gray-500 font-bold tracking-tight">{{ __('messages.booking_deposit_label') }}</span>
                                <span
                                    class="text-xs font-black text-gray-900 dark:text-white">{{ number_format($depositAmount, 0) }}
                                    {{ __('messages.egp') }}</span>
                            </div>
                            @if ($shop->show_service_prices)
                            <div class="flex justify-between items-center opacity-60">
                                <span
                                    class="text-[10px] text-gray-400 font-bold tracking-tight">{{ __('messages.booking_remaining_label') }}</span>
                                <span
                                    class="text-xs font-black text-gray-400">{{ number_format($finalAmount - $depositAmount, 0) }}
                                    {{ __('messages.egp') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="pt-2">
                        <x-ios-button wire:click="confirmBooking" wire:loading.attr="disabled"
                            target="confirmBooking"
                            class="!rounded-[2rem] py-4.5 shadow-2xl shadow-banhafade-accent/30 text-sm font-black uppercase tracking-tight cursor-pointer">
                            <span wire:loading.remove wire:target="confirmBooking">
                                {{ __('messages.booking_payment_verify_button') }}
                            </span>
                            <span wire:loading wire:target="confirmBooking" class="flex items-center gap-2">
                                <span
                                    class="w-4 h-4 border-2 border-white/60 border-t-white rounded-full animate-spin"></span>
                                {{ __('messages.processing') }}
                            </span>
                        </x-ios-button>
                    </div>
                </div>
            @endif
        </div>

        {{-- Terms Bottom Sheet --}}
        <x-bottom-sheet wire:model="showTermsModal" :title="__('messages.booking_terms_title')">
            <x-slot:icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .415.162.798.425 1.081.263.284.625.462 1.025.462.4 0 .762-.178 1.025-.462.263-.283.425-.666.425-1.081 0-.231-.035-.454-.1-.664m-5.801 0A48.435 48.435 0 0 1 7.21 3.25c-1.131.094-1.976 1.057-1.976 2.192V16.5A2.25 2.25 0 0 0 7.5 18.75h.75m0 3.75A2.25 2.25 0 0 1 6 20.25V5.108c0-1.135.845-2.098 1.976-2.192a48.424 48.424 0 0 1 1.123-.08M15.75 18.75a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0A48.474 48.474 0 0 1 12 2.25c1.131 0 2.067.8 2.25 1.838a48.474 48.474 0 0 0 1.5 0c.183-1.038 1.12-1.838 2.25-1.838a48.424 48.424 0 0 1 1.123.08c1.131.094 1.976 1.057 1.976 2.192V16.5a2.25 2.25 0 0 1-2.25 2.25H12h-2.25z" />
                </svg>
            </x-slot:icon>

            {{-- Scrollable Terms Content --}}
            {{-- Scrollable Terms Content --}}
            <div class="space-y-6 max-h-[60vh] overflow-y-auto px-1 no-scrollbar mb-8" dir="rtl">
                <!-- 1. Payment Policy -->
                <div class="flex gap-4">
                    <div
                        class="w-1.5 h-1.5 rounded-full bg-banhafade-accent mt-2 shrink-0 shadow-[0_0_8px_rgba(1,101,225,0.4)]">
                    </div>
                    <p class="text-[13px] text-gray-700 dark:text-gray-300 font-bold leading-relaxed">
                        @if ($shop->payment_mode->value === \App\Enums\PaymentMode::NoPayment->value)
                            {{ __('messages.term_no_payment') }}
                        @elseif($shop->payment_mode->value === \App\Enums\PaymentMode::PartialDeposit->value)
                            {{ __('messages.term_partial_payment', [
                                'percentage' => number_format((float) $shop->deposit_percentage, 0),
                                'amount' => number_format((float) ($finalAmount * $shop->deposit_percentage) / 100, 0),
                            ]) }}
                        @else
                            {{ __('messages.term_full_payment', ['amount' => number_format($finalAmount, 0)]) }}
                        @endif
                    </p>
                </div>

                @if ($shop->payment_mode->value !== \App\Enums\PaymentMode::NoPayment->value)
                    <!-- 2. Refund Window (Success) -->
                    <div class="flex gap-4">
                        <div
                            class="w-1.5 h-1.5 rounded-full bg-green-500 mt-2 shrink-0 shadow-[0_0_8px_rgba(34,197,94,0.4)]">
                        </div>
                        <p class="text-[13px] text-gray-700 dark:text-gray-300 font-bold leading-relaxed">
                            {{ __('messages.term_2') }}
                        </p>
                    </div>

                    <!-- 3. NO Refund Window (Penalty) -->
                    <div class="flex gap-4">
                        <div
                            class="w-1.5 h-1.5 rounded-full bg-red-500 mt-2 shrink-0 shadow-[0_0_8px_rgba(239,68,68,0.4)]">
                        </div>
                        <p class="text-[13px] text-red-600 dark:text-red-400 font-bold leading-relaxed">
                            {{ __('messages.term_3') }}
                        </p>
                    </div>
                @endif

                <!-- 4. Late Policy -->
                <div class="flex gap-4">
                    <div
                        class="w-1.5 h-1.5 rounded-full bg-banhafade-accent mt-2 shrink-0 shadow-[0_0_8px_rgba(1,101,225,0.4)]">
                    </div>
                    <p class="text-[13px] text-gray-700 dark:text-gray-300 font-bold leading-relaxed">
                        {{ __('messages.term_5') }}
                    </p>
                </div>

                <!-- 5. No Show Penalty (Financial) -->
                @if ($shop->payment_mode->value !== \App\Enums\PaymentMode::NoPayment->value)
                    <div class="flex gap-4">
                        <div
                            class="w-1.5 h-1.5 rounded-full bg-red-500 mt-2 shrink-0 shadow-[0_0_8px_rgba(239,68,68,0.4)]">
                        </div>
                        <p class="text-[13px] text-red-600 dark:text-red-400 font-bold leading-relaxed">
                            {{ __('messages.term_6') }}
                        </p>
                    </div>
                @endif

                <!-- 6. Blocking Policy -->
                <div class="flex gap-4 border-t border-gray-100 dark:border-white/5 pt-4">
                    <div
                        class="w-1.5 h-1.5 rounded-full bg-red-600 mt-2 shrink-0 shadow-[0_0_8px_rgba(220,38,38,0.4)]">
                    </div>
                    <p class="text-[13px] text-red-600 dark:text-red-400 font-black leading-relaxed">
                        {{ __('messages.term_7') }}
                    </p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-col gap-3">
                <x-ios-button @click="$wire.set('policyAccepted', true); $wire.set('showTermsModal', false)">
                    {{ __('messages.booking_terms_accept') }}
                </x-ios-button>

                <x-ios-button @click="$wire.set('showTermsModal', false)" variant="secondary">
                    {{ __('messages.booking_terms_modal_close') }}
                </x-ios-button>
            </div>
        </x-bottom-sheet>

    @endif
</div>
