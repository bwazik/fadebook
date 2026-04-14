<div class="pb-[calc(5rem+var(--safe-area-bottom)+64px)] relative min-h-screen pt-4 px-4">
    <!-- Header -->
    <div class="mb-6 mt-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">
                {{ __('messages.dashboard_title') }}
            </h1>
            <p class="text-sm text-gray-500 font-bold mt-1">
                {{ $shop->name }}
            </p>
        </div>
        <div class="flex items-center gap-3">
            @if ($this->isOwner)
                <a href="{{ route('home') }}" wire:navigate
                    class="w-12 h-12 rounded-2xl bg-fadebook-accent/10 border-2 border-fadebook-accent/20 shadow-sm overflow-hidden flex items-center justify-center liquid-button group">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor"
                        class="w-6 h-6 text-fadebook-accent group-hover:scale-110 transition-transform">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                </a>
            @endif

            <div class="shrink-0">
                @php $logo = $shop->getImage('logo')->first(); @endphp
                @if ($logo)
                    <img src="{{ Storage::url($logo->path) }}" alt="{{ $shop->name }}"
                        class="w-12 h-12 rounded-full object-cover border border-black/5 dark:border-white/10 shadow-sm bg-white dark:bg-[#1c1c1e]">
                @else
                    <div
                        class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center border border-black/5 dark:border-white/10 shadow-sm">
                        <span class="text-xl text-gray-400 font-black">{{ mb_substr($shop->name, 0, 1) }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-2.5 mb-6">
        <div class="liquid-glass rounded-2xl p-3.5 border border-white/20 shadow-sm transition-all hover:scale-[1.02]">
            <p class="text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-1.5 leading-none">
                {{ __('messages.month_bookings') }}
            </p>
            <p class="text-xl font-black text-gray-900 dark:text-white leading-none">
                {{ $this->stats['total_bookings'] }}
            </p>
        </div>
        <div class="liquid-glass rounded-2xl p-3.5 border border-white/20 shadow-sm relative overflow-hidden transition-all hover:scale-[1.02]">
            <div class="absolute inset-0 bg-fadebook-accent/5 pointer-events-none"></div>
            <p class="text-[9px] font-bold text-fadebook-accent uppercase tracking-widest mb-1.5 leading-none">
                {{ __('messages.net_amount_egp') }}
            </p>
            <p class="text-xl font-black text-fadebook-accent leading-none">
                {{ number_format($this->stats['net_payout'], 0) }}
            </p>
        </div>
        <div class="liquid-glass rounded-2xl p-3.5 border border-white/20 shadow-sm transition-all hover:scale-[1.02]">
            <p class="text-[9px] font-bold text-yellow-500 uppercase tracking-widest mb-1.5 leading-none">
                {{ __('messages.rating') }}
            </p>
            <div class="flex items-center gap-1">
                <p class="text-xl font-black text-gray-900 dark:text-white leading-none">
                    {{ (float) $this->stats['average_rating'] }}
                </p>
                @if($this->stats['average_rating'] > 0)
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3.5 h-3.5 text-yellow-400">
                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" />
                    </svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5 text-blue-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                    </svg>
                @endif
            </div>
        </div>
    </div>

    @if ($this->pendingCount > 0)
        <x-home.info-card :href="route('dashboard.reservations')" title="{{ __('messages.pending_confirmations') }}" :subtitle="__('messages.pending_confirmations_desc', ['count' => $this->pendingCount])" color="fadebook-accent"
            class="mb-8 !px-0 animate-in fade-in slide-in-from-top-4 duration-500">
            <x-slot name="icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                    stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
            </x-slot>
        </x-home.info-card>
    @endif

    <!-- Quick Actions -->
    <div class="flex gap-2 mb-8 overflow-x-auto no-scrollbar -mx-4 px-4 snap-x">
        <a href="{{ route('dashboard.reservations') }}" wire:navigate
            class="shrink-0 snap-start flex flex-col items-center justify-center w-20 h-20 liquid-glass liquid-button rounded-[1.2rem] border border-white/20 shadow-sm text-gray-700 dark:text-gray-300">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-6 h-6 mb-2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5m-9-3.75h.008v.008H12v-.008Z" />
            </svg>
            <span class="text-[10px] font-medium opacity-80">{{ __('messages.nav_schedule') }}</span>
        </a>
        <a href="{{ route('dashboard.barbers') }}" wire:navigate
            class="shrink-0 snap-start flex flex-col items-center justify-center w-20 h-20 liquid-glass liquid-button rounded-[1.2rem] border border-white/20 shadow-sm text-gray-700 dark:text-gray-300">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-6 h-6 mb-2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
            </svg>
            <span class="text-[10px] font-medium opacity-80">{{ __('messages.nav_barbers') }}</span>
        </a>
        <a href="{{ route('dashboard.services') }}" wire:navigate
            class="shrink-0 snap-start flex flex-col items-center justify-center w-20 h-20 liquid-glass liquid-button rounded-[1.2rem] border border-white/20 shadow-sm text-gray-700 dark:text-gray-300">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-6 h-6 mb-2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
            </svg>
            <span class="text-[10px] font-medium opacity-80">{{ __('messages.nav_services') }}</span>
        </a>
        <a href="{{ route('dashboard.categories') }}" wire:navigate
            class="shrink-0 snap-start flex flex-col items-center justify-center w-20 h-20 liquid-glass liquid-button rounded-[1.2rem] border border-white/20 shadow-sm text-gray-700 dark:text-gray-300">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-6 h-6 mb-2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M2.25 7.125C2.25 6.504 2.754 6 3.375 6h6c.621 0 1.125.504 1.125 1.125v3.75c0 .621-.504 1.125-1.125 1.125h-6a1.125 1.125 0 0 1-1.125-1.125v-3.75ZM14.25 8.625c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v8.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 0 1-1.125-1.125v-8.25ZM3.75 16.125c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 0 1-1.125-1.125v-2.25Z" />
            </svg>
            <span class="text-[10px] font-medium opacity-80">{{ __('messages.nav_categories') }}</span>
        </a>
        <a href="{{ route('dashboard.reviews') }}" wire:navigate
            class="shrink-0 snap-start flex flex-col items-center justify-center w-20 h-20 liquid-glass liquid-button rounded-[1.2rem] border border-white/20 shadow-sm text-gray-700 dark:text-gray-300">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-6 h-6 mb-2 text-yellow-500">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.563.563 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
            </svg>
            <span class="text-[10px] font-medium opacity-80">{{ __('messages.nav_reviews') }}</span>
        </a>
        <a href="{{ route('dashboard.settings') }}" wire:navigate
            class="shrink-0 snap-start flex flex-col items-center justify-center w-20 h-20 liquid-glass liquid-button rounded-[1.2rem] border border-white/20 shadow-sm text-gray-700 dark:text-gray-300">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-6 h-6 mb-2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
            <span class="text-[10px] font-medium opacity-80">{{ __('messages.nav_shop') }}</span>
        </a>
    </div>

    <!-- Barber Performance Carousel -->
    @if($this->barberPerformance->isNotEmpty())
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tighter">
                    {{ __('messages.barbers_performance') }}
                </h2>
            </div>
            <div class="flex gap-4 overflow-x-auto no-scrollbar -mx-4 px-4 pb-2 snap-x">
                @foreach($this->barberPerformance as $barber)
                    <div class="shrink-0 snap-start w-32 liquid-glass rounded-2xl p-4 border border-white/20 shadow-sm flex flex-col items-center">
                        <div class="relative mb-3">
                            @php $photo = $barber->getImage('photo')->first(); @endphp
                            @if ($photo)
                                <img src="{{ Storage::url($photo->path) }}" alt="{{ $barber->name }}"
                                    class="w-14 h-14 rounded-full object-cover border-2 border-white/50 shadow-sm">
                            @else
                                <div class="w-14 h-14 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center border-2 border-white/50 shadow-sm">
                                    <span class="text-xl text-gray-400 font-black uppercase">{{ mb_substr($barber->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 bg-yellow-400 text-white text-[9px] font-black px-1.5 py-0.5 rounded-full shadow-md flex items-center gap-0.5">
                                @if($barber->average_rating > 0)
                                    <span>{{ (float) $barber->average_rating }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-2 h-2">
                                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-2.5 h-2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z" />
                                    </svg>
                                @endif
                            </div>
                        </div>
                        <h3 class="text-[11px] font-black text-gray-900 dark:text-white uppercase truncate w-full text-center">{{ $barber->name }}</h3>
                        <span class="text-[9px] text-gray-400 font-bold mt-1 uppercase tracking-tighter">{{ $barber->total_reviews }} {{ __('messages.reviews') }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Today's Upcoming Bookings -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tighter">
                {{ __('messages.today_bookings') }}
            </h2>
            <span class="text-[10px] font-black text-gray-400 bg-black/5 dark:bg-white/5 px-2 py-1 rounded-full uppercase tracking-widest">
                {{ $this->todayBookings->count() }} {{ __('messages.booking_unit') }}
            </span>
        </div>

        <div class="space-y-3">
            @forelse($this->todayBookings as $booking)
                <div class="liquid-glass rounded-2xl p-4 border border-white/20 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-black text-gray-900 dark:text-white uppercase mb-1">
                            {{ $booking->client->name }}
                        </p>
                        <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">
                            {{ $booking->service?->name ?? __('messages.booking_service_deleted') }} •
                            {{ $booking->barber?->name ?? __('messages.any_barber') }}
                        </p>
                    </div>
                    <div class="text-left">
                        <p class="text-sm font-black text-fadebook-accent tracking-widest" dir="ltr">
                            {{ $booking->scheduled_at->format('g:i A') }}
                        </p>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1">
                            #{{ $booking->booking_code }}
                        </p>
                    </div>
                </div>
            @empty
                <x-empty-state title="{{ __('messages.no_bookings_today') }}" description="{{ __('messages.no_bookings_today_desc') }}">
                    <x-slot name="icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor" class="w-8 h-8 opacity-60">
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
    </div>
</div>
