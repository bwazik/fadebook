@auth
    @if (
        !auth()->user()->phone_verified_at &&
            !request()->routeIs(['phone.verification.show', 'password.request', 'password.change']))
        <div class="fixed left-0 right-0 z-[60] px-4 pt-4 pointer-events-none" style="top: env(safe-area-inset-top);">
            <div class="max-w-sm mx-auto pointer-events-auto px-4">
                <div
                    class="liquid-glass border border-banhafade-accent/20 rounded-full p-2.5 flex items-center justify-between shadow-2xl">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-full bg-banhafade-accent/10 flex items-center justify-center text-banhafade-accent shadow-inner">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="space-y-0.5">
                            <p
                                class="text-sm font-black text-gray-900 dark:text-white tracking-tight leading-none uppercase">
                                {{ __('messages.verify_phone_title') }}</p>
                            <p class="text-[11px] text-gray-500 dark:text-gray-400 font-bold leading-tight">
                                {{ __('messages.verify_to_book_reminder') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('phone.verification.show') }}" wire:navigate
                        class="shrink-0 bg-banhafade-accent text-white px-4 py-2 rounded-[1rem] text-xs font-black shadow-lg shadow-banhafade-accent/20 active:scale-95 transition-all uppercase tracking-wider">
                        {{ __('messages.confirm') }}
                    </a>
                </div>
            </div>
        </div>
    @endif
@endauth
