<div class="pt-16 pb-8 px-2 w-full" x-data="{ step: @entangle('step'), role: @entangle('role') }">
    <div class="w-full">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">{{ __('messages.register_welcome') }}</h1>
            <p class="text-gray-600 dark:text-gray-300">{{ __('messages.register_subtitle') }}</p>
        </div>

        <!-- Step Indicator -->
        <div class="flex justify-center mb-8 space-x-2 space-x-reverse">
            <div class="h-2 w-12 rounded-full transition-colors duration-300" :class="step >= 1 ? 'bg-fadebook-accent' : 'bg-gray-300 dark:bg-gray-700'"></div>
            <div class="h-2 w-12 rounded-full transition-colors duration-300" :class="step >= 2 ? 'bg-fadebook-accent' : 'bg-gray-300 dark:bg-gray-700'"></div>
        </div>

        <form wire:submit="register">
            <!-- Step 1: Account Info -->
            <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                <div class="mb-6 space-y-4">
                    <x-ios-input label="{{ __('messages.full_name') }}" wire:model="name" type="text" id="name" placeholder="{{ __('messages.name') }}" />
                    <x-ios-input label="{{ __('messages.phone') }}" wire:model="phone" type="tel" id="phone" dir="ltr" placeholder="01xxxxxxxxx" />
                    <x-ios-input label="{{ __('messages.password') }}" wire:model="password" type="password" id="password" dir="ltr" placeholder="••••••••" />
                    <x-ios-input label="{{ __('messages.confirm_password') }}" wire:model="password_confirmation" type="password" id="password_confirmation" dir="ltr" placeholder="••••••••" />
                </div>

                <x-ios-button wire:click="nextStep" type="button">{{ __('messages.next') }}</x-ios-button>
            </div>

            <!-- Step 2: Role Selection -->
            <div x-show="step === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                <div class="space-y-4 mb-6">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 text-center">{{ __('messages.register_role_prompt') }}</p>

                    <label class="relative flex items-center p-4 bg-white/70 dark:bg-[#1c1c1e]/70 backdrop-blur-3xl border rounded-2xl cursor-pointer transition-all duration-200" :class="role === 'client' ? 'border-fadebook-accent shadow-sm ring-1 ring-fadebook-accent' : 'border-black/5 dark:border-white/10 hover:bg-white/80 dark:hover:bg-[#1c1c1e]/80'">
                        <input type="radio" wire:model.live="role" name="role" value="client" class="sr-only" x-model="role">
                        <div class="flex-1">
                            <span class="block text-sm font-bold text-gray-900 dark:text-white">{{ __('messages.role_client_title') }}</span>
                            <span class="block text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('messages.role_client_desc') }}</span>
                        </div>
                        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center ms-3" :class="role === 'client' ? 'border-fadebook-accent' : 'border-gray-300'">
                            <div class="w-2.5 h-2.5 rounded-full bg-fadebook-accent" x-show="role === 'client'"></div>
                        </div>
                    </label>

                    <label class="relative flex items-center p-4 bg-white/70 dark:bg-[#1c1c1e]/70 backdrop-blur-3xl border rounded-2xl cursor-pointer transition-all duration-200" :class="role === 'barber_owner' ? 'border-fadebook-accent shadow-sm ring-1 ring-fadebook-accent' : 'border-black/5 dark:border-white/10 hover:bg-white/80 dark:hover:bg-[#1c1c1e]/80'">
                        <input type="radio" wire:model.live="role" name="role" value="barber_owner" class="sr-only" x-model="role">
                        <div class="flex-1">
                            <span class="block text-sm font-bold text-gray-900 dark:text-white">{{ __('messages.role_barber_title') }}</span>
                            <span class="block text-xs text-gray-500 dark:text-gray-400 mt-1">{{ __('messages.role_barber_desc') }}</span>
                        </div>
                        <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center ms-3" :class="role === 'barber_owner' ? 'border-fadebook-accent' : 'border-gray-300'">
                            <div class="w-2.5 h-2.5 rounded-full bg-fadebook-accent" x-show="role === 'barber_owner'"></div>
                        </div>
                    </label>
                </div>

                <div class="flex gap-3">
                    <div class="w-1/3">
                        <x-ios-button type="button" wire:click="goBack" variant="secondary">{{ __('messages.back') }}</x-ios-button>
                    </div>
                    <div class="w-2/3">
                        <x-ios-button target="register">{{ __('messages.create_account') }}</x-ios-button>
                    </div>
                </div>
            </div>
        </form>

        <div class="mt-8 text-center text-sm text-gray-600 dark:text-gray-400">
            {{ __('messages.already_have_account') }}
            <a href="{{ route('login') }}" wire:navigate class="font-bold text-fadebook-accent hover:text-red-600 hover:underline">{{ __('messages.login') }}</a>
        </div>
    </div>
</div>
