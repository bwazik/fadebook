<div class="pb-[calc(5rem+var(--safe-area-bottom)+64px)] relative min-h-screen pt-4 px-4">
    <x-sticky-back-button href="{{ route('profile.index') }}" />

    {{-- Header --}}
    <div class="mb-6 mt-4">
        <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">
            {{ __('messages.profile_app_settings') }}
        </h1>
    </div>

    <div class="space-y-10">
        {{-- Appearance Settings --}}
        <section class="animate-in fade-in slide-in-from-bottom-4 duration-700 delay-150">
            <x-section-header :title="__('messages.profile_appearance_section')" />

            <div class="mt-4 space-y-6 px-2">
                {{-- Dark Mode Toggle --}}
                <div class="flex items-center justify-between py-2">
                    <div class="space-y-1">
                        <span
                            class="text-base font-black text-gray-900 dark:text-white tracking-tight">{{ __('messages.profile_dark_mode') }}</span>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                            {{ __('messages.profile_dark_mode_desc') }}</p>
                    </div>
                    <div x-data="{
                        isDark: document.documentElement.classList.contains('dark'),
                        toggle() {
                            this.isDark = !this.isDark;
                            document.documentElement.classList.toggle('dark', this.isDark);
                            localStorage.setItem('darkMode', this.isDark);
                        }
                    }">
                        <x-ios-toggle x-model="isDark" @click="toggle()" />
                    </div>
                </div>

                {{-- Accent Color --}}
                <div class="space-y-4 pt-2">
                    <div class="space-y-1">
                        <span
                            class="text-base font-black text-gray-900 dark:text-white tracking-tight">{{ __('messages.profile_accent_color') }}</span>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                            {{ __('messages.profile_accent_color_desc') }}</p>
                    </div>
                    <x-accent-picker />
                </div>
            </div>
        </section>

        {{-- Notifications Settings --}}
        <section class="animate-in fade-in slide-in-from-bottom-4 duration-700 delay-300">
            <x-section-header :title="__('messages.profile_notifications_section')" />

            <div class="mt-4 px-2 space-y-4">
                <div class="flex items-center justify-between py-2">
                    <div class="space-y-1">
                        <span
                            class="text-base font-black text-gray-900 dark:text-white tracking-tight">{{ __('messages.profile_whatsapp_alerts') }}</span>
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">
                            {{ __('messages.profile_whatsapp_alerts_desc') }}</p>
                    </div>
                    <x-ios-toggle checked disabled />
                </div>

                <div
                    class="p-4 rounded-[1.5rem] bg-gray-100 dark:bg-white/5 border border-black/5 dark:border-white/10">
                    <p
                        class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest leading-relaxed">
                        {{ __('messages.profile_whatsapp_alerts_disclaimer') }}
                    </p>
                </div>
            </div>
        </section>

        {{-- Version Footer --}}
        <div class="text-center py-6 animate-in fade-in zoom-in duration-1000 delay-600">
            <span
                class="text-[10px] font-black text-gray-300 dark:text-gray-700 uppercase tracking-[0.5em] font-mono">BanhaFade
                v1.0.0</span>
        </div>
    </div>
</div>
