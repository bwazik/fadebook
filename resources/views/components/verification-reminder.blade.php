@auth
    @if(!auth()->user()->phone_verified_at && !request()->routeIs(['phone.verification.show', 'password.request', 'password.change']))
        <div class="fixed left-0 right-0 z-[60] px-4 pt-2 pointer-events-none" style="top: env(safe-area-inset-top);">
            <div class="max-w-md mx-auto pointer-events-auto">
                <div class="glass border border-fadebook-accent/30 rounded-2xl p-4 flex items-center justify-between shadow-lg backdrop-blur-xl bg-white/60 dark:bg-gray-900/60">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-fadebook-accent/10 flex items-center justify-center text-fadebook-accent">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <div class="text-[13px] leading-tight">
                            <p class="font-bold text-gray-900 dark:text-white">{{ __('messages.verify_phone_title') }}</p>
                            <p class="text-gray-600 dark:text-gray-400 text-xs mt-0.5">{{ __('messages.verify_to_book_reminder') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('phone.verification.show') }}" wire:navigate class="bg-fadebook-accent text-white px-4 py-2 rounded-xl text-sm font-bold shadow-sm active:scale-95 transition-transform">
                        {{ __('messages.confirm') }}
                    </a>
                </div>
            </div>
        </div>
    @endif
@endauth
