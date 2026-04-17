<div x-data="{
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
    <div class="pt-16 pb-8 px-2 w-full">
        <div class="text-center mb-8">
            @if ($type === \App\Enums\OtpType::PasswordReset)
                <h2 class="text-2xl font-bold mb-4 font-tajawal">{{ __('messages.reset_password_title') }}</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-6 font-tajawal text-sm leading-relaxed">
                    {{ __('messages.reset_password_hint') }}
                </p>
            @else
                <h2 class="text-2xl font-bold mb-4 font-tajawal">{{ __('messages.verify_phone_title') }}</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-6 font-tajawal text-sm leading-relaxed">
                    {!! __('messages.verify_phone_hint', [
                        'phone' => '<span class="font-bold text-gray-800 dark:text-gray-200" dir="ltr">' . $phone . '</span>',
                    ]) !!}
                </p>
            @endif
        </div>

        <form wire:submit="verify" class="space-y-4">
            <div class="mb-4">
                <x-otp-input model="otp" :digits="6" />
            </div>

            <x-ios-button target="verify">{{ __('messages.confirm') }}</x-ios-button>
        </form>

        <div class="mt-6 text-sm text-gray-500 font-tajawal text-center">
            {{ __('messages.didnt_get_code') }}
            <button type="button" wire:click="resend" wire:loading.attr="disabled" :disabled="cooldown > 0"
                :class="cooldown > 0 ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'"
                class="text-banhafade-accent hover:text-red-600 font-bold transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                <span wire:loading.remove wire:target="resend">
                    <span x-show="cooldown <= 0">{{ __('messages.resend_code') }}</span>
                    <span x-show="cooldown > 0"
                        x-text="'{{ __('messages.otp_resend_countdown') }}'.replace(':seconds', cooldown)"
                        dir="rtl"></span>
                </span>
                <span wire:loading wire:target="resend">{{ __('messages.sending_code') }}</span>
            </button>
        </div>
    </div>
</div>
