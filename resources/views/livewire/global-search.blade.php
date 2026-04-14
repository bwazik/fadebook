<div x-data="{ open: @entangle('show') }" @open-global-search.window="open = true">
    <x-bottom-sheet wire:model="show" title="{{ __('messages.search_hub_title') }}">
        <div class="px-1 pb-8">
            {{-- Search Input --}}
            <div class="relative mb-6">
                <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-gray-400">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 15.803a7.5 7.5 0 0 0 10.607 0Z" />
                    </svg>
                </div>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="query"
                    placeholder="{{ match($context) {
                        'dashboard' => __('messages.search_placeholder_dashboard'),
                        'client' => __('messages.search_placeholder_client'),
                        default => __('messages.search_placeholder_marketplace')
                    } }}"
                    class="w-full bg-black/5 dark:bg-white/5 border-2 border-transparent focus:border-fadebook-accent/30 rounded-2xl py-4 pr-12 pl-4 text-sm font-bold text-gray-900 dark:text-white outline-none transition-all placeholder:text-gray-400"
                    x-ref="searchInput"
                    @opened.window="setTimeout(() => $refs.searchInput.focus(), 100)"
                >
            </div>

            {{-- 1. Loader Overlay --}}
            <div wire:loading wire:target="query" class="w-full min-h-[300px] grid place-items-center text-center content-center">
                <div class="flex flex-col items-center">
                    <div class="w-10 h-10 border-4 border-fadebook-accent/20 border-t-fadebook-accent rounded-full animate-spin mb-4"></div>
                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest">{{ __('messages.searching_now') }}</p>
                </div>
            </div>

            {{-- 2. Results Area --}}
            <div wire:loading.remove wire:target="query">
                
                {{-- Suggestions (Blank State) --}}
                @if(empty($query))
                    @if($this->suggestions->isNotEmpty())
                        <div class="mb-2 px-2">
                            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">
                                {{ $context === 'dashboard' ? __('messages.todays_schedule') : __('messages.upcoming_next') }}
                            </h3>
                            <div class="space-y-3">
                                @foreach($this->suggestions as $suggestion)
                                    <a href="{{ $context === 'dashboard' ? route('dashboard.reservations', ['search' => $suggestion->booking_code]) : route('booking.show', $suggestion->uuid) }}" 
                                       wire:navigate
                                       class="flex items-center gap-4 p-3 rounded-2xl bg-black/5 dark:bg-white/5 border border-white/5 active:scale-[0.98] transition-all">
                                        <div class="w-10 h-10 rounded-xl bg-fadebook-accent/10 flex items-center justify-center shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-fadebook-accent">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-2">
                                                <h4 class="text-xs font-black text-gray-900 dark:text-white truncate">
                                                    {{ $context === 'dashboard' ? ($suggestion->client?->name ?? 'عميل') : ($suggestion->shop?->name ?? 'محل') }}
                                                </h4>
                                                <span class="text-[9px] font-bold text-gray-400">{{ \Carbon\Carbon::parse($suggestion->scheduled_at)->format('H:i') }}</span>
                                            </div>
                                            <p class="text-[10px] text-gray-500 font-bold truncate uppercase mt-0.5">
                                                {{ $suggestion->service?->name ?? 'خدمة' }} • #{{ $suggestion->booking_code }}
                                            </p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        {{-- Empty State: Start Typing --}}
                        <x-empty-state 
                            class="min-h-[300px]"
                            title="{{ __('messages.start_typing_to_search') }}" 
                            description="{{ __('messages.search_hint') }}">
                            <x-slot name="icon">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 15.803a7.5 7.5 0 0 0 10.607 0Z" />
                                </svg>
                            </x-slot>
                        </x-empty-state>
                    @endif
                @endif

                {{-- Search Results --}}
                @if(!empty($query))
                    <div class="space-y-2">
                        @forelse($this->results as $result)
                            <a href="{{ $result['url'] }}" 
                               wire:navigate
                               class="flex items-center gap-4 p-3 rounded-2xl hover:bg-black/5 dark:hover:bg-white/5 active:scale-[0.98] transition-all group">
                                <div class="w-12 h-12 rounded-full overflow-hidden bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/10 shrink-0">
                                    @if($result['image'])
                                        <img src="{{ Storage::url($result['image']) }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400 font-black text-sm uppercase">
                                            {{ mb_substr($result['title'] ?? '?', 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-black text-gray-900 dark:text-white truncate group-hover:text-fadebook-accent transition-colors">
                                        {{ $result['title'] }}
                                    </h4>
                                    <p class="text-[10px] text-gray-500 font-bold truncate uppercase mt-1">
                                        {{ $result['subtitle'] }}
                                    </p>
                                </div>
                                <div class="shrink-0 text-gray-300 dark:text-gray-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4 rtl:rotate-180">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                                    </svg>
                                </div>
                            </a>
                        @empty
                            {{-- Empty State: No Results --}}
                            <x-empty-state 
                                class="min-h-[300px]"
                                title="{{ __('messages.no_results_found') }}" 
                                description="{{ __('messages.try_different_search') }}">
                                <x-slot name="icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                                    </svg>
                                </x-slot>
                            </x-empty-state>
                        @endforelse
                    </div>
                @endif
            </div>
        </div>
    </x-bottom-sheet>
</div>
