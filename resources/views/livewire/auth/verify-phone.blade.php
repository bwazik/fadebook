<div class="flex flex-col items-center justify-center min-h-[80vh]" x-data="{ 
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
    <div class="glass w-full p-8 text-center">
        @if($type === \App\Enums\OtpType::PasswordReset)
            <h2 class="text-2xl font-bold mb-4 font-tajawal">{{ __('messages.reset_password_title') }}</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-6 font-tajawal text-sm leading-relaxed">
                {{ __('messages.reset_password_hint') }}
            </p>
        @else
            <h2 class="text-2xl font-bold mb-4 font-tajawal">{{ __('messages.verify_phone_title') }}</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-6 font-tajawal text-sm leading-relaxed">
                {!! __('messages.verify_phone_hint', ['phone' => '<span class="font-bold text-gray-800 dark:text-gray-200" dir="ltr">' . $phone . '</span>']) !!}
            </p>
        @endif

        <form wire:submit="verify" class="space-y-4">
            <div>
                <input wire:model="otp" type="text" inputmode="numeric" pattern="[0-9]*" class="w-full bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-fadebook-accent text-center tracking-widest text-2xl font-bold" placeholder="• • • • • •" maxlength="6" dir="ltr">
            </div>

            <button type="submit" class="w-full bg-fadebook-accent hover:bg-red-600 text-white font-bold py-3 px-4 rounded-xl transition duration-200 shadow-md">
                {{ __('messages.confirm') }}
            </button>
        </form>

        <div class="mt-6 text-sm text-gray-500 font-tajawal text-center">
            {{ __('messages.didnt_get_code') }} 
            <button type="button" wire:click="resend" wire:loading.attr="disabled" :disabled="cooldown > 0" :class="cooldown > 0 ? 'opacity-50 cursor-not-allowed' : ''" class="text-fadebook-accent hover:text-red-700 font-bold transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                <span wire:loading.remove wire:target="resend">
                    <span x-show="cooldown <= 0">{{ __('messages.resend_code') }}</span>
                    <span x-show="cooldown > 0" x-text="`إعادة الإرسال بعد ${cooldown}ث`" dir="rtl"></span>
                </span>
                <span wire:loading wire:target="resend">{{ __('messages.sending_code') }}</span>
            </button>
        </div>
    </div>
</div>
