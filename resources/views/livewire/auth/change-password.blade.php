<div class="min-h-screen flex items-center justify-center p-4" x-data="{ 
    step: @entangle('step'),
    cooldown: 0, 
    timer: null,
    startCooldown(seconds) { 
        this.cooldown = seconds;
        if (this.timer) clearInterval(this.timer);
        this.timer = setInterval(() => {
            this.cooldown--;
            if (this.cooldown <= 0) clearInterval(this.timer);
        }, 1000);
    }
}" @resend-cooldown.window="startCooldown($event.detail.seconds)">
    <div class="glass w-full max-w-md p-8 rounded-3xl shadow-lg border border-white/20">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ __('messages.change_password_title') }}</h1>
            <p class="text-gray-600 dark:text-gray-300">{{ __('messages.change_password_subtitle') }}</p>
        </div>

        <!-- Step 1: Confirmation -->
        <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
            <div class="space-y-6 text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('messages.change_password_confirmation_hint', ['phone' => substr($phone, 0, 3) . '*******' . substr($phone, -2)]) }}
                </p>

                <div class="text-right">
                    <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.current_password') }}</label>
                    <input wire:model="current_password" type="password" id="current_password" dir="ltr" class="w-full bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-left" placeholder="••••••••" required>
                </div>

                <button type="button" wire:click="sendOtp" class="w-full bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-bold rounded-xl py-3.5 hover:opacity-90 transition active:scale-[0.98] shadow-md relative disabled:opacity-50 disabled:cursor-not-allowed" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="sendOtp">{{ __('messages.send_verification_code') }}</span>
                    <span wire:loading wire:target="sendOtp">{{ __('messages.sending_code') }}</span>
                </button>
            </div>
        </div>

        <!-- Step 2: OTP -->
        <div x-show="step === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
            <form wire:submit="verifyOtp" class="space-y-4">
                <p class="text-sm text-center text-gray-600 dark:text-gray-400 mb-4">{{ __('messages.otp_whatsapp_hint', ['phone' => $phone]) }}</p>
                <div>
                    <label for="otp" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.otp_code') }}</label>
                    <input wire:model="otp" type="text" id="otp" dir="ltr" class="w-full bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-center tracking-widest text-lg font-bold" placeholder="123456" required maxlength="6">
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="button" wire:click="goBack" class="w-1/3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold rounded-xl py-3.5 hover:opacity-90 transition active:scale-[0.98] shadow-sm">
                        {{ __('messages.back') }}
                    </button>
                    <button type="submit" class="w-2/3 bg-blue-600 text-white font-bold rounded-xl py-3.5 hover:bg-blue-700 transition active:scale-[0.98] shadow-md relative disabled:opacity-50 disabled:cursor-not-allowed" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="verifyOtp">{{ __('messages.confirm_otp') }}</span>
                        <span wire:loading wire:target="verifyOtp">{{ __('messages.confirming') }}</span>
                    </button>
                </div>
            </form>

            <div class="mt-6 text-sm text-gray-500 font-tajawal text-center">
                {{ __('messages.didnt_get_code') }} 
                <button type="button" wire:click="resendOtp" wire:loading.attr="disabled" :disabled="cooldown > 0" :class="cooldown > 0 ? 'opacity-50 cursor-not-allowed' : ''" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 hover:underline font-bold transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="resendOtp">
                        <span x-show="cooldown <= 0">{{ __('messages.resend_code') }}</span>
                        <span x-show="cooldown > 0" x-text="`إعادة الإرسال بعد ${cooldown}ث`" dir="rtl"></span>
                    </span>
                    <span wire:loading wire:target="resendOtp">{{ __('messages.sending_code') }}</span>
                </button>
            </div>
        </div>

        <!-- Step 3: New Password -->
        <div x-show="step === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
            <form wire:submit="resetPassword" class="space-y-4">
                <div>
                    <label for="new_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.new_password') }}</label>
                    <input wire:model="password" type="password" id="new_password" dir="ltr" class="w-full bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-left" placeholder="••••••••" required>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.confirm_password') }}</label>
                    <input wire:model="password_confirmation" type="password" id="password_confirmation" dir="ltr" class="w-full bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-left" placeholder="••••••••" required>
                </div>

                <button type="submit" class="w-full bg-green-600 text-white font-bold rounded-xl py-3.5 hover:bg-green-700 transition active:scale-[0.98] mt-6 shadow-md relative disabled:opacity-50 disabled:cursor-not-allowed" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="resetPassword">{{ __('messages.save_password') }}</span>
                    <span wire:loading wire:target="resetPassword">{{ __('messages.processing') }}</span>
                </button>
            </form>
        </div>
    </div>
</div>
