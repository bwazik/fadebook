<div class="pb-[calc(5rem+var(--safe-area-bottom)+64px)] min-h-screen pt-4 px-4 relative">
    <div class="mb-6 mt-4">
        <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">
            {{ __('messages.booking_list_title') }}
        </h1>
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
            :class="$wire.tab === 'upcoming' ? 'text-gray-900 dark:text-white' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'">
            {{ __('messages.booking_tab_upcoming') }}
        </button>
        <button wire:click="setTab('completed')"
            class="flex-1 py-2 text-[11px] font-black relative z-10 transition-colors uppercase tracking-wider cursor-pointer"
            :class="$wire.tab === 'completed' ? 'text-gray-900 dark:text-white' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'">
            {{ __('messages.booking_tab_past') }}
        </button>
        <button wire:click="setTab('cancelled')"
            class="flex-1 py-2 text-[11px] font-black relative z-10 transition-colors uppercase tracking-wider cursor-pointer"
            :class="$wire.tab === 'cancelled' ? 'text-gray-900 dark:text-white' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'">
            {{ __('messages.booking_tab_cancelled') }}
        </button>
    </div>

    <!-- Bookings List -->
    <div class="space-y-4">
        @forelse($this->bookings as $booking)
            <x-booking.booking-card :booking="$booking" />
        @empty
            <x-empty-state 
                title="{{ __('messages.booking_empty_state') }}"
                description="{{ __('messages.booking_empty_state_hint') }}"
            >
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 opacity-60">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5m-9-3.75h.008v.008H12v-.008Z" />
                    </svg>
                </x-slot>
            </x-empty-state>
        @endforelse
    </div>
</div>
