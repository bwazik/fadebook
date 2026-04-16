<div class="pt-16 pb-8 px-2 w-full">
    <div class="w-full">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">BanhaFade</h1>
            <p class="text-gray-600 dark:text-gray-300">{{ __('messages.login_welcome') }}</p>
        </div>

        <form wire:submit="authenticate" class="space-y-4">
            <div class="space-y-4">
                <x-ios-input label="{{ __('messages.phone') }}" wire:model="phone" type="tel" id="phone"
                    dir="ltr" placeholder="01xxxxxxxxx" />
                <x-ios-input label="{{ __('messages.password') }}" wire:model="password" type="password" id="password"
                    dir="ltr" placeholder="••••••••" />
            </div>

            <div class="flex items-center justify-between mt-2">
                <a href="{{ route('password.request') }}" wire:navigate
                    class="text-sm text-banhafade-accent hover:text-red-600 transition">{{ __('messages.forgot_password') }}</a>
            </div>

            <x-ios-button target="authenticate">{{ __('messages.login') }}</x-ios-button>
        </form>

        <div class="mt-8 text-center text-sm text-gray-600 dark:text-gray-400">
            {{ __('messages.dont_have_account') }}
            <a href="{{ route('register') }}" wire:navigate
                class="font-bold text-banhafade-accent hover:text-red-600 hover:underline">{{ __('messages.register_now') }}</a>
        </div>
    </div>
</div>
