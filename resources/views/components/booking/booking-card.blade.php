@props(['booking', 'type' => 'user', 'componentId' => null])

@php
    $isOwner = $type === 'owner';
    $tag = $isOwner ? 'div' : 'a';
@endphp

<{{ $tag }}
    @if (!$isOwner) href="{{ route('booking.show', $booking->uuid) }}" wire:navigate
    @else
        wire:click="openBookingDetails({{ $booking->id }})" @endif
    {{ $attributes->merge(['class' => 'block liquid-glass ' . (!$isOwner ? 'liquid-button' : 'cursor-pointer active:scale-[0.99]') . ' rounded-[1.5rem] p-4 border border-white/20 shadow-sm transition-all relative overflow-hidden']) }}>
    <div class="flex justify-between items-start mb-3">
        <div class="flex items-center gap-3">
            @if (!$isOwner)
                @php $logo = $booking->shop->getImage('logo')->first(); @endphp
                <div class="shrink-0 text-right">
                    @if ($logo)
                        <img src="{{ Storage::url($logo->path) }}" alt="{{ $booking->shop->name }}"
                            class="w-10 h-10 rounded-full object-cover border border-black/5 dark:border-white/10 shadow-sm bg-white dark:bg-[#1c1c1e]">
                    @else
                        <div
                            class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center border border-black/5 dark:border-white/10 shadow-sm">
                            <span
                                class="text-lg text-gray-400 font-black">{{ mb_substr($booking->shop->name, 0, 1) }}</span>
                        </div>
                    @endif
                </div>
                <div>
                    <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase leading-none mb-1">
                        {{ $booking->shop->name }}
                    </h3>
                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest leading-tight">
                        {{ $booking->service?->name ?? __('messages.booking_service_deleted') }}
                        @if ($booking->barber)
                            • {{ $booking->barber->name }}
                        @endif
                    </p>
                </div>
            @else
                @php $avatar = $booking->client?->getImage('avatar')->first(); @endphp
                <div class="shrink-0 text-right">
                    @if ($avatar)
                        <img src="{{ Storage::url($avatar->path) }}" alt="{{ $booking->client->name }}"
                            class="w-10 h-10 rounded-full object-cover border border-black/5 dark:border-white/10 shadow-sm bg-white dark:bg-[#1c1c1e]">
                    @else
                        <div
                            class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center border border-black/5 dark:border-white/10 shadow-sm">
                            <span
                                class="text-lg text-gray-400 font-black">{{ mb_substr($booking->client?->name ?? '?', 0, 1) }}</span>
                        </div>
                    @endif
                </div>
                <div>
                    <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase leading-none mb-1">
                        {{ $booking->client?->name ?? 'عميل محذوف' }}
                    </h3>
                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest leading-tight">
                        {{ $booking->service?->name ?? 'خدمة غير متوفرة' }}
                        @if ($booking->barber)
                            • {{ $booking->barber->name }}
                        @endif
                    </p>
                </div>
            @endif
        </div>
        <div class="shrink-0 text-left">
            <x-booking.status-badge :status="$booking->status" />
        </div>
    </div>

    <div
        class="flex items-center gap-4 pt-3 border-t border-gray-100 dark:border-gray-800 {{ $isOwner && in_array($booking->status, [\App\Enums\BookingStatus::Pending, \App\Enums\BookingStatus::Confirmed, \App\Enums\BookingStatus::InProgress]) ? 'mb-4' : '' }}">
        <div class="flex-1">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">
                {{ __('messages.booking_label_date') }}</p>
            <p class="text-xs font-bold text-gray-900 dark:text-white">
                {{ $booking->scheduled_at->translatedFormat('Y-m-d g:i A') }}
            </p>
        </div>
        <div class="shrink-0 text-left">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">
                {{ __('messages.booking_code') }}</p>
            <p class="text-xs font-black text-banhafade-accent tracking-widest" dir="ltr">
                #{{ $booking->booking_code }}
            </p>
        </div>
    </div>

    <!-- Owner Actions & Payment Info -->
    @if ($isOwner)
        @if ($booking->payment_method_id && $booking->status === \App\Enums\BookingStatus::Pending)
            <div class="mt-4 p-3 rounded-2xl bg-banhafade-accent/5 border border-banhafade-accent/10 space-y-2">
                <div class="flex justify-between items-center">
                    <span
                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest">{{ __('messages.booking_payment_method_title') }}</span>
                    <span class="text-[10px] font-black text-gray-900 dark:text-white text-left">
                        {{ $booking->paymentMethod?->type->getLabel() ?? 'N/A' }}
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span
                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest">{{ __('messages.booking_payment_ref_label') }}</span>
                    <span class="text-[11px] font-black text-banhafade-accent tracking-widest text-left" dir="ltr">
                        {{ $booking->payment_reference }}
                    </span>
                </div>
                <div class="flex justify-between items-center pt-1 border-t border-banhafade-accent/5">
                    <span
                        class="text-[9px] font-black text-gray-400 uppercase tracking-widest">{{ __('messages.booking_deposit_label') }}</span>
                    <span class="text-[11px] font-black text-gray-900 dark:text-white text-left">
                        {{ number_format($booking->deposit_amount, 0) }} {{ __('messages.egp') }}
                    </span>
                </div>
            </div>
        @endif

        @if ($booking->status === \App\Enums\BookingStatus::Pending)
            <div class="flex gap-2 mt-4">
                <x-ios-button type="button"
                    @click.stop="$dispatch('open-ios-alert', {
                        title: '{{ $booking->deposit_amount > 0 ? __('messages.booking_payment_verify_button') : __('messages.status_confirmed') }}',
                        message: '{{ $booking->deposit_amount > 0 ? 'هل تأكدت من وصول المبلغ على محفظتك؟' : 'هل أنت متأكد من تأكيد هذا الحجز؟' }}',
                        action: 'verifyPayment',
                        params: {{ $booking->id }},
                        componentId: '{{ $componentId }}'
                    })"
                    class="flex-1 !py-2.5 !text-[11px] uppercase tracking-widest">
                    {{ $booking->deposit_amount > 0 ? __('messages.booking_payment_verify_button') : __('messages.status_confirmed') }}
                </x-ios-button>
                <x-ios-button type="button" variant="danger"
                    @click.stop="$dispatch('open-ios-alert', {
                        title: '{{ __('messages.booking_cancel_button') }}',
                        message: '{{ __('messages.booking_cancel_confirm') }}',
                        action: 'cancelBooking',
                        params: {{ $booking->id }},
                        componentId: '{{ $componentId }}'
                    })"
                    class="flex-1 !py-2.5 !text-[11px] uppercase tracking-widest">
                    {{ __('messages.cancel') }}
                </x-ios-button>
            </div>
        @elseif($booking->status === \App\Enums\BookingStatus::Confirmed)
            <div class="flex gap-2 mt-4">
                <x-ios-button type="button"
                    @click.stop="$dispatch('open-ios-alert', {
                        title: 'وصول العميل',
                        message: 'هل وصل العميل بالفعل لبدء الموعد؟',
                        action: 'markArrived',
                        params: {{ $booking->id }},
                        componentId: '{{ $componentId }}'
                    })"
                    class="flex-1 !py-2.5 !text-[11px] uppercase tracking-widest">
                    {{ __('messages.status_arrived') }}
                </x-ios-button>
                <x-ios-button type="button" variant="danger"
                    @click.stop="$dispatch('open-ios-alert', {
                        title: 'إلغاء الحجز',
                        message: 'هل أنت متأكد من إلغاء هذا الحجز؟',
                        action: 'cancelBooking',
                        params: {{ $booking->id }},
                        componentId: '{{ $componentId }}'
                    })"
                    class="flex-1 !py-2.5 !text-[11px] uppercase tracking-widest">
                    إلغاء
                </x-ios-button>
            </div>
        @elseif($booking->status === \App\Enums\BookingStatus::InProgress)
            <div class="flex gap-2 mt-4">
                <x-ios-button type="button"
                    @click.stop="$dispatch('open-ios-alert', {
                        title: 'أنهى الموعد',
                        message: 'هل انتهى العميل من الخدمة؟ سيتم تحويل الموعد للمنتهية.',
                        action: 'markCompleted',
                        params: {{ $booking->id }},
                        componentId: '{{ $componentId }}'
                    })"
                    class="flex-1 !py-2.5 !text-[11px] uppercase tracking-widest">
                    خلص؟
                </x-ios-button>
                <x-ios-button type="button" variant="secondary"
                    @click.stop="$dispatch('open-ios-alert', {
                        title: 'تسجيل كغائب',
                        message: 'هل لم يحضر العميل للموعد؟ سيتم تسجيله كغائب.',
                        action: 'markNoShow',
                        params: {{ $booking->id }},
                        componentId: '{{ $componentId }}'
                    })"
                    class="flex-1 !py-2.5 !text-[11px] uppercase tracking-widest">
                    ما جاش
                </x-ios-button>
            </div>
        @endif
    @endif
    </{{ $tag }}>
