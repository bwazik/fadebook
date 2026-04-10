<div class="flex flex-col items-center justify-center min-h-[80vh] px-4">
    <div class="glass w-full max-w-md p-8 text-center shrink-0">
        <h2 class="text-2xl font-bold mb-4 font-tajawal">{{ __('messages.change_phone_title') }}</h2>
        
        @if($step === 1)
            <div class="mb-6">
                <p class="text-gray-500 font-medium text-sm mb-1">{{ __('messages.current_phone') }}:</p>
                <p class="text-xl font-bold text-gray-800 dark:text-gray-200" dir="ltr">{{ $currentPhone }}</p>
                
                @if(!$canChange && $nextChangeDate)
                <p class="text-xs text-red-500 mt-2 font-medium">
                    {{ __('messages.phone_change_limit', ['date' => $nextChangeDate->format('Y-m-d')]) }}
                </p>
                @endif
            </div>

            <form wire:submit="verifyPasswordAndSendOtp" class="space-y-4">
                <div>
                    <input wire:model="current_password" type="password" class="w-full bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-fadebook-accent text-right transition-colors" placeholder="{{ __('messages.current_password') }}" @if(!$canChange) disabled @endif>
                    @error('current_password') <span class="text-red-500 text-xs mt-1 block text-right font-medium">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <input wire:model="new_phone" type="tel" inputmode="numeric" class="w-full bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-fadebook-accent text-left transition-colors" placeholder="01xxxxxxxxx" dir="ltr" @if(!$canChange) disabled @endif>
                    @error('new_phone') <span class="text-red-500 text-xs mt-1 block text-right font-medium">{{ $message }}</span> @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" @if(!$canChange) disabled @endif class="w-full bg-fadebook-accent hover:bg-red-600 text-white font-bold py-3 px-4 rounded-xl transition duration-200 shadow-md disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="verifyPasswordAndSendOtp">{{ __('messages.send_verification_code') }}</span>
                        <span wire:loading wire:target="verifyPasswordAndSendOtp">{{ __('messages.sending_code') }}</span>
                    </button>
                </div>
            </form>
        @endif

        @if($step === 2)
            <p class="text-gray-600 dark:text-gray-400 mb-6 font-tajawal text-sm leading-relaxed">
                {!! __('messages.verify_phone_hint', ['phone' => '<span class="font-bold text-gray-800 dark:text-gray-200" dir="ltr">' . $new_phone . '</span>']) !!}
            </p>

            <form wire:submit="verifyOtp" class="space-y-5">
                <div>
                    <input wire:model="otp_code" type="text" inputmode="numeric" pattern="[0-9]*" class="w-full bg-white/50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-fadebook-accent text-center tracking-[0.5em] text-2xl font-bold transition-colors" placeholder="••••••" maxlength="6" dir="ltr">
                    @error('otp_code') <span class="text-red-500 text-xs mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div class="pt-2 space-y-3">
                    <button type="submit" class="w-full bg-fadebook-accent hover:bg-red-600 text-white font-bold py-3 px-4 rounded-xl transition duration-200 shadow-md">
                        <span wire:loading.remove wire:target="verifyOtp">{{ __('messages.confirm_change') }}</span>
                        <span wire:loading wire:target="verifyOtp">{{ __('messages.confirming') }}</span>
                    </button>
                    
                    <button type="button" wire:click="goBack" class="w-full bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-bold py-3 px-4 rounded-xl transition duration-200">
                        {{ __('messages.go_back_change_number') }}
                    </button>
                </div>
            </form>

            <div class="mt-6 text-sm text-gray-500 font-tajawal">
                {{ __('messages.didnt_get_code') }} 
                <button type="button" wire:click="resendOtp" wire:loading.attr="disabled" class="text-fadebook-accent hover:text-red-700 font-bold transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    <span wire:loading.remove wire:target="resendOtp">{{ __('messages.resend_code') }}</span>
                    <span wire:loading wire:target="resendOtp">{{ __('messages.sending_code') }}</span>
                </button>
            </div>
        @endif
    </div>
</div>
