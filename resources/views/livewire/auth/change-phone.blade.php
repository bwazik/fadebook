<div>
    <div class="pt-16 pb-8 px-2 w-full shrink-0">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold mb-4 font-tajawal">{{ __('messages.change_phone_title') }}</h2>
        </div>

        @if ($step === 1)
            <div class="mb-6 text-center">
                <p class="text-gray-500 font-medium text-sm mb-1">{{ __('messages.current_phone') }}:</p>
                <p class="text-xl font-bold text-gray-800 dark:text-gray-200" dir="ltr">{{ $currentPhone }}</p>

                @if (!$canChange && $nextChangeDate)
                    <p class="text-xs text-red-500 mt-2 font-medium">
                        {{ __('messages.phone_change_limit', ['date' => $nextChangeDate->format('Y-m-d')]) }}
                    </p>
                @endif
            </div>

            <form wire:submit="verifyPasswordAndSendOtp" class="space-y-4">
                <div class="mb-4 space-y-4">
                    <x-ios-input label="{{ __('messages.current_password') }}" wire:model="current_password"
                        type="password" placeholder="{{ __('messages.current_password') }}" :disabled="!$canChange"
                        dir="ltr" />
                    <x-ios-input label="{{ __('messages.new_phone') }}" wire:model="new_phone" type="tel"
                        placeholder="01xxxxxxxxx" dir="ltr" :disabled="!$canChange" />
                </div>

                <x-ios-button type="submit" target="verifyPasswordAndSendOtp"
                    :disabled="!$canChange">{{ __('messages.send_verification_code') }}</x-ios-button>
            </form>
        @endif

        @if ($step === 2)
            <div class="text-center mb-8">
                <p class="text-gray-600 dark:text-gray-400 font-tajawal text-sm leading-relaxed">
                    {!! __('messages.verify_phone_hint', [
                        'phone' => '<span class="font-bold text-gray-800 dark:text-gray-200" dir="ltr">' . $new_phone . '</span>',
                    ]) !!}
                </p>
            </div>

            <form wire:submit="verifyOtp" class="space-y-4">
                <div class="mb-4">
                    <x-otp-input model="otp_code" :digits="6" />
                </div>

                <div class="pt-2 space-y-3">
                    <x-ios-button target="verifyOtp">{{ __('messages.confirm_change') }}</x-ios-button>

                    <button type="button" wire:click="goBack"
                        class="w-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold py-3 px-4 rounded-2xl transition duration-200 cursor-pointer">
                        {{ __('messages.go_back_change_number') }}
                    </button>
                </div>
            </form>

            <div class="mt-6 text-sm text-gray-500 font-tajawal text-center">
                {{ __('messages.didnt_get_code') }}
                <button type="button" wire:click="resendOtp" wire:loading.attr="disabled"
                    class="text-banhafade-accent hover:text-red-600 font-bold transition-colors disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                    <span wire:loading.remove wire:target="resendOtp">{{ __('messages.resend_code') }}</span>
                    <span wire:loading wire:target="resendOtp">{{ __('messages.sending_code') }}</span>
                </button>
            </div>
        @endif
    </div>
</div>
