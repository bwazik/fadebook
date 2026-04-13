@props(['booking'])

<a {{ $attributes->merge(['href' => route('booking.show', $booking->uuid), 'class' => 'block liquid-glass liquid-button rounded-[1.5rem] p-4 border border-white/20 shadow-sm transition-all']) }} wire:navigate>
    <div class="flex justify-between items-start mb-3">
        <div class="flex items-center gap-3">
            @php $logo = $booking->shop->images->where('collection', 'logo')->first(); @endphp
            <div class="shrink-0">
                @if ($logo)
                    <img src="{{ Storage::url($logo->path) }}" alt="{{ $booking->shop->name }}"
                        class="w-10 h-10 rounded-full object-cover border border-black/5 dark:border-white/10 shadow-sm bg-white dark:bg-[#1c1c1e]">
                @else
                    <div class="w-10 h-10 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center border border-black/5 dark:border-white/10 shadow-sm">
                        <span class="text-lg text-gray-400 font-black">{{ mb_substr($booking->shop->name, 0, 1) }}</span>
                    </div>
                @endif
            </div>
            <div>
                <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase leading-none mb-1">
                    {{ $booking->shop->name }}
                </h3>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">
                    {{ $booking->service->name }}
                </p>
            </div>
        </div>
        <div class="shrink-0 text-left">
            <x-booking.status-badge :status="$booking->status" />
        </div>
    </div>

    <div class="flex items-center gap-4 pt-3 border-t border-gray-100 dark:border-gray-800">
        <div class="flex-1">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">{{ __('messages.booking_label_date') }}</p>
            <p class="text-xs font-bold text-gray-900 dark:text-white">
                {{ $booking->scheduled_at->translatedFormat('Y-m-d g:i A') }}
            </p>
        </div>
        <div class="shrink-0 text-left">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">{{ __('messages.booking_code') }}</p>
            <p class="text-xs font-black text-fadebook-accent tracking-widest" dir="ltr">
                #{{ $booking->booking_code }}
            </p>
        </div>
    </div>
</a>
