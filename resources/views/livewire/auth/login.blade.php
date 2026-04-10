<div class="min-h-screen flex items-center justify-center p-4">
    <div class="glass w-full max-w-md p-8 rounded-3xl shadow-lg border border-white/20">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">FadeBook</h1>
            <p class="text-gray-600 dark:text-gray-300">{{ __('messages.login_welcome') }}</p>
        </div>

        <form wire:submit="authenticate" class="space-y-4">
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.phone') }}</label>
                <input wire:model="phone" type="tel" id="phone" dir="ltr"
                    class="w-full bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-left"
                    placeholder="01xxxxxxxxx" required>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.password') }}</label>
                <input wire:model="password" type="password" id="password" dir="ltr"
                    class="w-full bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-left"
                    placeholder="••••••••" required>
            </div>

            <div class="flex items-center justify-between mt-2">
                <a href="{{ route('password.request') }}" wire:navigate
                    class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 transition">{{ __('messages.forgot_password') }}</a>
            </div>

            <button type="submit"
                class="w-full bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-bold rounded-xl py-3.5 hover:opacity-90 transition active:scale-[0.98] mt-6 shadow-md">
                {{ __('messages.login') }}
            </button>
        </form>

        <div class="mt-8 text-center text-sm text-gray-600 dark:text-gray-400">
            {{ __('messages.dont_have_account') }}
            <a href="{{ route('register') }}" wire:navigate
                class="font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 hover:underline">{{ __('messages.register_now') }}</a>
        </div>
    </div>
</div>
