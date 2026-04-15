<div class="pb-[calc(5rem+var(--safe-area-bottom)+64px)] min-h-screen pt-4 px-4 relative">
    <div class="mb-6 mt-4 flex items-center justify-between">
        <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">
            {{ $search ? __('messages.search_results') : __('messages.reservations_title') }}
        </h1>

        @if ($search)
            <button wire:click="$set('search', '')"
                class="text-[10px] font-black uppercase text-fadebook-accent bg-fadebook-accent/10 px-3 py-1 rounded-lg">
                {{ __('messages.clear_search') }} X
            </button>
        @endif
    </div>

    <!-- Tabs -->
    <div class="flex liquid-panel bg-black/[0.03] dark:bg-transparent p-1 rounded-[1.2rem] mb-6 relative">
        <div class="absolute inset-y-1 bg-white/90 dark:bg-white/10 rounded-xl shadow-sm transition-all duration-500 ease-[cubic-bezier(0.2,0.8,0.2,1)]"
            :style="$wire.tab === 'upcoming' ? 'width: calc(33.333% - 0.25rem); right: 0.25rem;' :
                ($wire.tab === 'completed' ? 'width: calc(33.333% - 0.25rem); right: calc(33.333% + 0.125rem);' :
                    'width: calc(33.333% - 0.25rem); right: calc(66.666% + 0.125rem);')">
        </div>

        <button wire:click="setTab('upcoming')"
            class="flex-1 py-2 text-[11px] font-black relative z-10 transition-colors uppercase tracking-wider cursor-pointer"
            :class="$wire.tab === 'upcoming' ? 'text-gray-900 dark:text-white' :
                'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'">
            {{ __('messages.status_upcoming') }}
        </button>
        <button wire:click="setTab('completed')"
            class="flex-1 py-2 text-[11px] font-black relative z-10 transition-colors uppercase tracking-wider cursor-pointer"
            :class="$wire.tab === 'completed' ? 'text-gray-900 dark:text-white' :
                'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'">
            {{ __('messages.status_completed') }}
        </button>
        <button wire:click="setTab('cancelled')"
            class="flex-1 py-2 text-[11px] font-black relative z-10 transition-colors uppercase tracking-wider cursor-pointer"
            :class="$wire.tab === 'cancelled' ? 'text-gray-900 dark:text-white' :
                'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'">
            {{ __('messages.status_cancelled') }}
        </button>
    </div>

    <!-- Reservations List -->
    <div class="space-y-4">
        @forelse($this->bookings as $booking)
            <x-booking.booking-card :booking="$booking" type="owner" :component-id="$this->getId()" />
        @empty
            <x-empty-state title="{{ __('messages.no_bookings_current') }}"
                description="{{ __('messages.no_bookings_current_desc') }}">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-8 h-8 opacity-60">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5m-9-3.75h.008v.008H12v-.008Z" />
                    </svg>
                </x-slot>
            </x-empty-state>
        @endforelse
    </div>

    <!-- Infinite Scroll Sentinel -->
    @if ($this->hasMore)
        <div wire:key="sentinel-{{ $this->perPage }}" wire:intersect="loadMore" class="flex justify-center py-8">
            <div
                class="flex items-center gap-2 px-4 py-2 rounded-full border border-black/5 dark:border-white/10 bg-white/40 dark:bg-white/5 backdrop-blur-xl">
                <span class="text-xs font-bold text-gray-500">{{ __('messages.loading_more_dashboard') }}</span>
                <svg class="animate-spin h-4 w-4 text-fadebook-accent" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
            </div>
        </div>
    @endif

    {{-- Booking Details Modal --}}
    <x-bottom-sheet wire:model="showDetailsModal" :title="__('messages.booking_details_title')">
        @if ($this->selectedBooking)
            <div class="space-y-6 pb-6">
                {{-- Client Info & Contact --}}
                <div
                    class="flex items-center justify-between p-4 rounded-[1.5rem] bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/5 shadow-inner">
                    <div class="flex items-center gap-4">
                        @php $avatar = $this->selectedBooking->client?->getImage('avatar')->first(); @endphp
                        <div class="shrink-0">
                            @if ($avatar)
                                <img src="{{ Storage::url($avatar->path) }}"
                                    class="w-12 h-12 rounded-full object-cover border border-white/20">
                            @else
                                <div
                                    class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center border border-black/5 shadow-sm">
                                    <span
                                        class="text-xl text-gray-400 font-black">{{ mb_substr($this->selectedBooking->client?->name ?? '?', 0, 1) }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="min-w-0">
                            <h4 class="text-sm font-black text-gray-900 dark:text-white uppercase truncate">
                                {{ $this->selectedBooking->client?->name }}</h4>
                            <p class="text-[11px] text-gray-500 font-bold tracking-widest mt-0.5" dir="rtl">
                                {{ $this->selectedBooking->client?->phone }}</p>
                        </div>
                    </div>
                    <a href="tel:{{ $this->selectedBooking->client?->phone }}"
                        class="w-11 h-11 rounded-2xl bg-green-500 text-white flex items-center justify-center shadow-lg shadow-green-500/20 active:scale-95 transition-transform shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z" />
                        </svg>
                    </a>
                </div>

                {{-- Summary Rows --}}
                <div class="space-y-3.5 px-1">
                    <x-booking.summary-row :label="__('messages.booking_label_status')" border>
                        <x-slot:value>
                            <x-booking.status-badge :status="$this->selectedBooking->status" />
                        </x-slot:value>
                    </x-booking.summary-row>
                    <x-booking.summary-row :label="__('messages.booking_label_service')" :value="$this->selectedBooking->service?->name" border />
                    <x-booking.summary-row :label="__('messages.booking_label_barber')" :value="$this->selectedBooking->barber?->name" border />
                    <x-booking.summary-row :label="__('messages.booking_label_date')" :value="$this->selectedBooking->scheduled_at->translatedFormat('l, d F Y')" border />
                    <x-booking.summary-row :label="__('messages.booking_label_time')" :value="$this->selectedBooking->scheduled_at->translatedFormat('g:i A')" border />
                </div>

                {{-- Financial Breakdown --}}
                <div
                    class="p-6 rounded-[2rem] bg-fadebook-accent/5 dark:bg-white/5 border border-fadebook-accent/10 dark:border-white/10 space-y-4 shadow-sm">
                    <div class="space-y-2.5">
                        <x-booking.summary-row :label="__('messages.booking_label_total')" :value="number_format($this->selectedBooking->service_price, 0) . ' ' . __('messages.egp')"
                            :class="$this->selectedBooking->discount_amount > 0 ? 'line-through opacity-50' : ''" />

                        @if ($this->selectedBooking->discount_amount > 0)
                            <x-booking.summary-row :label="__('messages.booking_discount')" :value="'-' .
                                number_format($this->selectedBooking->discount_amount, 0) .
                                ' ' .
                                __('messages.egp')" value-class="text-green-500" />
                        @endif

                        <div class="flex justify-between items-center pt-2 border-t border-fadebook-accent/10">
                            <span class="text-sm text-gray-900 dark:text-white font-black">{{ __('messages.booking_label_final_total') }}</span>
                            <span class="text-xl font-black text-gray-900 dark:text-white">
                                {{ number_format($this->selectedBooking->final_amount, 0) }} 
                                <small class="text-[10px] ms-0.5">{{ __('messages.egp') }}</small>
                            </span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-fadebook-accent/10 dark:border-white/10 space-y-2.5">
                        <x-booking.summary-row :label="__('messages.booking_label_paid')" :value="number_format($this->selectedBooking->paid_amount, 0) . ' ' . __('messages.egp')"
                            value-class="text-green-600 dark:text-green-400 font-black" />
                        <x-booking.summary-row :label="__('messages.booking_label_remaining')" :value="number_format(
                            $this->selectedBooking->final_amount - $this->selectedBooking->paid_amount,
                            0,
                        ) .
                            ' ' .
                            __('messages.egp')"
                            value-class="text-fadebook-accent text-xl font-black" />
                    </div>
                </div>

                {{-- Transaction ID if present --}}
                @if ($this->selectedBooking->payment_reference)
                    <div
                        class="p-5 rounded-[1.5rem] bg-gray-50 dark:bg-white/[0.03] border border-dashed border-gray-200 dark:border-white/10 flex items-center justify-between">
                        <div class="min-w-0">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">
                                {{ __('messages.booking_payment_ref_label') }}</p>
                            <p class="text-sm font-black text-gray-900 dark:text-white tracking-[0.2em]" dir="ltr">
                                #{{ $this->selectedBooking->payment_reference }}</p>
                        </div>
                        <div
                            class="px-3 py-1.5 rounded-xl bg-gray-100 dark:bg-white/10 text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">
                            {{ $this->selectedBooking->paymentMethod?->type->getLabel() }}
                        </div>
                    </div>
                @endif

                {{-- Code Display --}}
                <div class="flex flex-col items-center py-6 border-t border-gray-100 dark:border-white/5 mt-4">
                    <span
                        class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">{{ __('messages.booking_code') }}</span>
                    <span
                        class="text-4xl font-black text-fadebook-accent tracking-[0.5em] ms-[0.5em] selection:bg-fadebook-accent selection:text-white"
                        dir="ltr">
                        {{ $this->selectedBooking->booking_code }}
                    </span>
                </div>
            </div>
        @endif
    </x-bottom-sheet>
</div>
