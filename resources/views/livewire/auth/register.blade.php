<div class="min-h-screen flex items-center justify-center p-4" x-data="{ step: @entangle('step') }">
    <div class="glass w-full max-w-md p-8 rounded-3xl shadow-lg border border-white/20">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">{{ __('messages.register_welcome') }}</h1>
            <p class="text-gray-600 dark:text-gray-300">{{ __('messages.register_subtitle') }}</p>
        </div>

        <!-- Step Indicator -->
        <div class="flex justify-center mb-8 space-x-2 space-x-reverse">
            <div class="h-2 w-12 rounded-full transition-colors duration-300" :class="step >= 1 ? 'bg-blue-500' : 'bg-gray-300 dark:bg-gray-700'"></div>
            <div class="h-2 w-12 rounded-full transition-colors duration-300" :class="step >= 2 ? 'bg-blue-500' : 'bg-gray-300 dark:bg-gray-700'"></div>
        </div>

        <form wire:submit="register">
            <!-- Step 1: Account Info -->
            <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.full_name') }}</label>
                        <input wire:model="name" type="text" id="name" class="w-full bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="{{ __('messages.name') }}" required>
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.phone') }}</label>
                        <input wire:model="phone" type="tel" id="phone" dir="ltr" class="w-full bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-left" placeholder="01xxxxxxxxx" required>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.password') }}</label>
                        <input wire:model="password" type="password" id="password" dir="ltr" class="w-full bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-left" placeholder="••••••••" required>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('messages.confirm_password') }}</label>
                        <input wire:model="password_confirmation" type="password" id="password_confirmation" dir="ltr" class="w-full bg-white/50 dark:bg-gray-800/50 backdrop-blur-sm border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-left" placeholder="••••••••" required>
                    </div>
                </div>

                <button type="button" wire:click="nextStep" class="w-full bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-bold rounded-xl py-3.5 hover:opacity-90 transition active:scale-[0.98] mt-6 shadow-md">
                    {{ __('messages.next') }}
                </button>
            </div>

            <!-- Step 2: Role Selection -->
            <div x-show="step === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                <div class="space-y-4">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 text-center">{{ __('messages.register_role_prompt') }}</p>

                    <label class="relative flex items-center p-4 bg-white/50 dark:bg-gray-800/50 border rounded-xl cursor-pointer transition-all duration-200" :class="role === 'client' ? 'border-blue-500 shadow-sm ring-1 ring-blue-500' : 'border-gray-200 dark:border-gray-700 hover:bg-white/80'">
                        <input type="radio" wire:model.live="role" name="role" value="client" class="sr-only" x-model="role">
                        <div class="flex-1">
                            <span class="block text-sm font-bold text-gray-900 dark:text-white">{{ __('messages.role_client_title') }}</span>
                            <span class="block text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('messages.role_client_desc') }}</span>
                        </div>
                        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center ms-3" :class="role === 'client' ? 'border-blue-500' : 'border-gray-300'">
                            <div class="w-2.5 h-2.5 rounded-full bg-blue-500" x-show="role === 'client'"></div>
                        </div>
                    </label>

                    <label class="relative flex items-center p-4 bg-white/50 dark:bg-gray-800/50 border rounded-xl cursor-pointer transition-all duration-200" :class="role === 'barber_owner' ? 'border-blue-500 shadow-sm ring-1 ring-blue-500' : 'border-gray-200 dark:border-gray-700 hover:bg-white/80'">
                        <input type="radio" wire:model.live="role" name="role" value="barber_owner" class="sr-only" x-model="role">
                        <div class="flex-1">
                            <span class="block text-sm font-bold text-gray-900 dark:text-white">{{ __('messages.role_barber_title') }}</span>
                            <span class="block text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('messages.role_barber_desc') }}</span>
                        </div>
                        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center ms-3" :class="role === 'barber_owner' ? 'border-blue-500' : 'border-gray-300'">
                            <div class="w-2.5 h-2.5 rounded-full bg-blue-500" x-show="role === 'barber_owner'"></div>
                        </div>
                    </label>
                </div>

                <div class="flex gap-3 mt-6">
                    <button type="button" wire:click="goBack" class="w-1/3 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold rounded-xl py-3.5 hover:opacity-90 transition active:scale-[0.98] shadow-sm">
                        {{ __('messages.back') }}
                    </button>
                    <button type="submit" class="w-2/3 bg-blue-600 text-white font-bold rounded-xl py-3.5 hover:bg-blue-700 transition active:scale-[0.98] shadow-md">
                        {{ __('messages.create_account') }}
                    </button>
                </div>
            </div>
        </form>

        <div class="mt-8 text-center text-sm text-gray-600 dark:text-gray-400">
            {{ __('messages.already_have_account') }}
            <a href="{{ route('login') }}" wire:navigate class="font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 hover:underline">{{ __('messages.login') }}</a>
        </div>
    </div>
</div>
