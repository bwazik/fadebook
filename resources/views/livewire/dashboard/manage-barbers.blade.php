<div class="pb-[calc(5rem+var(--safe-area-bottom)+64px)] relative min-h-screen pt-4 px-4">
    <!-- Header -->
    <div class="mb-6 mt-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">
                {{ __('messages.barbers_team') }}
            </h1>
            <p class="text-sm text-gray-500 font-bold mt-1">
                {{ __('messages.manage_barbers_desc') }}
            </p>
        </div>
        <button wire:click="create"
            class="w-10 h-10 rounded-full bg-fadebook-accent text-white flex items-center justify-center shadow-md shadow-fadebook-accent/30 active:scale-95 transition-transform cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
        </button>
    </div>

    <!-- Barbers List -->
    <div class="space-y-4">
        @forelse($this->barbers as $barber)
            <div
                class="liquid-glass rounded-[1.5rem] p-4 border border-white/20 shadow-sm flex items-center gap-4 {{ !$barber->is_active ? 'opacity-60 grayscale' : '' }}">
                <div class="shrink-0">
                    @php $barberImage = $barber->images->first(); @endphp
                    @if ($barberImage)
                        <img src="{{ Storage::url($barberImage->path) }}" alt="{{ $barber->name }}"
                            class="w-14 h-14 rounded-full object-cover border border-black/5 dark:border-white/10 shadow-sm bg-white dark:bg-[#1c1c1e]">
                    @else
                        <div
                            class="w-14 h-14 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center border border-black/5 dark:border-white/10 shadow-sm">
                            <span class="text-xl text-gray-400 font-black">{{ mb_substr($barber->name, 0, 1) }}</span>
                        </div>
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase leading-tight truncate mb-1">
                        {{ $barber->name }}
                    </h3>
                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest truncate">
                        {{ $barber->services->count() > 0 ? implode(' • ', $barber->services->pluck('name')->toArray()) : __('messages.top_artist') }}
                    </p>
                </div>
                <div class="shrink-0 flex items-center gap-2">
                    <button wire:click="edit({{ $barber->id }})"
                        class="p-2 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 active:scale-95 transition-transform cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                        </svg>
                    </button>
                    <button
                        @click="$dispatch('open-ios-alert', {
                            title: '{{ __('messages.delete_barber_title') }}',
                            message: '{{ __('messages.delete_barber_confirm', ['name' => $barber->name]) }}',
                            action: 'deleteBarber',
                            params: {{ $barber->id }},
                            componentId: '{{ $this->getId() }}'
                        })"
                        class="p-2 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 active:scale-95 transition-transform cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                    </button>
                    <div class="h-6 w-[1px] bg-gray-200 dark:bg-gray-800 mx-1"></div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" class="sr-only peer" {{ $barber->is_active ? 'checked' : '' }}
                            wire:click="toggleActive({{ $barber->id }})">
                        <div
                            class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-fadebook-accent">
                        </div>
                    </label>
                </div>
            </div>
        @empty
            <x-empty-state title="{{ __('messages.no_barbers') }}" description="{{ __('messages.no_barbers_desc') }}">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-8 h-8 opacity-60">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                    </svg>
                </x-slot>
            </x-empty-state>
        @endforelse
    </div>

    <!-- Form Bottom Sheet -->
    <x-bottom-sheet wire:model="showForm" :title="$editingId ? __('messages.edit_barber') : __('messages.add_new_barber')">
        <form wire:submit="save" class="space-y-6 pb-6">
            <!-- Avatar Upload -->
            <div class="flex justify-center mb-6">
                <x-photo-upload wire:key="avatar-u-{{ $editingId ?? 'new' }}" wireModel="avatar" :photo="$avatar"
                    :current-photo="$editingId
                        ? $this->barbers->find($editingId)?->getImage('avatar')->first()?->path ?? null
                        : null" label="{{ __('messages.barber_photo') }}"
                    placeholder="images/barber-default.jpg" />
            </div>
            <div class="space-y-4">
                <div>
                    <x-ios-input label="{{ __('messages.barber_name_label') }}" wire:model="name" type="text"
                        placeholder="{{ __('messages.barber_name_placeholder') }}" />
                </div>

                <div>
                    <x-ios-input label="{{ __('messages.phone_number_label') }}" wire:model="phone" type="tel"
                        dir="ltr" placeholder="{{ __('messages.phone_number_placeholder') }}" />
                    <p class="text-[10px] text-gray-400 font-bold mt-1 px-1">
                        {{ __('messages.phone_number_note') }}
                        @if (!$editingId)
                            <br> {{ __('messages.default_password_note') }} <span
                                class="text-fadebook-accent">{{ $password }}</span>
                        @endif
                    </p>
                </div>

                <!-- Recurring Days Off -->
                <div>
                    <x-ios-select label="{{ __('messages.weekly_off_label') }}" wire:model="daysOff" :options="[
                        'sunday' => __('messages.sunday_off'),
                        'monday' => __('messages.monday_off'),
                        'tuesday' => __('messages.tuesday_off'),
                        'wednesday' => __('messages.wednesday_off'),
                        'thursday' => __('messages.thursday_off'),
                        'friday' => __('messages.friday_off'),
                        'saturday' => __('messages.saturday_off'),
                    ]"
                        multiple placeholder="{{ __('messages.days_off_placeholder') }}" />
                </div>

                <!-- Unavailability -->
                <div class="space-y-3">
                    <label class="block text-xs font-medium text-gray-600 dark:text-white/60 mb-1.5">
                        {{ __('messages.special_holidays_label') }}
                    </label>
                    <div class="flex items-end gap-2">
                        <div class="flex-1">
                            <x-ios-input type="date" wire:model="newUnavailabilityDate" />
                        </div>
                        <div class="shrink-0 pb-0.5">
                            <x-ios-button type="button" wire:click="addUnavailabilityDate"
                                class="!rounded-2xl !p-3.5 !w-auto">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                            </x-ios-button>
                        </div>
                    </div>

                    @if (count($unavailabilityDates) > 0)
                        <div class="flex flex-wrap gap-2 p-1">
                            @foreach ($unavailabilityDates as $date)
                                <div wire:key="undate-{{ $date }}"
                                    class="flex items-center gap-1.5 px-3 py-2 bg-[--color-fadebook-accent]/10 border border-[--color-fadebook-accent]/20 rounded-xl">
                                    <span
                                        class="text-[10px] font-bold text-[--color-fadebook-accent]">{{ \Carbon\Carbon::parse($date)->format('Y/m/d') }}</span>
                                    <button type="button"
                                        wire:click="removeUnavailabilityDate('{{ $date }}')"
                                        class="text-[--color-fadebook-accent]/60 hover:text-[--color-fadebook-accent] transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-[10px] text-gray-400 font-medium px-1">{{ __('messages.no_special_holidays') }}
                        </p>
                    @endif
                </div>

                <!-- Services Selection -->
                <div>
                    <x-ios-select label="{{ __('messages.services_provided_label') }}" wire:model="selectedServices"
                        :options="$this->availableServices->pluck('name', 'id')->toArray()" multiple placeholder="{{ __('messages.select_services_placeholder') }}" />
                </div>
            </div>

            <div class="pt-4">
                <x-ios-button type="submit" wire:loading.attr="disabled" target="save">
                    <span wire:loading.remove
                        wire:target="save">{{ $editingId ? __('messages.update_barber_btn') : __('messages.add_barber_btn') }}</span>
                    <span wire:loading wire:target="save">{{ __('messages.saving_btn') }}</span>
                </x-ios-button>
            </div>
        </form>
    </x-bottom-sheet>
</div>
