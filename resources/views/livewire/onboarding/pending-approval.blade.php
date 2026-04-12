<div class="pt-16 pb-8 px-2 w-full">
    <div class="w-full text-center">
        {{-- Pending Icon --}}
        <div class="mb-8">
            <div class="w-24 h-24 rounded-full bg-gradient-to-br from-yellow-400 to-orange-500 flex items-center justify-center mx-auto mb-6 shadow-lg">
                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">{{ __('messages.onboarding_pending_title') }}</h1>
            <p class="text-gray-600 dark:text-gray-300">{!! __('messages.onboarding_pending_welcome', ['name' => '<strong>'.$shop->name.'</strong>']) !!}</p>
        </div>

        {{-- Content Card --}}
        <div class="space-y-6">
            <div class="bg-white/70 dark:bg-[#1c1c1e]/70 backdrop-blur-3xl border border-black/5 dark:border-white/10 rounded-2xl p-6 text-right">
                <p class="text-gray-700 dark:text-gray-200 text-base leading-relaxed mb-6">
                    {{ __('messages.onboarding_pending_description') }}
                </p>

                {{-- Status Info --}}
                <div class="space-y-3 pt-4 border-t border-black/5 dark:border-white/10">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500 dark:text-gray-400 text-sm">{{ __('messages.onboarding_shop_name') }}</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ $shop->name }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500 dark:text-gray-400 text-sm">{{ __('messages.onboarding_shop_phone') }}</span>
                        <span class="font-bold text-gray-900 dark:text-white" dir="ltr">{{ $shop->phone }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500 dark:text-gray-400 text-sm">{{ __('messages.onboarding_owner_phone') }}</span>
                        <span class="font-bold text-gray-900 dark:text-white" dir="ltr">{{ $shop->owner->phone }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500 dark:text-gray-400 text-sm">{{ __('messages.onboarding_area') }}</span>
                        <span class="font-bold text-gray-900 dark:text-white">{{ $shop->area->name }}</span>
                    </div>
                </div>
            </div>

            {{-- Next Steps --}}
            <div class="bg-white/70 dark:bg-[#1c1c1e]/70 backdrop-blur-3xl border border-black/5 dark:border-white/10 rounded-2xl p-5 text-right">
                <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">{{ __('messages.onboarding_next_steps') }}</p>
                <ul class="space-y-3">
                    <li class="flex items-start gap-3">
                        <div class="w-5 h-5 rounded-full bg-fadebook-accent/10 flex items-center justify-center text-fadebook-accent text-xs font-bold mt-0.5">١</div>
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('messages.onboarding_step_1') }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-5 h-5 rounded-full bg-fadebook-accent/10 flex items-center justify-center text-fadebook-accent text-xs font-bold mt-0.5">٢</div>
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('messages.onboarding_step_2') }}</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <div class="w-5 h-5 rounded-full bg-fadebook-accent/10 flex items-center justify-center text-fadebook-accent text-xs font-bold mt-0.5">٣</div>
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('messages.onboarding_step_3') }}</span>
                    </li>
                </ul>
            </div>

            <div class="pt-4">
                <x-ios-button href="{{ route('home') }}" wire:navigate>{{ __('messages.onboarding_go_home') }}</x-ios-button>
                <p class="text-gray-400 text-xs mt-6">
                    {{ __('messages.onboarding_stay_hint') }}
                </p>
            </div>
        </div>
    </div>
</div>
