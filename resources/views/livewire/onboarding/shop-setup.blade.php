<div class="pt-16 pb-8 px-2 w-full" x-data="{ step: @entangle('step') }">
    <div class="w-full">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">{{ __('messages.setup_title') }}</h1>
            <p class="text-gray-600 dark:text-gray-300">
                {{ __('messages.setup_step_indicator', ['step' => $step, 'total' => 2]) }}</p>
        </div>

        {{-- Step Indicator --}}
        <div class="flex justify-center mb-8 space-x-2 space-x-reverse">
            <div class="h-2 w-12 rounded-full transition-colors duration-300"
                :class="step >= 1 ? 'bg-banhafade-accent' : 'bg-gray-300 dark:bg-gray-700'"></div>
            <div class="h-2 w-12 rounded-full transition-colors duration-300"
                :class="step >= 2 ? 'bg-banhafade-accent' : 'bg-gray-300 dark:bg-gray-700'"></div>
        </div>

        <div class="w-full">
            @if ($step === 1)
                <div class="space-y-6">
                    <div class="space-y-4">
                        <x-ios-input label="{{ __('messages.register_shop_name_label') }}" wire:model="shopName"
                            type="text" placeholder="{{ __('messages.register_shop_name_placeholder') }}" />
                        <x-ios-input label="{{ __('messages.phone') }}" wire:model="phone" type="tel" dir="ltr"
                            placeholder="01xxxxxxxxx" />

                        <div
                            class="px-4 py-2 bg-white/70 dark:bg-[#1c1c1e]/70 backdrop-blur-3xl border border-black/5 dark:border-white/10 rounded-2xl">
                            <label
                                class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">{{ __('messages.register_area_label') }}</label>
                            <select wire:model="areaId"
                                class="w-full bg-transparent border-b border-white/10 pb-2 text-sm font-bold text-gray-900 dark:text-white focus:outline-none focus:border-banhafade-accent transition-colors appearance-none">
                                <option value="0" class="bg-white dark:bg-slate-800">
                                    {{ __('messages.register_area_placeholder') }}</option>
                                @foreach ($areas as $area)
                                    <option value="{{ $area->id }}" class="bg-white dark:bg-slate-800">
                                        {{ $area->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <x-ios-input label="{{ __('messages.register_address_label') }}" wire:model="address"
                            type="text" placeholder="{{ __('messages.register_address_placeholder') }}" />
                        <x-ios-textarea label="{{ __('messages.setup_description_label') }}" wire:model="description"
                            placeholder="{{ __('messages.setup_description_placeholder') }}" />
                    </div>

                    <div class="mt-8">
                        <x-ios-button wire:click="nextStep">{{ __('messages.setup_continue') }}</x-ios-button>
                    </div>
                </div>
            @elseif($step === 2)
                <div class="space-y-6">
                    <div class="space-y-4">
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 text-center">
                            {{ __('messages.setup_hours_intro') }}</p>

                        @foreach (['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday'] as $day)
                            <div
                                class="bg-white/70 dark:bg-[#1c1c1e]/70 backdrop-blur-3xl border border-black/5 dark:border-white/10 rounded-2xl p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-gray-900 dark:text-white font-bold">@lang('messages.day_' . $day)</span>
                                    <x-ios-toggle wire:model="openingHours.{{ $day }}.is_open" />
                                </div>

                                @if ($openingHours[$day]['is_open'])
                                    <div class="flex items-center gap-4">
                                        <div class="flex-1">
                                            <label
                                                class="block text-[10px] text-gray-400 uppercase mb-1">{{ __('messages.setup_open_time') }}</label>
                                            <input type="time" wire:model="openingHours.{{ $day }}.open"
                                                class="w-full bg-transparent border-b border-black/10 dark:border-white/10 text-gray-900 dark:text-white text-sm focus:outline-none">
                                        </div>
                                        <div class="flex-1">
                                            <label
                                                class="block text-[10px] text-gray-400 uppercase mb-1">{{ __('messages.setup_close_time') }}</label>
                                            <input type="time" wire:model="openingHours.{{ $day }}.close"
                                                class="w-full bg-transparent border-b border-black/10 dark:border-white/10 text-gray-900 dark:text-white text-sm focus:outline-none">
                                        </div>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-500 italic">{{ __('messages.setup_closed') }}</span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="flex gap-4 mt-8">
                        <div class="w-1/3">
                            <x-ios-button variant="secondary"
                                wire:click="previousStep">{{ __('messages.back') }}</x-ios-button>
                        </div>
                        <div class="w-2/3">
                            <x-ios-button wire:click="submit" wire:loading.attr="disabled">
                                <span wire:loading.remove>{{ __('messages.setup_finish') }}</span>
                                <span wire:loading>{{ __('messages.processing') }}</span>
                            </x-ios-button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="mt-8 text-center">
            <a href="{{ route('home') }}" wire:navigate
                class="text-sm font-bold text-gray-400 hover:text-banhafade-accent transition-colors">
                {{ __('messages.onboarding_go_home') }}
            </a>
        </div>
    </div>
</div>
