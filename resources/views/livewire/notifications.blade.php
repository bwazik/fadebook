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
    <div class="space-y-4">
        @forelse($notifications as $notification)
            <div wire:key="notification-{{ $notification->id }}"
                 @if(!$notification->read_at) wire:click="markAsRead('{{ $notification->id }}')" @endif
                 class="relative p-5 rounded-[2rem] border transition-all duration-500 transform-gpu
                        {{ !$notification->read_at 
                            ? 'bg-white dark:bg-white/10 border-banhafade-accent/30 shadow-xl dark:shadow-banhafade-accent/5 ring-1 ring-banhafade-accent/10' 
                            : 'bg-white/30 dark:bg-white/5 border-black/5 dark:border-white/5 opacity-70 grayscale-[0.3]' }}">
                
                <div class="flex gap-5 items-start">
                    {{-- Premium Icon Glow --}}
                    <div class="relative shrink-0">
                        <div class="w-14 h-14 rounded-[1.5rem] flex items-center justify-center relative z-10
                                    {{ !$notification->read_at 
                                        ? 'bg-gradient-to-br from-banhafade-accent to-purple-600 text-white shadow-lg' 
                                        : 'bg-gray-100 dark:bg-white/5 text-gray-400 border border-black/5 dark:border-white/10' }}">
                            @php
                                $type = $notification->data['type'] ?? 'info';
                                $icon = match($type) {
                                    'booking_confirmed' => 'M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z',
                                    'booking_reminder' => 'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
                                    'offer_near_you' => 'M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 1 0 9.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1 1 14.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z',
                                    default => 'M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0',
                                };
                            @endphp
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-6">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" />
                            </svg>
                        </div>
                        {{-- Soft shadow glow for unread --}}
                        @if(!$notification->read_at)
                            <div class="absolute inset-0 bg-banhafade-accent/40 blur-xl opacity-40 -z-10 animate-pulse"></div>
                        @endif
                    </div>

                    {{-- Text Content --}}
                    <div class="flex-1 min-w-0 pt-1">
                        <div class="flex justify-between items-start gap-2">
                            <h3 class="text-[14px] font-black text-gray-900 dark:text-white leading-tight">
                                {{ $notification->data['title'] ?? __('تنبيه جديد') }}
                            </h3>
                            @if(!$notification->read_at)
                                <span class="flex h-2 w-2 rounded-full bg-banhafade-accent mt-1.5 animate-pulse shrink-0"></span>
                            @endif
                        </div>
                        
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mt-2 line-clamp-2 leading-relaxed tracking-tight">
                            {{ $notification->data['message'] ?? __('لديك إشعار جديد من تطبيق BanhaFade.') }}
                        </p>

                        <div class="mt-4 flex items-center justify-between">
                            <time class="text-[10px] text-gray-400 dark:text-gray-500 font-black uppercase tracking-widest">
                                {{ $notification->created_at->diffForHumans() }}
                            </time>

                            @if(!$notification->read_at)
                                <div class="text-[10px] font-black text-banhafade-accent uppercase tracking-tighter opacity-0 group-hover:opacity-100 transition-opacity">
                                    {{ __('Click to mark as read') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Liquid transition hover effect --}}
                <div class="absolute inset-0 rounded-[2rem] bg-gradient-to-r from-banhafade-accent/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
            </div>
        @empty
            <x-empty-state title="{{ __('لا يوجد إشعارات') }}"
                description="{{ __('سوف تظهر هنا التنبيهات المتعلقة بحسابك وحجوزاتك.') }}">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-8 h-8 opacity-60">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                    </svg>
                </x-slot>
            </x-empty-state>
        @endforelse
    </div>
</div>
