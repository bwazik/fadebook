@props(['booking', 'type' => 'user', 'componentId' => null])

@php
    $isOwner = $type === 'owner';
    $tag = $isOwner ? 'div' : 'a';
@endphp

<{{ $tag }}
    @if (!$isOwner) href="{{ route('booking.show', $booking->uuid) }}" wire:navigate @endif
    {{ $attributes->merge(['class' => 'block liquid-glass ' . (!$isOwner ? 'liquid-button' : '') . ' rounded-[1.5rem] p-4 border border-white/20 shadow-sm transition-all relative overflow-hidden']) }}>
    <div class="flex justify-between items-start mb-3">
        <div class="flex items-center gap-3">
            @if (!$isOwner)
                @php $logo = $booking->shop->getImage('logo')->first(); @endphp
                <div class="shrink-0">
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
                <div class="shrink-0">
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
            <p class="text-xs font-black text-fadebook-accent tracking-widest" dir="ltr">
                #{{ $booking->booking_code }}
            </p>
        </div>
    </div>

    <!-- Owner Actions -->
    @if ($isOwner)
        @if ($booking->status === \App\Enums\BookingStatus::Pending)
            <div class="flex gap-2 mt-4">
                <x-ios-button type="button"
                    @click="$dispatch('open-ios-alert', {
                        title: 'تأكيد الحجز',
                        message: 'هل أنت متأكد من تأكيد هذا الحجز؟',
                        action: 'confirmReservation',
                        params: {{ $booking->id }},
                        componentId: '{{ $componentId }}'
                    })"
                    class="flex-1 !py-2.5 !text-[11px] uppercase tracking-widest">
                    تأكيد الحجز
                </x-ios-button>
                <x-ios-button type="button" variant="danger"
                    @click="$dispatch('open-ios-alert', {
                        title: 'إلغاء الحجز',
                        message: 'هل أنت متأكد من إلغاء هذا الحجز؟ لا يمكن التراجع عن هذا الإجراء.',
                        action: 'cancelBooking',
                        params: {{ $booking->id }},
                        componentId: '{{ $componentId }}'
                    })"
                    class="flex-1 !py-2.5 !text-[11px] uppercase tracking-widest">
                    إلغاء
                </x-ios-button>
            </div>
        @elseif($booking->status === \App\Enums\BookingStatus::Confirmed)
            <div class="flex gap-2 mt-4">
                <x-ios-button type="button"
                    @click="$dispatch('open-ios-alert', {
                        title: 'وصول العميل',
                        message: 'هل وصل العميل بالفعل لبدء الموعد؟',
                        action: 'markArrived',
                        params: {{ $booking->id }},
                        componentId: '{{ $componentId }}'
                    })"
                    class="flex-1 !py-2.5 !text-[11px] uppercase tracking-widest">
                    وصل؟
                </x-ios-button>
                <x-ios-button type="button" variant="danger"
                    @click="$dispatch('open-ios-alert', {
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
                    @click="$dispatch('open-ios-alert', {
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
                    @click="$dispatch('open-ios-alert', {
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
