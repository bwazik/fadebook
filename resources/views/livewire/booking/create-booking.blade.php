<div class="pb-[calc(5rem+var(--safe-area-bottom)+64px)] relative min-h-screen pt-4 px-4"
     x-data="{
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
        <x-booking.step-indicator step="step" />

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
                            <h3 class="text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">{{ $categoryName }}</h3>
                            <div class="flex-1 h-px bg-black/5 dark:bg-white/5"></div>
                        </div>
                        
                        <div class="space-y-4">
                            @foreach($services as $service)
                                <div wire:key="service-{{ $service->id }}"
                                    @if ($service->is_active) @click="$wire.selectService({{ $service->id }})"
                                    @else
                                    wire:click="showServiceBlockedToast({{ $shop->is_online ? 'true' : 'false' }}, false)" @endif>
                                    <x-shop.service-card :service="$service" :selected="$selectedServiceId === $service->id" :unavailable="!$service->is_active" />
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p
                        class="text-gray-500 text-center py-10 bg-black/5 dark:bg-white/5 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-800">
                        {{ __('messages.booking_no_services') }}
                    </p>
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
                    <x-booking.barber-card 
                        wire:key="barber-{{ $barber->id }}"
                        :barber="$barber" 
                        :selected="'selectedBarberId === ' . $barber->id" 
                        wire:click="selectBarber({{ $barber->id }})"
                    />
                @empty
                    <p class="text-gray-500 text-center py-4">{{ __('messages.booking_no_barbers') }}</p>
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
                        :active="'selectedDate === \'' . $date->format('Y-m-d') . '\''"
                        wire:click="selectDate('{{ $date->format('Y-m-d') }}')"
                    />
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
                                        <x-booking.slot-card 
                                            wire:key="slot-{{ $slot }}"
                                            :time="$slot" 
                                            :active="'selectedSlot === \'' . $slot . '\''"
                                            wire:click="selectSlot('{{ $slot }}')"
                                        />
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
                            stroke="currentColor" class="w-8 h-8 text-fadebook-accent">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                        </svg>
                    </x-slot>
                </x-empty-state>
            @endif
        </div>

        <!-- Step 4: Confirm -->
        <div x-show="step === 4" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0"
            style="display: none;">
            <h2 class="text-lg font-black text-gray-900 dark:text-white mb-4">
                {{ __('messages.booking_confirm_title') }}</h2>

            <div class="liquid-glass rounded-[1.5rem] p-5 border border-white/20 shadow-sm mb-6 space-y-4">
                <x-booking.summary-row :label="__('messages.booking_label_shop')" :value="$shop->name" border />
                <x-booking.summary-row :label="__('messages.booking_label_service')" :value="$shop->services->firstWhere('id', $selectedServiceId)?->name ?? ''" border />
                
                @if ($selectedBarberId)
                    <x-booking.summary-row :label="__('messages.booking_label_barber')" :value="$shop->barbers->firstWhere('id', $selectedBarberId)?->name ?? ''" border />
                @endif

                @if($selectedDate)
                    <x-booking.summary-row :label="__('messages.booking_label_date')" :value="\Carbon\Carbon::parse($selectedDate)->translatedFormat('l, d F Y')" border />
                @endif

                @if($selectedSlot)
                    @php
                        $timeFormatted = \Carbon\Carbon::createFromFormat('H:i', $selectedSlot);
                        $timeString = $timeFormatted->format('g:i') . ' ' . ($timeFormatted->format('a') === 'am' ? __('messages.time_am') : __('messages.time_pm'));
                    @endphp
                    <x-booking.summary-row :label="__('messages.booking_label_time')" :value="$timeString" border />
                @endif

                <div class="pt-2 border-t border-gray-100 dark:border-gray-800 space-y-2">
                    <x-booking.summary-row 
                        :label="__('messages.booking_label_total')" 
                        :value="number_format($this->totalBeforeDiscount, 0) . ' ' . __('messages.egp')" 
                        :class="$discountAmount > 0 ? 'line-through opacity-50' : ''" 
                    />
                    
                    <x-booking.summary-row 
                        :label="__('messages.tax')" 
                        :value="'0 ' . __('messages.egp')" 
                    />

                    @if ($discountAmount > 0)
                        <div class="transition-all animate-in fade-in slide-in-from-top-1">
                            <x-booking.summary-row 
                                :label="__('messages.booking_discount')" 
                                :value="'-' . number_format($discountAmount, 0) . ' ' . __('messages.egp')" 
                                value-class="text-green-500" 
                            />
                        </div>
                    @endif
                    
                    <div class="flex justify-between items-center pt-2 border-t border-gray-100 dark:border-gray-800">
                        <span class="text-sm text-gray-900 dark:text-white font-black">{{ __('messages.booking_label_final_total') }}</span>
                        <span class="text-xl font-black text-fadebook-accent">
                            {{ number_format($finalAmount, 0) }} <small class="text-[10px]">{{ __('messages.egp') }}</small>
                        </span>
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <div class="flex gap-3">
                    <div class="flex-1">
                        <x-ios-input type="text" wire:model="couponCode" dir="ltr"
                            placeholder="{{ __('messages.booking_coupon_placeholder') }}" />
                    </div>
                    <div class="w-24">
                        <x-ios-button wire:click="applyCoupon" target="applyCoupon" variant="secondary"
                            class="!py-3.5">
                            <span wire:loading.remove
                                wire:target="applyCoupon">{{ __('messages.booking_apply_button') }}</span>
                            <span wire:loading wire:target="applyCoupon"
                                class="w-4 h-4 border-2 border-current border-t-transparent rounded-full animate-spin"></span>
                        </x-ios-button>
                    </div>
                </div>
            </div>

            <div class="mb-8 py-4 px-2">
                <div class="flex items-center gap-1 text-center justify-center flex-wrap">
                    <span class="text-xs text-gray-500 font-bold">
                        {{ __('messages.booking_terms_agreement_text') }}
                    </span>
                    <button type="button" wire:click="toggleTermsModal"
                        class="text-xs text-fadebook-accent font-black underline transition-colors hover:text-fadebook-accent/80 cursor-pointer">
                        {{ __('messages.booking_terms_link') }}
                    </button>
                    @if ($policyAccepted)
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
                            class="w-4 h-4 text-green-500 transition-all animate-in zoom-in pointer-events-none ms-1">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4.13-5.5z"
                                clip-rule="evenodd" />
                        </svg>
                    @endif
                </div>
            </div>

            <div class="flex gap-4">
                <x-ios-button wire:click="confirmBooking" wire:loading.attr="disabled" target="confirmBooking">
                    <span wire:loading.remove
                        wire:target="confirmBooking">{{ __('messages.booking_confirm_button') }}</span>
                    <span wire:loading wire:target="confirmBooking"
                        class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                </x-ios-button>
            </div>
        </div>

        {{-- Terms Bottom Sheet --}}
        <x-bottom-sheet wire:model="showTermsModal" :title="__('messages.booking_terms_title')">
            <x-slot:icon>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .415.162.798.425 1.081.263.284.625.462 1.025.462.4 0 .762-.178 1.025-.462.263-.283.425-.666.425-1.081 0-.231-.035-.454-.1-.664m-5.801 0A48.435 48.435 0 0 1 7.21 3.25c-1.131.094-1.976 1.057-1.976 2.192V16.5A2.25 2.25 0 0 0 7.5 18.75h.75m0 3.75A2.25 2.25 0 0 1 6 20.25V5.108c0-1.135.845-2.098 1.976-2.192a48.424 48.424 0 0 1 1.123-.08M15.75 18.75a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0A48.474 48.474 0 0 1 12 2.25c1.131 0 2.067.8 2.25 1.838a48.474 48.474 0 0 0 1.5 0c.183-1.038 1.12-1.838 2.25-1.838a48.424 48.424 0 0 1 1.123.08c1.131.094 1.976 1.057 1.976 2.192V16.5a2.25 2.25 0 0 1-2.25 2.25H12h-2.25z" />
                </svg>
            </x-slot:icon>

            {{-- Scrollable Terms Content --}}
            <div class="space-y-6 max-h-[50vh] overflow-y-auto px-1 custom-scrollbar mb-8" dir="rtl">
                <!-- Dynamic Payment Terms -->
                <div class="flex gap-4">
                    <div class="w-1.5 h-1.5 rounded-full bg-fadebook-accent mt-2 shrink-0 shadow-[0_0_8px_rgba(1,101,225,0.4)]"></div>
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

                <!-- Refund Policy if applicable -->
                @if ($shop->payment_mode->value !== \App\Enums\PaymentMode::NoPayment->value)
                    <div class="flex gap-4">
                        <div class="w-1.5 h-1.5 rounded-full bg-fadebook-accent mt-2 shrink-0 shadow-[0_0_8px_rgba(1,101,225,0.4)]"></div>
                        <p class="text-[13px] text-gray-700 dark:text-gray-300 font-bold leading-relaxed">
                            {{ __('messages.term_refund_policy') }}
                        </p>
                    </div>

                    <div class="flex gap-4">
                        <div class="w-1.5 h-1.5 rounded-full bg-red-500 mt-2 shrink-0 shadow-[0_0_8px_rgba(239,68,68,0.4)]"></div>
                        <p class="text-[13px] text-red-600 dark:text-red-400 font-bold leading-relaxed">
                            {{ __('messages.term_late_cancellation') }}
                        </p>
                    </div>

                    <div class="flex gap-4 border-b border-gray-100 dark:border-white/5 pb-4">
                        <div class="w-1.5 h-1.5 rounded-full bg-red-500 mt-2 shrink-0 shadow-[0_0_8px_rgba(239,68,68,0.4)]"></div>
                        <p class="text-[13px] text-red-600 dark:text-red-400 font-bold leading-relaxed">
                            {{ __('messages.term_no_show_penalty') }}
                        </p>
                    </div>
                @endif

                <!-- General Terms -->
                <div class="flex gap-4">
                    <div class="w-1.5 h-1.5 rounded-full bg-fadebook-accent mt-2 shrink-0 shadow-[0_0_8px_rgba(1,101,225,0.4)]"></div>
                    <p class="text-[13px] text-gray-900 dark:text-white font-black leading-relaxed">
                        {{ __('messages.term_cancellation_window') }}
                    </p>
                </div>

                <div class="flex gap-4">
                    <div class="w-1.5 h-1.5 rounded-full bg-red-500 mt-2 shrink-0 shadow-[0_0_8px_rgba(239,68,68,0.4)]"></div>
                    <p class="text-[13px] text-gray-900 dark:text-white font-black leading-relaxed">
                        {{ __('messages.term_no_show_procedure') }}
                    </p>
                </div>

                <div class="flex gap-4">
                    <div class="w-1.5 h-1.5 rounded-full bg-fadebook-accent mt-2 shrink-0 shadow-[0_0_8px_rgba(1,101,225,0.4)]"></div>
                    <p class="text-[13px] text-gray-700 dark:text-gray-300 font-bold leading-relaxed">{{ __('messages.term_1') }}</p>
                </div>

                <div class="flex gap-4">
                    <div class="w-1.5 h-1.5 rounded-full bg-fadebook-accent mt-2 shrink-0 shadow-[0_0_8px_rgba(1,101,225,0.4)]"></div>
                    <p class="text-[13px] text-gray-700 dark:text-gray-300 font-bold leading-relaxed">{{ __('messages.term_2') }}</p>
                </div>

                <div class="flex gap-4">
                    <div class="w-1.5 h-1.5 rounded-full bg-fadebook-accent mt-2 shrink-0 shadow-[0_0_8px_rgba(1,101,225,0.4)]"></div>
                    <p class="text-[13px] text-gray-700 dark:text-gray-300 font-bold leading-relaxed">{{ __('messages.term_3') }}</p>
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
