<div class="pb-[calc(5rem+var(--safe-area-bottom)+64px)] min-h-screen pt-4 px-4 relative">
    {{-- ═══════════════════════════════ --}}
    {{-- 1. HEADER SECTION               --}}
    {{-- ═══════════════════════════════ --}}
    <div class="flex items-center justify-between mb-6 mt-4">
        <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">
            {{ __('الإشعارات') }}
        </h1>

        @if($notifications->whereNull('read_at')->isNotEmpty())
            <button wire:click="markAllAsRead" 
                    wire:loading.attr="disabled"
                    class="flex items-center gap-1.5 px-4 py-2 rounded-2xl bg-banhafade-accent/10 border border-banhafade-accent/20 text-banhafade-accent text-xs font-bold active:scale-95 transition-all liquid-button">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="size-3.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                </svg>
                <span>{{ __('قراءة الكل') }}</span>
            </button>
        @endif
    </div>

    {{-- ═══════════════════════════════ --}}
    {{-- 2. NOTIFICATIONS FEED           --}}
    {{-- ═══════════════════════════════ --}}
    {{-- ═══════════════════════════════ --}}
    {{-- 2. NOTIFICATIONS FEED           --}}
    {{-- ═══════════════════════════════ --}}
    <x-ios-input-group class="mt-4">
        @forelse($notifications as $notification)
            <div wire:key="notification-{{ $notification->id }}"
                 wire:click="handleNotificationClick('{{ $notification->id }}')"
                 @class([
                     'flex gap-4 p-4 border-b border-black/5 dark:border-white/10 last:border-0 transition-colors cursor-pointer group',
                     'bg-banhafade-accent/[0.03] dark:bg-banhafade-accent/[0.05]' => !$notification->read_at,
                     'hover:bg-black/5 dark:hover:bg-white/5' => $notification->read_at,
                 ])>
                
                {{-- Notification Icon --}}
                <div class="relative shrink-0 pt-0.5">
                    <div @class([
                        'w-10 h-10 rounded-xl flex items-center justify-center relative z-10',
                        'bg-banhafade-accent text-white shadow-sm' => !$notification->read_at,
                        'bg-gray-100 dark:bg-white/5 text-gray-400' => $notification->read_at,
                        $notification->data['icon_bg'] ?? '' => !$notification->read_at && isset($notification->data['icon_bg']),
                    ])>
                        @php
                            $iconName = $notification->data['icon'] ?? 'heroicon-o-bell';
                        @endphp

                        <x-dynamic-component :component="$iconName" class="size-5" />
                    </div>
                </div>

                {{-- Text Content --}}
                <div class="flex-1 min-w-0 pt-0.5">
                    <div class="flex justify-between items-start gap-2">
                        <h3 @class([
                            'text-sm leading-tight transition-colors',
                            'font-black text-gray-900 dark:text-white' => !$notification->read_at,
                            'font-bold text-gray-500 dark:text-gray-400' => $notification->read_at,
                        ])>
                            {{ $notification->data['title'] ?? __('تنبيه جديد') }}
                        </h3>
                        @if(!$notification->read_at)
                            <span class="flex h-2 w-2 rounded-full bg-banhafade-accent mt-1.5 shrink-0"></span>
                        @endif
                    </div>
                    
                    <p @class([
                        'text-xs mt-1.5 line-clamp-2 leading-relaxed tracking-tight',
                        'font-bold text-gray-700 dark:text-gray-300' => !$notification->read_at,
                        'font-medium text-gray-400 dark:text-gray-500' => $notification->read_at,
                    ])>
                        {{ $notification->data['message'] ?? __('لديك إشعار جديد من تطبيق BanhaFade.') }}
                    </p>

                    <div class="mt-2.5">
                        <time class="text-[9px] text-gray-400 dark:text-gray-500 font-black uppercase tracking-widest">
                            {{ $notification->created_at->diffForHumans() }}
                        </time>
                    </div>
                </div>

                {{-- Row Arrow Indicator --}}
                <div class="flex items-center">
                    <svg class="w-3.5 h-3.5 text-gray-300 dark:text-gray-600 rtl:rotate-180 transition-transform"
                         fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7" />
                    </svg>
                </div>
            </div>
        @empty
            <div class="p-12 text-center">
                <div class="w-20 h-20 bg-gray-50 dark:bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4 border border-black/5 dark:border-white/10">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-8 h-8 opacity-40">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                    </svg>
                </div>
                <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tighter">{{ __('لا يوجد إشعارات') }}</h3>
                <p class="text-[11px] font-bold text-gray-400 mt-2">{{ __('سوف تظهر هنا التنبيهات المتعلقة بحسابك وحجوزاتك.') }}</p>
            </div>
        @endforelse
    </x-ios-input-group>

    <!-- Infinite Scroll Sentinel (iOS Style Loader) -->
    <div class="mt-6 px-4">
        @if ($this->hasMore)
            <div wire:key="sentinel-{{ $this->perPage }}" wire:intersect="loadMore"
                class="flex justify-center py-6">
                <div class="flex items-center gap-2 px-4 py-2 rounded-2xl bg-white/40 dark:bg-white/5 backdrop-blur-xl border border-black/5 dark:border-white/10 shadow-sm animate-pulse">
                    <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">{{ __('جاري التحميل...') }}</span>
                    <svg class="animate-spin h-3.5 w-3.5 text-banhafade-accent" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            </div>
        @elseif($notifications->count() > 0)
            <div wire:key="sentinel-end" class="flex justify-center py-8">
                <div class="flex flex-col items-center gap-2">
                    <div class="h-px w-8 bg-gray-200 dark:bg-gray-800"></div>
                    <p class="text-[9px] text-gray-400 font-black uppercase tracking-[0.2em]">{{ __('نهاية القائمة') }}</p>
                </div>
            </div>
        @endif
    </div>
</div>
