<div class="pt-16 pb-8 px-2 w-full" x-data="{ step: @entangle('step'), role: @entangle('role') }">
    <div class="w-full">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">{{ __('messages.register_welcome') }}</h1>
            <p class="text-gray-600 dark:text-gray-300">{{ __('messages.register_subtitle') }}</p>
        </div>

        <!-- Step Indicator -->
        <div class="flex justify-center mb-8 space-x-2 space-x-reverse">
            <div class="h-2 w-12 rounded-full transition-colors duration-300"
                :class="step >= 1 ? 'bg-banhafade-accent' : 'bg-gray-300 dark:bg-gray-700'"></div>
            <div class="h-2 w-12 rounded-full transition-colors duration-300"
                :class="step >= 2 ? 'bg-banhafade-accent' : 'bg-gray-300 dark:bg-gray-700'"></div>
        </div>

        <form wire:submit.prevent="register">
            <!-- Step 1: Account Info -->
            <div x-show="step === 1" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                <div class="mb-6 space-y-4">
                    <x-ios-input label="{{ __('messages.full_name') }}" wire:model="name" type="text" id="name"
                        placeholder="{{ __('messages.name') }}" />
                    <x-ios-input label="{{ __('messages.phone') }}" wire:model="phone" type="tel" id="phone"
                        dir="ltr" placeholder="01xxxxxxxxx" />
                    <x-ios-input label="{{ __('messages.password') }}" wire:model="password" type="password"
                        id="password" dir="ltr" placeholder="••••••••" />
                    <x-ios-input label="{{ __('messages.confirm_password') }}" wire:model="password_confirmation"
                        type="password" id="password_confirmation" dir="ltr" placeholder="••••••••" />
                </div>

                <x-ios-button wire:click="nextStep" type="button">{{ __('messages.next') }}</x-ios-button>
            </div>

            <!-- Step 2: Role Selection & Shop Info -->
            <div x-show="step === 2" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-x-4" x-transition:enter-end="opacity-100 translate-x-0"
                style="display: none;">
                <div class="space-y-4 mb-6">
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 text-center">
                        {{ __('messages.register_role_prompt') }}</p>

                    <div class="grid grid-cols-2 gap-3 mb-6">
                        <label
                            class="relative flex flex-col items-center p-4 bg-white/70 dark:bg-[#1c1c1e]/70 backdrop-blur-3xl border rounded-2xl cursor-pointer transition-all duration-200 text-center"
                            :class="role === 'client' ? 'border-banhafade-accent shadow-sm ring-1 ring-banhafade-accent' :
                                'border-black/5 dark:border-white/10 hover:bg-white/80 dark:hover:bg-[#1c1c1e]/80'">
                            <input type="radio" wire:model.live="role" name="role" value="client" class="sr-only"
                                x-model="role">
                            <span
                                class="block text-sm font-bold text-gray-900 dark:text-white">{{ __('messages.role_client_title') }}</span>
                            <span
                                class="block text-[10px] text-gray-500 dark:text-gray-400 mt-1">{{ __('messages.role_client_desc') }}</span>
                        </label>

                        <label
                            class="relative flex flex-col items-center p-4 bg-white/70 dark:bg-[#1c1c1e]/70 backdrop-blur-3xl border rounded-2xl cursor-pointer transition-all duration-200 text-center"
                            :class="role === 'barber_owner' ? 'border-banhafade-accent shadow-sm ring-1 ring-banhafade-accent' :
                                'border-black/5 dark:border-white/10 hover:bg-white/80 dark:hover:bg-[#1c1c1e]/80'">
                            <input type="radio" wire:model.live="role" name="role" value="barber_owner"
                                class="sr-only" x-model="role">
                            <span
                                class="block text-sm font-bold text-gray-900 dark:text-white">{{ __('messages.role_barber_title') }}</span>
                            <span
                                class="block text-[10px] text-gray-500 dark:text-gray-400 mt-1">{{ __('messages.role_barber_desc') }}</span>
                        </label>
                    </div>

                    <!-- Shop Info Fields (Shown only for Barber) -->
                    <div x-show="role === 'barber_owner'" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        class="space-y-4 mt-6 pt-6 border-t border-black/5 dark:border-white/10">
                        <p class="text-xs font-black text-banhafade-accent uppercase tracking-widest text-center mb-4">
                            {{ __('messages.register_shop_header') }}</p>

                        <x-ios-input label="{{ __('messages.register_shop_name_label') }}" wire:model="shopName"
                            type="text" placeholder="{{ __('messages.register_shop_name_placeholder') }}" />

                        <x-ios-input label="{{ __('messages.register_shop_phone_label') }}" wire:model="shopPhone"
                            type="tel" dir="ltr" placeholder="01xxxxxxxxx" />

                        <x-ios-select label="{{ __('messages.register_area_label') }}" wire:model="areaId"
                            :options="$areasOptions" placeholder="{{ __('messages.register_area_placeholder') }}"
                            class="w-full" />

                        <x-ios-input label="{{ __('messages.register_address_label') }}" wire:model="address"
                            type="text" placeholder="{{ __('messages.register_address_placeholder') }}" />
                    </div>
                </div>

                <div class="flex gap-3">
                    <div class="w-1/3">
                        <x-ios-button type="button" wire:click="previousStep"
                            variant="secondary">{{ __('messages.back') }}</x-ios-button>
                    </div>
                    <div class="w-2/3">
                        <x-ios-button type="submit"
                            target="register">{{ __('messages.create_account') }}</x-ios-button>
                    </div>
                </div>
            </div>
        </form>

        <div class="mt-8 text-center text-sm text-gray-600 dark:text-gray-400">
            {{ __('messages.already_have_account') }}
            <a href="{{ route('login') }}" wire:navigate
                class="font-bold text-banhafade-accent hover:text-red-600 hover:underline">{{ __('messages.login') }}</a>
        </div>
    </div>
</div>
