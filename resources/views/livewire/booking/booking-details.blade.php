<div class="pb-[calc(5rem+var(--safe-area-bottom)+64px)] min-h-screen pt-4 px-4 relative">
    <x-sticky-back-button href="{{ route('bookings.index') }}" />

    <div class="mb-6 mt-4">
        <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">
            {{ __('messages.booking_details_title') }}
        </h1>
        <p class="text-sm text-gray-500 font-bold mt-1">
            {{ $booking->shop->name }}
        </p>
    </div>

    <!-- Status-specific Guidance -->
    @if ($booking->status === \App\Enums\BookingStatus::Pending)
        <div class="mb-6 p-4 rounded-3xl bg-amber-500/10 border border-amber-500/20 flex gap-4 scale-in">
            <div class="w-10 h-10 rounded-2xl bg-amber-500/20 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-5 h-5 text-amber-600">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h4 class="text-sm font-black text-amber-900 dark:text-amber-400 mb-0.5">
                    {{ __('messages.booking_pending_title') }}</h4>
                <p class="text-xs text-amber-800/70 dark:text-amber-500/60 font-bold leading-relaxed">
                    {{ __('messages.booking_pending_description') }}
                </p>
            </div>
        </div>
    @endif

    <!-- Booking Code Card -->
    <div class="liquid-glass rounded-[2rem] p-8 border-2 shadow-2xl text-center mb-8 relative overflow-hidden transition-all duration-500"
        :class="'{{ $booking->status->value }}'
        === '{{ \App\Enums\BookingStatus::Confirmed->value }}' ? 'border-fadebook-accent/30 bg-fadebook-accent/[0.03]' :
            'border-white/50 dark:border-white/10'">

        <div
            class="absolute -top-12 -right-12 w-32 h-32 bg-fadebook-accent/10 rounded-full blur-3xl pointer-events-none">
        </div>

        <p
            class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-3 relative z-10">
            {{ __('messages.booking_code') }}</p>
        <p class="text-6xl font-black tracking-[0.1em] text-gray-900 dark:text-white relative z-10 drop-shadow-sm"
            dir="ltr">
            {{ $booking->booking_code }}
        </p>

        <div class="mt-4 mb-2 relative z-10">
            <p class="text-[10px] text-gray-500 font-bold">{{ __('messages.booking_code_helper') }}</p>
        </div>

        <div class="mt-4 inline-flex">
            <x-booking.status-badge :status="$booking->status" class="px-4 py-2 text-[10px]" />
        </div>
    </div>

    <!-- Booking Details List -->
    <div class="liquid-glass rounded-[1.5rem] p-5 border border-white/20 shadow-sm mb-6 space-y-4">
        <x-booking.summary-row :label="__('messages.booking_label_shop')" :value="$booking->shop->name" border />
        <x-booking.summary-row :label="__('messages.booking_label_service')" :value="$booking->service?->name ?? __('messages.booking_service_deleted')" border />

        @if ($booking->barber_id)
            <x-booking.summary-row :label="__('messages.booking_label_barber')" :value="$booking->barber?->name ?? __('messages.booking_barber_deleted')" border />
        @endif

        <x-booking.summary-row :label="__('messages.booking_label_date')" :value="$booking->scheduled_at->translatedFormat('l, d F Y')" border />
        <x-booking.summary-row :label="__('messages.booking_label_time')" :value="$booking->scheduled_at->format('g:i A')" border />

        <div class="pt-2 border-t border-gray-100 dark:border-gray-800 space-y-2">
            <x-booking.summary-row :label="__('messages.booking_label_total')" :value="number_format($booking->service_price, 0) . ' ' . __('messages.egp')" :class="$booking->discount_amount > 0 ? 'line-through opacity-50' : ''" />

            <x-booking.summary-row :label="__('messages.tax')" :value="'0 ' . __('messages.egp')" />

            @if ($booking->discount_amount > 0)
                <div class="transition-all animate-in fade-in slide-in-from-top-1">
                    <x-booking.summary-row :label="__('messages.booking_discount')" :value="'-' . number_format($booking->discount_amount, 0) . ' ' . __('messages.egp')" value-class="text-green-500" />
                </div>
            @endif

            <div class="flex justify-between items-center pt-2 border-t border-gray-100 dark:border-gray-800">
                <span
                    class="text-sm text-gray-900 dark:text-white font-black">{{ __('messages.booking_label_final_total') }}</span>
                <span class="text-xl font-black text-gray-900 dark:text-white">
                    {{ number_format($booking->final_amount, 0) }} <small
                        class="text-[10px] ms-0.5">{{ __('messages.egp') }}</small>
                </span>
            </div>

            @if ($booking->status === \App\Enums\BookingStatus::Completed)
                <div class="pt-3 animate-in fade-in slide-in-from-top-2">
                    <x-booking.summary-row
                        :label="__('messages.booking_label_paid_total')"
                        :value="number_format($booking->final_amount, 0) . ' ' . __('messages.egp')"
                        value-class="text-green-600 dark:text-green-400"
                    />
                </div>
            @else
                <div class="pt-2 space-y-3">
                    @if ($booking->paid_amount > 0)
                        <x-booking.summary-row
                            :label="__('messages.booking_label_paid')"
                            :value="number_format($booking->paid_amount, 0) . ' ' . __('messages.egp')"
                            value-class="text-green-600 dark:text-green-400"
                        />
                    @endif

                    @if ($this->remainingAmount > 0)
                        <x-booking.summary-row
                            :label="__('messages.booking_label_remaining')"
                            :value="number_format($this->remainingAmount, 0) . ' ' . __('messages.egp')"
                            value-class="text-fadebook-accent text-xl font-black"
                        />
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Actions -->
    <div class="mt-8 px-2">
        @if ($this->canCancel)
            <x-ios-button
                @click="$dispatch('open-ios-alert', {
                    title: '{{ __('messages.booking_cancel_title') }}',
                    message: '{{ __('messages.booking_cancel_confirm') }}',
                    action: 'cancelBooking',
                    componentId: '{{ $this->getId() }}'
                })"
                variant="danger">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                    stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span>{{ __('messages.booking_cancel_button') }}</span>
            </x-ios-button>
        @elseif(in_array($booking->status, [\App\Enums\BookingStatus::Pending, \App\Enums\BookingStatus::Confirmed]))
            {{-- Window Closed Warning --}}
            <div
                class="p-4 rounded-[1.5rem] bg-gray-50 dark:bg-white/[0.03] border border-gray-100 dark:border-white/10 flex gap-4 items-center">
                <div
                    class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-white/10 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5 text-gray-500">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 font-bold leading-relaxed">
                    {{ __('messages.booking_cancel_timeout') }}
                </p>
            </div>
        @elseif(
            $booking->status === \App\Enums\BookingStatus::Completed &&
                !$booking->reviews()->where('user_id', Auth::id())->exists())
            <x-ios-button href="{{ route('review.create', $booking->uuid) }}" wire:navigate>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                    stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                </svg>
                <span>{{ __('messages.review_experience_action') }}</span>
            </x-ios-button>
        @endif
    </div>
</div>
