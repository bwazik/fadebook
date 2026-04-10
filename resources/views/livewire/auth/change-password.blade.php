<div class="pt-16 pb-8 px-2 w-full" x-data="{
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
    <div class="w-full">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ __('messages.change_password_title') }}</h1>
            <p class="text-gray-600 dark:text-gray-300">{{ __('messages.change_password_subtitle') }}</p>
        </div>

        <!-- Step 1: Confirmation -->
        <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
            <div class="space-y-6">
                <p class="text-sm text-gray-600 dark:text-gray-400 text-center">
                    {!! __('messages.change_password_confirmation_hint', ['phone' => '<span dir="ltr" class="inline-block font-bold">' . $maskedPhone . '</span>']) !!}
                </p>

                <div class="mb-4 space-y-4">
                    <x-ios-input label="{{ __('messages.current_password') }}" wire:model="current_password" type="password" id="current_password" dir="ltr" placeholder="••••••••" />
                </div>

                <x-ios-button wire:click="sendOtp" type="button" target="sendOtp" class="mt-0">{{ __('messages.send_verification_code') }}</x-ios-button>
            </div>
        </div>

        <!-- Step 2: OTP -->
        <div x-show="step === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
            <form wire:submit="verifyOtp" class="space-y-4">
                <p class="text-sm text-center text-gray-600 dark:text-gray-400 mb-4">{{ __('messages.otp_whatsapp_hint', ['phone' => $phone]) }}</p>
                <div class="mb-4">
                    <x-otp-input model="otp" :digits="6" />
                </div>

                <div class="flex gap-3">
                    <div class="w-1/3">
                        <x-ios-button type="button" wire:click="goBack" variant="secondary">{{ __('messages.back') }}</x-ios-button>
                    </div>
                    <div class="w-2/3">
                        <x-ios-button target="verifyOtp">{{ __('messages.confirm_otp') }}</x-ios-button>
                    </div>
                </div>
            </form>

            <div class="mt-6 text-sm text-gray-500 font-tajawal text-center">
                {{ __('messages.didnt_get_code') }}
                <button type="button" wire:click="resendOtp" wire:loading.attr="disabled" :disabled="cooldown > 0" :class="cooldown > 0 ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'" class="text-fadebook-accent hover:text-red-600 hover:underline font-bold transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
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
                <div class="mb-4 space-y-4">
                    <x-ios-input label="{{ __('messages.new_password') }}" wire:model="password" type="password" id="new_password" dir="ltr" placeholder="••••••••" />
                    <x-ios-input label="{{ __('messages.confirm_password') }}" wire:model="password_confirmation" type="password" id="password_confirmation" dir="ltr" placeholder="••••••••" />
                </div>

                <x-ios-button target="resetPassword">{{ __('messages.save_password') }}</x-ios-button>
            </form>
        </div>
    </div>
</div>
