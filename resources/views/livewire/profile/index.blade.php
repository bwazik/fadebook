<div class="pb-[calc(5rem+var(--safe-area-bottom)+64px)] relative min-h-screen pt-4 px-4">
    {{-- Header --}}
    <div class="mb-6 mt-4">
        <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">
            {{ __('messages.profile_title') }}
        </h1>
    </div>

    <div class="space-y-10">
        {{-- Profile Info Card --}}
        <section class="animate-in fade-in slide-in-from-bottom-4 duration-700 delay-150">
            <div
                class="liquid-glass rounded-[2.5rem] p-7 border border-white/40 dark:border-white/5 shadow-2xl relative overflow-hidden">
                {{-- Decorative background in card --}}
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-fadebook-accent/10 rounded-full blur-2xl"></div>

                {{-- Header: Avatar + Info --}}
                <div class="flex items-center gap-5 mb-6 relative z-10 transition-all duration-300">
                    <x-photo-upload wireModel="avatar" :photo="$avatar" :current-photo="$this->user->getImage('avatar')->first()?->path" sizeClasses="w-20 h-20" />

                    <div class="min-w-0">
                        <h3 class="text-xl font-black text-gray-900 dark:text-white tracking-tight truncate">
                            {{ $this->user->name }}</h3>
                        <p class="text-[11px] text-gray-500 font-bold uppercase tracking-wider truncate mt-0.5">
                            {{ $this->user->phone }}</p>
                        @if ($this->user->email)
                            <p class="text-[10px] text-gray-400 font-bold truncate">{{ $this->user->email }}</p>
                        @endif
                    </div>
                </div>

                {{-- Badges Row --}}
                <div class="flex flex-wrap items-center gap-2 mb-8 relative z-10">
                    @foreach ($this->badges as $badge)
                        <span @class([
                            'px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border transition-all',
                            'bg-fadebook-accent/10 text-fadebook-accent border-fadebook-accent/20' =>
                                $badge['type'] === 'accent',
                            'bg-green-500/10 text-green-600 dark:text-green-400 border-green-500/20' =>
                                $badge['type'] === 'success',
                            'bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-white/60 border-black/5 dark:border-white/10' =>
                                $badge['type'] === 'gray',
                        ])>
                            {{ $badge['label'] }}
                        </span>
                    @endforeach
                </div>

                {{-- Stats Grid --}}
                <div class="grid grid-cols-3 gap-3 relative z-10">
                    <div
                        class="bg-black/5 dark:bg-white/5 rounded-3xl p-4 text-center group border border-transparent hover:border-fadebook-accent/10 transition-colors">
                        <p
                            class="text-xl font-black text-gray-900 dark:text-white group-hover:scale-110 transition-transform">
                            {{ $this->stats['total_bookings'] }}</p>
                        <span
                            class="text-[8px] font-black text-gray-400 uppercase tracking-widest mt-1 block">{{ __('messages.profile_total_bookings') }}</span>
                    </div>
                    <div
                        class="bg-black/5 dark:bg-white/5 rounded-3xl p-4 text-center group border border-transparent hover:border-green-500/10 transition-colors">
                        <p
                            class="text-xl font-black text-gray-900 dark:text-white group-hover:scale-110 transition-transform">
                            {{ $this->stats['completed_bookings'] }}</p>
                        <span
                            class="text-[8px] font-black text-gray-400 uppercase tracking-widest mt-1 block">{{ __('messages.profile_completed_bookings') }}</span>
                    </div>
                    <div
                        class="bg-black/5 dark:bg-white/5 rounded-3xl p-4 text-center group border border-transparent hover:border-red-500/10 transition-colors">
                        <p
                            class="text-xl font-black text-gray-900 dark:text-white group-hover:scale-110 transition-transform">
                            {{ $this->stats['canceled_bookings'] }}</p>
                        <span
                            class="text-[8px] font-black text-gray-400 uppercase tracking-widest mt-1 block">{{ __('messages.profile_canceled_bookings') }}</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Account Section -->
        <section class="animate-in fade-in slide-in-from-bottom-4 duration-700 delay-300 px-1">
            <x-section-header :title="__('messages.profile_account_section')" />

            <x-ios-input-group class="mt-4">
                <a href="{{ route('profile.edit') }}" wire:navigate
                    class="flex items-center justify-between p-4 border-b border-black/5 dark:border-white/10 last:border-0 hover:bg-black/5 dark:hover:bg-white/5 transition-colors group">
                    <span class="text-sm font-medium">{{ __('messages.profile_edit_info') }}</span>
                    <svg class="w-4 h-4 text-gray-400 rtl:rotate-180 group-hover:translate-x-[-4px] transition-transform"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>

                <a href="{{ route('profile.referral') }}" wire:navigate
                    class="flex items-center justify-between p-4 border-b border-black/5 dark:border-white/10 last:border-0 hover:bg-black/5 dark:hover:bg-white/5 transition-colors group">
                    <span class="text-sm font-medium">{{ __('messages.profile_referral_title') }}</span>
                    <svg class="w-4 h-4 text-gray-400 rtl:rotate-180 group-hover:translate-x-[-4px] transition-transform"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
            </x-ios-input-group>
        </section>

        <!-- App Settings Section -->
        <section class="animate-in fade-in slide-in-from-bottom-4 duration-700 delay-[450ms] px-1">
            <x-section-header :title="__('messages.profile_app_settings')" />

            <x-ios-input-group class="mt-4">
                <a href="{{ route('profile.settings') }}" wire:navigate
                    class="flex items-center justify-between p-4 border-b border-black/5 dark:border-white/10 last:border-0 hover:bg-black/5 dark:hover:bg-white/5 transition-colors group">
                    <span class="text-sm font-medium">{{ __('messages.profile_appearance_section') }}</span>
                    <svg class="w-4 h-4 text-gray-400 rtl:rotate-180 group-hover:translate-x-[-4px] transition-transform"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>

                <button wire:click="$set('showContactSheet', true)"
                    class="w-full flex items-center justify-between p-4 border-b border-black/5 dark:border-white/10 last:border-0 hover:bg-black/5 dark:hover:bg-white/5 transition-colors group cursor-pointer">
                    <span class="text-sm font-medium">{{ __('messages.profile_contact_dev') }}</span>
                    <svg class="w-4 h-4 text-gray-400 rtl:rotate-180 group-hover:translate-x-[-4px] transition-transform"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <button wire:click="$set('showTermsSheet', true)"
                    class="w-full flex items-center justify-between p-4 border-b border-black/5 dark:border-white/10 last:border-0 hover:bg-black/5 dark:hover:bg-white/5 transition-colors group cursor-pointer">
                    <span class="text-sm font-medium">{{ __('messages.profile_terms') }}</span>
                    <svg class="w-4 h-4 text-gray-400 rtl:rotate-180 group-hover:translate-x-[-4px] transition-transform"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>

                <button wire:click="$set('showPrivacySheet', true)"
                    class="w-full flex items-center justify-between p-4 last:border-0 hover:bg-black/5 dark:hover:bg-white/5 transition-colors group cursor-pointer">
                    <span class="text-sm font-medium">{{ __('messages.profile_privacy') }}</span>
                    <svg class="w-4 h-4 text-gray-400 rtl:rotate-180 group-hover:translate-x-[-4px] transition-transform"
                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
            </x-ios-input-group>
        </section>

        {{-- Logout --}}
        <div class="mt-12 px-1 animate-in fade-in slide-in-from-bottom-4 duration-700 delay-300" x-data>
            <x-ios-button type="button"
                @click="$dispatch('open-ios-alert', {
                    title: '{{ __('messages.profile_logout_btn') }}',
                    message: '{{ __('messages.profile_logout_confirm_msg') }}',
                    action: 'logout',
                    componentId: '{{ $_instance->getId() }}'
                })"
                variant="danger">
                {{ __('messages.profile_logout_btn') }}
            </x-ios-button>
        </div>
    </div>

    {{-- Terms Bottom Sheet --}}
    <x-bottom-sheet wire:model="showTermsSheet" :title="__('messages.profile_terms')">
        <x-slot:icon>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-8 h-8">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
            </svg>
        </x-slot:icon>

        <div class="space-y-4 max-h-[55vh] overflow-y-auto px-1 no-scrollbar mb-6" dir="rtl">
            @foreach (explode("\n\n", $this->termsContent) as $paragraph)
                <p class="text-[13px] text-gray-700 dark:text-gray-300 font-bold leading-relaxed">
                    {{ $paragraph }}
                </p>
            @endforeach
        </div>

        <x-ios-button wire:click="$set('showTermsSheet', false)" variant="secondary">
            {{ __('messages.booking_terms_modal_close') }}
        </x-ios-button>
    </x-bottom-sheet>

    {{-- Privacy Bottom Sheet --}}
    <x-bottom-sheet wire:model="showPrivacySheet" :title="__('messages.profile_privacy')">
        <x-slot:icon>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-8 h-8">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
            </svg>
        </x-slot:icon>

        <div class="space-y-4 max-h-[55vh] overflow-y-auto px-1 no-scrollbar mb-6" dir="rtl">
            @foreach (explode("\n\n", $this->privacyContent) as $paragraph)
                <p class="text-[13px] text-gray-700 dark:text-gray-300 font-bold leading-relaxed">
                    {{ $paragraph }}
                </p>
            @endforeach
        </div>

        <x-ios-button wire:click="$set('showPrivacySheet', false)" variant="secondary">
            {{ __('messages.booking_terms_modal_close') }}
        </x-ios-button>
    </x-bottom-sheet>
    {{-- Contact Developer Bottom Sheet --}}
    <x-bottom-sheet wire:model="showContactSheet" :title="__('messages.profile_contact_dev')">
        <x-slot:icon>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="w-8 h-8">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15.182 15.182a4.5 4.5 0 0 1-6.364 0M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0ZM9.75 9.75c0 .414-.168.75-.375.75S9 10.164 9 9.75 9.168 9 9.375 9s.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Zm5.625 0c0 .414-.168.75-.375.75s-.375-.336-.375-.75.168-.75.375-.75.375.336.375.75Zm-.375 0h.008v.015h-.008V9.75Z" />
            </svg>
        </x-slot:icon>

        <div class="space-y-4 max-h-[55vh] overflow-y-auto px-1 no-scrollbar mb-6" dir="rtl">
            @foreach (explode("\n\n", $this->contactDeveloperContent) as $paragraph)
                <p class="text-[13px] text-gray-700 dark:text-gray-300 font-bold leading-relaxed">
                    {{ $paragraph }}
                </p>
            @endforeach
        </div>

        <div class="flex flex-col gap-3">
            <x-ios-button href="https://wa.me/{{ $this->developerWhatsapp }}" target="_blank">
                {{ __('messages.profile_contact_dev_whatsapp') }}
            </x-ios-button>

            <x-ios-button wire:click="$set('showContactSheet', false)" variant="secondary">
                {{ __('messages.booking_terms_modal_close') }}
            </x-ios-button>
        </div>
    </x-bottom-sheet>
</div>
