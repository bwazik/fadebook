<div class="pb-[calc(5rem+var(--safe-area-bottom)+64px)] relative min-h-screen pt-4 px-4">
    <!-- Header -->
    <div class="mb-6 mt-4">
        <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">
            {{ __('messages.financials_title') }}
        </h1>
        <p class="text-sm text-gray-500 font-bold mt-1">
            {{ __('messages.financials_subtitle') }}
        </p>
    </div>

    <!-- Date Filter -->
    <div class="grid grid-cols-2 gap-3 mb-8">
        <x-ios-select wire:model.live="month" :options="$this->monthOptions" placeholder="{{ __('messages.filter_month') }}" />
        <x-ios-select wire:model.live="year" :options="$this->yearOptions" placeholder="{{ __('messages.filter_year') }}" />
    </div>

    <!-- Main Stats -->
    <div class="space-y-4">
        <div class="liquid-glass rounded-2xl p-6 border border-white/20 shadow-sm relative overflow-hidden">
            <div class="absolute inset-0 bg-banhafade-accent/5 pointer-events-none"></div>
            <p class="text-xs font-black text-gray-500 uppercase tracking-widest mb-2 relative z-10">
                {{ __('messages.gross_earnings') }}</p>
            <p class="text-4xl font-black text-banhafade-accent leading-none relative z-10">
                {{ number_format($this->stats['gross_earnings'], 0) }} <span
                    class="text-sm">{{ __('messages.egp') }}</span></p>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="liquid-glass rounded-2xl p-5 border border-white/20 shadow-sm">
                <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">
                    {{ __('messages.commission_deducted') }} ({{ $this->stats['commission_rate'] }}%)</p>
                <p class="text-xl font-black text-red-500 dark:text-red-400 leading-none">
                    -{{ number_format($this->stats['commission_deducted'], 0) }} <span
                        class="text-[10px]">{{ __('messages.egp') }}</span></p>
            </div>
            <div class="liquid-glass rounded-2xl p-5 border border-white/20 shadow-sm">
                <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">
                    {{ __('messages.net_payout') }}</p>
                <p class="text-xl font-black text-green-500 dark:text-green-400 leading-none">
                    {{ number_format($this->stats['net_payout'], 0) }} <span
                        class="text-[10px]">{{ __('messages.egp') }}</span></p>
            </div>
        </div>

        <div
            class="liquid-glass rounded-2xl p-5 border border-white/20 shadow-sm flex items-center justify-between mt-4">
            <div>
                <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">
                    {{ __('messages.total_bookings_completed') }}</p>
                <p class="text-lg font-black text-gray-900 dark:text-white leading-none">
                    {{ $this->stats['total_bookings'] }} <span
                        class="text-[10px]">{{ __('messages.booking_unit') }}</span></p>
            </div>
            <div
                class="w-12 h-12 rounded-full bg-banhafade-accent/10 flex items-center justify-center text-banhafade-accent">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5m-9-3.75h.008v.008H12v-.008Z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Transactions List -->
    <div class="mt-8">
        <h2 class="text-xs font-black text-gray-500 uppercase tracking-[0.2em] mb-4">
            {{ __('messages.transactions_list_title') }}</h2>

        <div class="space-y-3">
            @forelse($this->transactions as $tx)
                <div
                    class="liquid-glass rounded-2xl p-4 border border-white/20 shadow-sm flex items-center justify-between relative active:scale-[0.98] transition-transform">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-banhafade-accent/5 flex items-center justify-center text-banhafade-accent">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182 1.128-.879 2.97-.879 4.098 0 .144.113.27.238.38.373" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-[13px] font-black text-gray-900 dark:text-white leading-tight">
                                {{ $tx->client->name }}
                            </p>
                            <p class="text-[9px] font-bold text-gray-500 uppercase tracking-wider mt-0.5">
                                {{ $tx->service->name }} • {{ $tx->completed_at->format('Y/m/d H:i') }}
                            </p>
                        </div>
                    </div>
                    <div class="text-left">
                        <p class="text-sm font-black text-gray-900 dark:text-white leading-none">
                            {{ number_format($tx->final_amount, 0) }} <span
                                class="text-[9px]">{{ __('messages.egp') }}</span>
                        </p>
                        @php
                            $rate = $tx->shop->commission_rate ?? 10.0;
                            $comm = $tx->final_amount * ($rate / 100);
                        @endphp
                        <p class="text-[8px] font-bold text-red-500 mt-1">
                            -{{ number_format($comm, 1) }} {{ __('messages.commission_label') }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="py-12 flex flex-col items-center justify-center opacity-40">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-12 h-12 mb-2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182 1.128-.879 2.97-.879 4.098 0 .144.113.27.238.38.373" />
                    </svg>
                    <p class="text-xs font-black uppercase tracking-widest">{{ __('messages.no_transactions') }}</p>
                </div>
            @endforelse
            <!-- Infinite Scroll Sentinel -->
            @if ($this->hasMore)
                <div wire:key="sentinel-{{ $this->perPage }}" wire:intersect="loadMore"
                    class="flex justify-center py-8">
                    <div
                        class="flex items-center gap-2 px-4 py-2 rounded-full border border-black/5 dark:border-white/10 bg-white/40 dark:bg-white/5 backdrop-blur-xl">
                        <span
                            class="text-xs font-bold text-gray-500">{{ __('messages.loading_more_dashboard') }}</span>
                        <svg class="animate-spin h-4 w-4 text-banhafade-accent" xmlns="http://www.w3.org/2000/svg"
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
        </div>
