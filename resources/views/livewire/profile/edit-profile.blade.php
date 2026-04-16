<div class="pb-[calc(5rem+var(--safe-area-bottom)+64px)] relative min-h-screen pt-4 px-4">
    <x-sticky-back-button href="{{ route('profile.index') }}" />

    {{-- Header --}}
    <div class="mb-6 mt-4">
        <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">
            {{ __('messages.profile_edit_info') }}
        </h1>
    </div>

    <div class="space-y-10">
        <!-- Personal Info Section -->
        <section class="animate-in fade-in slide-in-from-bottom-4 duration-700 delay-150">
            <x-section-header :title="__('messages.profile_personal_info')" />

            <div class="mt-4 px-2">
                <form wire:submit="updateProfile" class="space-y-6">
                    <x-ios-input label="{{ __('messages.profile_label_name') }}" wire:model="name" type="text" icon="m15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />

                    <x-ios-input label="{{ __('messages.profile_label_email') }}" wire:model="email" type="email" dir="ltr" icon="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />

                    <x-ios-input label="{{ __('messages.profile_label_birthday') }}" wire:model="birthday" type="date" icon="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 9v7.5m-9-3.75h.008v.008H12v-.008Z" />

                    <div class="pt-4">
                        <x-ios-button type="submit" wire:loading.attr="disabled" target="updateProfile">
                            <span wire:loading.remove wire:target="updateProfile">{{ __('messages.save_changes') }}</span>
                            <span wire:loading wire:target="updateProfile" class="flex items-center justify-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                {{ __('messages.saving') }}
                            </span>
                        </x-ios-button>
                    </div>
                </form>
            </div>
        </section>

        <!-- Secure Actions Section -->
        <section class="animate-in fade-in slide-in-from-bottom-4 duration-700 delay-300">
            <x-section-header :title="__('messages.profile_account_section')" />

            <x-ios-input-group class="mt-4">
                <!-- Change Phone -->
                <a href="{{ route('phone.change') }}" wire:navigate class="flex items-center justify-between p-4 border-b border-black/5 dark:border-white/10 last:border-0 hover:bg-black/5 dark:hover:bg-white/5 transition-colors group">
                    <div class="flex flex-col">
                        <span class="text-sm font-medium">{{ __('messages.profile_label_phone') }}</span>
                        <span class="text-[11px] text-gray-500 font-bold uppercase tracking-wider mt-0.5">{{ auth()->user()->phone }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                         <span class="text-[10px] font-black text-fadebook-accent uppercase bg-fadebook-accent/10 px-2 py-0.5 rounded-full">{{ __('messages.profile_change_phone_btn') ?? 'تغيير' }}</span>
                         <svg class="w-4 h-4 text-gray-400 rtl:rotate-180 group-hover:translate-x-[-4px] transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                        </svg>
                    </div>
                </a>

                <!-- Change Password -->
                <a href="{{ route('password.change') }}" wire:navigate class="flex items-center justify-between p-4 border-b border-black/5 dark:border-white/10 last:border-0 hover:bg-black/5 dark:hover:bg-white/5 transition-colors group">
                    <span class="text-sm font-medium">{{ __('messages.profile_change_password') }}</span>
                    <svg class="w-4 h-4 text-gray-400 rtl:rotate-180 group-hover:translate-x-[-4px] transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
            </x-ios-input-group>

            <div class="px-4 mt-2">
                <p class="text-[10px] text-gray-500 leading-relaxed">
                    {{ __('messages.profile_secure_actions_hint') ?? 'لتغيير رقم الهاتف أو كلمة المرور، ستحتاج إلى التحقق من هويتك لدواعي الأمان.' }}
                </p>
            </div>
        </section>
    </div>
</div>
