<div class="pb-[calc(5rem+var(--safe-area-bottom)+64px)] relative min-h-screen pt-4 px-4">
    <x-sticky-back-button href="{{ route('profile.index') }}" />

    {{-- Header --}}
    <div class="mb-6 mt-4">
        <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">
            {{ __('messages.profile_referral_title') }}
        </h1>
    </div>

    {{-- ═══════════════════════════════ --}}
    {{-- 1. APPLE PAY STYLE REWARDS CARD --}}
    {{-- ═══════════════════════════════ --}}
    <div class="mb-8 px-1 animate-in fade-in slide-in-from-bottom-4 duration-700 delay-150">
        <div
            class="relative w-full rounded-[2rem] p-6 text-white overflow-hidden shadow-2xl bg-gradient-to-br from-[#1a1a1c] to-[#2d2d30] border border-white/10 group">
            {{-- Ambient Glass Effects --}}
            <div
                class="absolute top-0 right-0 w-32 h-32 bg-banhafade-accent/30 rounded-full blur-[40px] -mr-10 -mt-10 group-hover:scale-125 transition-transform duration-1000">
            </div>
            <div
                class="absolute bottom-0 left-0 w-40 h-40 bg-blue-500/20 rounded-full blur-[50px] -ml-10 -mb-10 group-hover:scale-125 transition-transform duration-1000 delay-150">
            </div>

            <div class="relative z-10 flex flex-col gap-6">
                <div class="flex justify-between items-start">
                    <div class="space-y-1">
                        <p class="text-white/40 text-[9px] font-black uppercase tracking-[0.3em]">عضوية التميز</p>
                        <h2 class="text-2xl font-black tracking-tighter uppercase px-0.5">BANHAFADE REWARDS</h2>
                    </div>

                    <div
                        class="w-10 h-10 rounded-2xl bg-white/5 backdrop-blur-xl flex items-center justify-center border border-white/10 shadow-inner group-hover:rotate-6 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="size-5 text-white/80">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3" />
                        </svg>
                    </div>
                </div>

                {{-- Shop Selector --}}
                @if ($this->availableShops->count() > 0)
                    <div class="px-2">
                        <p class="text-[10px] font-bold text-white/50 uppercase tracking-widest mb-2">اختر الصالون اللي
                            هتدعو صاحبك ليه</p>
                        <x-ios-select wire:model.live="selectedShopId">
                            @foreach ($this->availableShops as $shop)
                                <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                            @endforeach
                        </x-ios-select>
                    </div>
                @endif

                {{-- Full Link Sharing Area --}}
                <div
                    class="relative h-14 bg-white/5 backdrop-blur-md border border-white/10 rounded-2xl px-5 flex items-center justify-between group/link overflow-hidden">
                    <span class="text-[13px] font-bold text-white/90 tracking-tight truncate me-2" dir="ltr">
                        {{ str_replace(['http://', 'https://'], '', $this->referralLink) }}
                    </span>
                    <x-copy-button :value="$this->referralLink"
                        class="!bg-banhafade-accent !text-white !w-10 !h-10 !rounded-xl !border-0 shadow-lg shadow-banhafade-accent/20 hover:scale-105 active:scale-95 transition-all cursor-pointer" />
                </div>

                <div class="flex justify-between items-end">
                    <div>
                        <p class="text-white/30 text-[8px] font-mono tracking-[0.2em] uppercase">BANHAFADE • REWARDS
                            MEMBER</p>
                        <p class="text-white/80 font-black text-[13px] tracking-tight uppercase">{{ $this->user->name }}
                        </p>
                    </div>

                    <div class="flex gap-1 opacity-50">
                        <div class="w-2.5 h-2.5 rounded-full bg-banhafade-accent"></div>
                        <div class="w-2.5 h-2.5 rounded-full bg-white/20"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════ --}}
    {{-- 2. STATS GRID (INDEX STYLE)     --}}
    {{-- ═══════════════════════════════ --}}
    <div class="mb-12 animate-in fade-in slide-in-from-bottom-4 duration-700 delay-300">
        <x-section-header title="إحصائيات الدعوات" />

        <div class="grid grid-cols-2 gap-4 mt-4">
            <div
                class="bg-black/5 dark:bg-white/5 rounded-[2.5rem] p-6 text-center group border border-transparent hover:border-banhafade-accent/10 transition-colors shadow-sm">
                <p class="text-3xl font-black text-gray-900 dark:text-white group-hover:scale-110 transition-transform">
                    {{ $this->stats['total_invites'] }}</p>
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1 block">إجمالي
                    الدعوات</span>
            </div>
            <div
                class="bg-black/5 dark:bg-white/5 rounded-[2.5rem] p-6 text-center group border border-transparent hover:border-green-500/10 transition-colors shadow-sm">
                <p class="text-3xl font-black text-green-500 group-hover:scale-110 transition-transform">
                    {{ $this->stats['successful_invites'] }}</p>
                <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1 block">دعوات
                    ناجحة</span>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════ --}}
    {{-- 3. HOW IT WORKS                 --}}
    {{-- ═══════════════════════════════ --}}
    <div class="mb-12 px-2 animate-in fade-in slide-in-from-bottom-4 duration-700 delay-[450ms]">
        <x-section-header title="ازاي بتشتغل؟" />

        <div class="space-y-6 mt-6">
            <div class="flex gap-5 group">
                <div
                    class="w-12 h-12 rounded-[1.25rem] bg-banhafade-accent/10 flex items-center justify-center text-banhafade-accent font-black shrink-0 transition-all group-hover:rotate-6 group-hover:scale-110 shadow-sm border border-banhafade-accent/5">
                    1</div>
                <div class="pt-1">
                    <h4 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight mb-1">
                        {{ __('messages.profile_referral_step_1_title') }}</h4>
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest leading-relaxed">
                        {{ __('messages.profile_referral_step_1_desc') }}</p>
                </div>
            </div>
            <div class="flex gap-5 group">
                <div
                    class="w-12 h-12 rounded-[1.25rem] bg-black/5 dark:bg-white/5 flex items-center justify-center text-gray-900 dark:text-white font-black shrink-0 transition-all group-hover:rotate-6 group-hover:scale-110 shadow-sm border border-black/5 dark:border-white/5">
                    2</div>
                <div class="pt-1">
                    <h4 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight mb-1">
                        {{ __('messages.profile_referral_step_2_title') }}</h4>
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest leading-relaxed">
                        {{ __('messages.profile_referral_step_2_desc') }}</p>
                </div>
            </div>
            <div class="flex gap-5 group">
                <div
                    class="w-12 h-12 rounded-[1.25rem] bg-green-500/10 flex items-center justify-center text-green-500 font-black shrink-0 transition-all group-hover:rotate-6 group-hover:scale-110 shadow-sm border border-green-500/5">
                    3</div>
                <div class="pt-1">
                    <h4 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight mb-1">
                        {{ __('messages.profile_referral_step_3_title') }}</h4>
                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest leading-relaxed">
                        {{ __('messages.profile_referral_step_3_desc') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════ --}}
    {{-- 4. RECENT INVITES               --}}
    {{-- ═══════════════════════════════ --}}
    <div class="animate-in fade-in slide-in-from-bottom-4 duration-700 delay-[600ms]">
        <x-section-header title="آخر الدعوات" />

        <div class="space-y-3 mt-4">
            @forelse($this->recentReferrals as $referral)
                <div wire:key="referral-{{ $referral->id }}" wire:click="openReferral({{ $referral->id }})"
                    class="liquid-glass rounded-[1.5rem] p-4 border border-white/20 dark:border-white/5 shadow-sm flex items-center justify-between group hover:bg-black/[0.02] dark:hover:bg-white/[0.02] transition-all cursor-pointer active:scale-[0.98]">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-[1rem] bg-banhafade-accent/10 flex items-center justify-center text-xs font-black text-banhafade-accent">
                                {{ mb_substr($referral->invitee->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight">
                                    {{ $referral->invitee->name }}</p>
                                <p class="text-[8px] text-gray-400 font-bold uppercase tracking-widest mt-0.5">
                                    {{ $referral->created_at->translatedFormat('d M Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        @if ($referral->status === \App\Enums\ReferralStatus::Rewarded && $referral->coupon)
                            <div
                                class="w-6 h-6 rounded-lg bg-green-500/10 flex items-center justify-center text-green-600 animate-pulse">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        @endif
                        <span @class([
                            'text-[8px] font-black uppercase tracking-[0.1em] px-2.5 py-1.5 rounded-xl border',
                            'bg-green-500/10 text-green-600 border-green-500/10' =>
                                $referral->status === \App\Enums\ReferralStatus::Rewarded,
                            'bg-yellow-500/10 text-yellow-600 border-yellow-500/10' =>
                                $referral->status === \App\Enums\ReferralStatus::Pending,
                            'bg-red-500/10 text-red-600 border-red-500/10' =>
                                $referral->status === \App\Enums\ReferralStatus::Skipped,
                        ])>
                            {{ $referral->status->getLabel() }}
                        </span>
                    </div>
                </div>
            @empty
                <x-empty-state title="{{ __('messages.profile_referrals_empty') }}"
                    description="{{ __('messages.profile_referrals_empty_desc') }}">
                    <x-slot name="icon">
                        <svg class="w-12 h-12 opacity-20 text-gray-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </x-slot>
                </x-empty-state>
            @endforelse
        </div>
    </div>

    {{-- Referral Details Bottom Sheet --}}
    <x-bottom-sheet wire:model="selectedReferralId" title="{{ __('messages.referral_details_title') }}">
        <x-slot:icon>
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                    d="M13 10V3L4 14h7v7l9-11h-7z">
                </path>
            </svg>
        </x-slot:icon>

        @if ($this->selectedReferral)
            <div class="space-y-8 animate-in zoom-in-95 duration-300">
                {{-- Premium Header Card (Banner Style) --}}
                <div class="relative rounded-[2.5rem] overflow-hidden group shadow-2xl">
                    <div class="h-32 bg-gradient-to-br from-banhafade-accent/80 to-purple-700/80">
                        <div
                            class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/carbon-fibre.png')] opacity-20">
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                    </div>

                    <div class="absolute bottom-5 left-6 right-6 flex items-center gap-4">
                        <div
                            class="w-16 h-16 rounded-2xl border-4 border-white dark:border-gray-900 shadow-xl overflow-hidden shrink-0 bg-white flex items-center justify-center text-xl font-black text-banhafade-accent">
                            {{ mb_substr($this->selectedReferral->invitee->name, 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-lg font-black text-white leading-none uppercase tracking-tight">
                                {{ $this->selectedReferral->invitee->name }}
                            </h3>
                            <p class="text-white/70 text-[10px] font-bold uppercase mt-1 tracking-widest">
                                {{ __('messages.referral_joined_on') }}
                                {{ $this->selectedReferral->created_at->translatedFormat('d M Y') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Status Body --}}
                <div class="text-center space-y-2 px-2">
                    <h2 class="text-2xl font-black text-gray-900 dark:text-white leading-tight">
                        @if ($this->selectedReferral->status === \App\Enums\ReferralStatus::Rewarded)
                            @if ($this->selectedReferral->coupon)
                                @if ($this->selectedReferral->coupon->discount_type === \App\Enums\DiscountType::Percentage)
                                    {{ __('messages.offers_discount_percentage', ['value' => (int) $this->selectedReferral->coupon->discount_value]) }}
                                @else
                                    {{ __('messages.offers_discount_fixed', ['value' => (int) $this->selectedReferral->coupon->discount_value]) }}
                                @endif
                            @else
                                {{ __('messages.referral_reward_unlocked') }}
                            @endif
                        @elseif($this->selectedReferral->status === \App\Enums\ReferralStatus::Pending)
                            {{ __('messages.referral_reward_pending_title') }}
                        @else
                            {{ __('messages.referral_reward_skipped_title') }}
                        @endif
                    </h2>
                    <p class="text-sm font-bold text-gray-500 dark:text-gray-400 leading-relaxed px-4">
                        @if ($this->selectedReferral->status === \App\Enums\ReferralStatus::Rewarded)
                            {{ __('messages.referral_reward_earned_for', ['name' => $this->selectedReferral->invitee->name]) }}
                        @elseif($this->selectedReferral->status === \App\Enums\ReferralStatus::Pending)
                            {{ __('messages.referral_reward_pending_desc_for', ['name' => $this->selectedReferral->invitee->name]) }}
                        @else
                            {{ __('messages.referral_reward_skipped_desc') }}
                        @endif
                    </p>
                </div>

                {{-- Stats Grid (Mirroring Offers) --}}
                <div class="grid grid-cols-2 gap-4">
                    <div
                        class="p-5 rounded-[2rem] bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/5 flex flex-col items-center justify-center space-y-1">
                        <span
                            class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">{{ __('messages.offers_modal_discount') }}</span>
                        <span class="text-2xl font-black text-banhafade-accent">
                            @if ($this->selectedReferral->status === \App\Enums\ReferralStatus::Rewarded && $this->selectedReferral->coupon)
                                @if ($this->selectedReferral->coupon->discount_type === \App\Enums\DiscountType::Percentage)
                                    {{ (int) $this->selectedReferral->coupon->discount_value }}%
                                @else
                                    {{ (int) $this->selectedReferral->coupon->discount_value }} <span
                                        class="text-xs">{{ __('messages.egp') }}</span>
                                @endif
                            @else
                                <span class="text-lg opacity-30">—</span>
                            @endif
                        </span>
                    </div>
                    <div
                        class="p-5 rounded-[2rem] bg-black/5 dark:bg-white/5 border border-black/5 dark:border-white/5 flex flex-col items-center justify-center space-y-1">
                        <span
                            class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">{{ __('messages.offers_modal_expires') }}</span>
                        <span class="text-sm font-black text-gray-700 dark:text-gray-300">
                            @if ($this->selectedReferral->coupon?->end_date)
                                {{ $this->selectedReferral->coupon->end_date->translatedFormat('d M Y') }}
                            @else
                                <span class="text-2xl leading-none">∞</span>
                            @endif
                        </span>
                    </div>
                </div>

                {{-- Action Card: Reward Coupon --}}
                @if ($this->selectedReferral->status === \App\Enums\ReferralStatus::Rewarded && $this->selectedReferral->coupon)
                    <div class="relative group">
                        {{-- Coupon Card (Compact & Elegant) --}}
                        <div
                            class="relative flex items-center bg-gradient-to-br from-indigo-600 to-purple-700 rounded-3xl p-6 overflow-hidden border border-white/20 shadow-2xl">
                            {{-- Punch holes --}}
                            <div
                                class="absolute -left-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-white dark:bg-gray-900 shadow-inner">
                            </div>
                            <div
                                class="absolute -right-3 top-1/2 -translate-y-1/2 w-6 h-6 rounded-full bg-white dark:bg-gray-900 shadow-inner">
                            </div>

                            {{-- Left: Code --}}
                            <div class="flex-grow ps-2">
                                <span
                                    class="text-[9px] font-black text-white/50 uppercase tracking-[0.2em] block mb-1">{{ __('messages.referral_reward_code') }}</span>
                                <div class="text-2xl font-black text-white tracking-widest uppercase">
                                    {{ $this->selectedReferral->coupon->code }}</div>
                            </div>

                            {{-- Center: Divider --}}
                            <div class="h-10 w-px bg-dashed border-l-2 border-dashed border-white/20 mx-4"></div>

                            {{-- Right: Copy --}}
                            <div class="shrink-0 pe-2">
                                <x-copy-button :value="$this->selectedReferral->coupon->code"
                                    class="!bg-white !text-indigo-600 !w-12 !h-12 !rounded-2xl shadow-lg border-0 cursor-pointer" />
                            </div>
                        </div>
                    </div>
                @endif

                <p class="text-[10px] font-medium text-gray-400 text-center px-8">
                    {{ __('messages.referral_sheet_footer_hint') }}
                </p>
            </div>
        @endif
    </x-bottom-sheet>
</div>
