<div class="pb-[calc(5rem+var(--safe-area-bottom)+64px)] min-h-screen pt-4 px-4 relative space-y-8 animate-in fade-in slide-in-from-bottom-4 duration-700">
    {{-- Profile Header --}}
    <div class="flex items-center gap-4 mb-8">
        <div class="w-16 h-16 rounded-3xl bg-fadebook-accent/10 flex items-center justify-center text-fadebook-accent text-2xl font-black shadow-inner">
            {{ Str::limit($user->name, 1, '') }}
        </div>
        <div>
            <h1 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">
                {{ $user->name }}
            </h1>
            <p class="text-sm text-gray-400 font-medium">{{ $user->phone }}</p>
        </div>
    </div>

    {{-- ═══════════════════════════════ --}}
    {{-- REFERRAL SECTION                --}}
    {{-- ═══════════════════════════════ --}}
    <section class="space-y-4">
        <h2 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tighter">
            {{ __('messages.profile_referral_section') }}
        </h2>
        
        <div class="p-8 rounded-[2.5rem] liquid-glass border border-fadebook-accent/10 dark:border-fadebook-accent/5 bg-gradient-to-b from-fadebook-accent/5 to-transparent space-y-6">
            <div class="text-center space-y-2">
                <h3 class="font-black text-xl text-gray-900 dark:text-white">{{ __('messages.profile_referral_code_label') }}</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('messages.offers_referral_card_desc') }}</p>
            </div>

            <div x-data="{
                copied: false,
                copyLink() {
                    navigator.clipboard.writeText('{{ $referralLink }}');
                    this.copied = true;
                    setTimeout(() => this.copied = false, 2000);
                }
            }" class="space-y-4">
                {{-- Input Area --}}
                <div class="relative group">
                    <input type="text" readonly value="{{ $referralLink }}"
                            class="w-full h-14 pr-4 pl-14 text-center text-sm font-medium rounded-2xl bg-white/50 dark:bg-black/20 border-black/5 dark:border-white/10 focus:ring-fadebook-accent focus:border-fadebook-accent">

                    <button @click="copyLink"
                            class="absolute left-2 top-2 bottom-2 px-4 rounded-xl bg-fadebook-accent text-white active:scale-95 transition-all text-xs font-bold shadow-lg shadow-fadebook-accent/20">
                        <span x-show="!copied">{{ __('messages.profile_referral_share_btn') }}</span>
                        <span x-show="copied" x-cloak class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                            {{ __('messages.offers_modal_copied') }}
                        </span>
                    </button>
                </div>

                {{-- Stats --}}
                <div class="flex items-center justify-between px-6 py-4 rounded-2xl bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/5">
                    <span class="text-sm font-bold text-gray-600 dark:text-gray-400">{{ __('messages.profile_referral_total_invites') }}</span>
                    <div class="flex items-center gap-2">
                        <span class="text-xl font-black text-fadebook-accent">{{ $inviteCount }}</span>
                        <svg class="w-5 h-5 text-fadebook-accent/30" fill="currentColor" viewBox="0 0 20 20"><path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a7 7 0 017 7v1H1v-1a7 7 0 017-7z"></path></svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- How it works --}}
        <div class="flex gap-4 p-1 -mx-4 overflow-x-auto snap-x no-scrollbar pb-2 px-4">
            {{-- Step 1 --}}
            <div class="flex-shrink-0 w-[200px] snap-center p-5 rounded-[2rem] liquid-glass border border-white/30 dark:border-white/5 shadow-md">
                <div class="flex items-center justify-center w-10 h-10 mb-3 text-lg font-black text-white rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 shadow-indigo-500/20">1</div>
                <h3 class="mb-1 text-sm font-bold text-gray-900 dark:text-white">{{ __('messages.profile_referral_step_1_title') }}</h3>
                <p class="text-[10px] leading-relaxed text-gray-500 dark:text-gray-400">{{ __('messages.profile_referral_step_1_desc') }}</p>
            </div>
            {{-- Step 2 --}}
            <div class="flex-shrink-0 w-[200px] snap-center p-5 rounded-[2rem] liquid-glass border border-white/30 dark:border-white/5 shadow-md">
                <div class="flex items-center justify-center w-10 h-10 mb-3 text-lg font-black text-white rounded-2xl bg-gradient-to-br from-pink-500 to-rose-600 shadow-rose-500/20">2</div>
                <h3 class="mb-1 text-sm font-bold text-gray-900 dark:text-white">{{ __('messages.profile_referral_step_2_title') }}</h3>
                <p class="text-[10px] leading-relaxed text-gray-500 dark:text-gray-400">{{ __('messages.profile_referral_step_2_desc') }}</p>
            </div>
            {{-- Step 3 --}}
            <div class="flex-shrink-0 w-[200px] snap-center p-5 rounded-[2rem] liquid-glass border border-white/30 dark:border-white/5 shadow-md">
                <div class="flex items-center justify-center w-10 h-10 mb-3 text-lg font-black text-white rounded-2xl bg-gradient-to-br from-amber-400 to-orange-600 shadow-orange-500/20">3</div>
                <h3 class="mb-1 text-sm font-bold text-gray-900 dark:text-white">{{ __('messages.profile_referral_step_3_title') }}</h3>
                <p class="text-[10px] leading-relaxed text-gray-500 dark:text-gray-400">{{ __('messages.profile_referral_step_3_desc') }}</p>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════ --}}
    {{-- MY COUPONS SECTION              --}}
    {{-- ═══════════════════════════════ --}}
    <section class="space-y-4">
        <h2 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tighter">
            {{ __('messages.offers_my_coupons_title') }}
        </h2>
        
        <div class="space-y-3">
            @forelse($myReferralCoupons as $coupon)
                {{-- Using the logic I built previously for glass coupons --}}
                <div class="p-5 rounded-[2rem] liquid-glass border border-white/40 dark:border-white/10 shadow-lg flex items-center gap-4 group hover:ring-2 hover:ring-fadebook-accent/20 transition-all active:scale-[0.98]"
                     x-data="{ copied: false }"
                     @click="navigator.clipboard.writeText('{{ $coupon->code }}'); copied = true; setTimeout(() => copied = false, 2000)">
                    <div class="flex-shrink-0 w-14 h-14 rounded-2xl bg-fadebook-accent/10 dark:bg-fadebook-accent/20 flex items-center justify-center text-fadebook-accent">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                    </div>

                    <div class="flex-grow min-w-0">
                        <div class="flex items-center gap-2">
                             <span class="text-[10px] font-bold text-fadebook-accent uppercase">
                                @if($coupon->discount_type === \App\Enums\DiscountType::Percentage)
                                    {{ __('messages.offers_discount_percentage', ['value' => $coupon->discount_value]) }}
                                @else
                                    {{ __('messages.offers_discount_fixed', ['value' => $coupon->discount_value]) }}
                                @endif
                             </span>
                        </div>
                        <h4 class="text-sm font-black text-gray-900 dark:text-white truncate">
                            {{ $coupon->shop ? $coupon->shop->name : __('messages.profile_reward_default_title') }}
                        </h4>
                        <div class="flex items-center justify-between mt-1">
                            <code class="text-[10px] font-mono font-bold text-gray-400">#{{ $coupon->code }}</code>
                            <span class="text-[9px] text-gray-400">{{ __('messages.offers_ends_label') }} {{ $coupon->end_date->format('Y-m-d') }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <x-empty-state 
                    title="{{ __('messages.offers_my_coupons_empty') }}"
                    description="{{ __('messages.offers_my_coupons_empty_sub') }}"
                />
            @endforelse
        </div>
    </section>

    {{-- Settings & Account Links --}}
    <section class="space-y-3">
        <h2 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tighter px-2">
            {{ __('messages.profile_account_section') }}
        </h2>
        
        <div class="liquid-panel divide-y divide-black/5 dark:divide-white/5 overflow-hidden">
             <a href="#" class="flex items-center justify-between p-4 hover:bg-black/5 dark:hover:bg-white/5 transition-colors group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <span class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ __('messages.profile_personal_info') }}</span>
                </div>
                <svg class="w-5 h-5 text-gray-300 group-hover:translate-x-[-4px] transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
            </a>

            <a href="{{ route('bookings.index') }}" wire:navigate class="flex items-center justify-between p-4 hover:bg-black/5 dark:hover:bg-white/5 transition-colors group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-500/10 text-purple-500 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <span class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ __('messages.profile_bookings') }}</span>
                </div>
                <svg class="w-5 h-5 text-gray-300 group-hover:translate-x-[-4px] transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-between p-4 hover:bg-red-500/10 transition-colors group">
                    <div class="flex items-center gap-3 text-red-500">
                        <div class="w-10 h-10 rounded-xl bg-red-500/10 flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </div>
                        <span class="text-sm font-bold">{{ __('messages.profile_logout_btn') }}</span>
                    </div>
                </button>
            </form>
        </div>
    </section>
</div>
