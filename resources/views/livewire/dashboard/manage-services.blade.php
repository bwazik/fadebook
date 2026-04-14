<div class="pb-[calc(5rem+var(--safe-area-bottom)+64px)] relative min-h-screen pt-4 px-4">
    <!-- Header -->
    <div class="mb-6 mt-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">
                {{ __('messages.services_title') }}
            </h1>
            <p class="text-sm text-gray-500 font-bold mt-1">
                {{ __('messages.manage_services_desc') }}
            </p>
        </div>
        <button wire:click="create" class="w-10 h-10 rounded-full bg-fadebook-accent text-white flex items-center justify-center shadow-md shadow-fadebook-accent/30 active:scale-95 transition-transform cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
        </button>
    </div>

    <!-- Services List -->
    <div class="space-y-8">
        @forelse($this->groupedServices as $categoryName => $services)
            <div>
                <div class="flex items-center gap-3 px-2 mb-4">
                    <h3 class="text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">{{ $categoryName }}</h3>
                    <div class="flex-1 h-px bg-black/5 dark:bg-white/5"></div>
                </div>

                <div class="space-y-3"
                    x-data="{
                        sortable: null,
                        init() {
                            this.sortable = new Sortable($el, {
                                animation: 150,
                                handle: '.drag-handle',
                                delay: 150,
                                delayOnTouchOnly: true,
                                forceFallback: true,
                                fallbackClass: 'opacity-80',
                                fallbackTolerance: 5,
                                scroll: true,
                                bubbleScroll: true,
                                ghostClass: 'opacity-50',
                                onEnd: (evt) => {
                                    let items = Array.from($el.querySelectorAll('[data-id]')).map((el, index) => {
                                        return { value: el.getAttribute('data-id'), order: index + 1 };
                                    });
                                    $wire.updateOrder(items);
                                }
                            });
                        }
                    }"
                    x-init="init"
                >
                    @foreach($services as $service)
                        <div data-id="{{ $service->id }}" class="liquid-glass rounded-[1.5rem] p-4 flex items-center justify-between border-white/30 dark:border-white/10 shadow-sm {{ !$service->is_active ? 'opacity-50 grayscale' : '' }}">
                            <div class="flex items-center gap-4 flex-1">
                                <div class="drag-handle w-10 h-10 rounded-2xl bg-black/5 dark:bg-white/5 flex items-center justify-center text-gray-400 cursor-grab active:cursor-grabbing shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5-6.75h16.5m-16.5 13.5h16.5" />
                                    </svg>
                                </div>
                                <div class="flex-1 pe-4">
                                    <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase leading-none">
                                        {{ $service->name }}
                                    </h3>
                                    @if($service->description)
                                        <p class="text-[9px] text-gray-400 font-bold mt-1.5 italic line-clamp-1 opacity-80">{{ $service->description }}</p>
                                    @endif
                                    <div class="flex items-center gap-2 mt-2.5">
                                        <div class="flex items-center gap-1 text-[9px] text-gray-500 dark:text-gray-400 font-black uppercase tracking-wider bg-black/5 dark:bg-white/5 px-2 py-0.5 rounded-lg border border-black/5">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor" class="w-2.5 h-2.5 text-fadebook-accent">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                            {{ $service->duration_minutes }} {{ __('messages.minutes_unit') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="shrink-0 flex items-center gap-3">
                                <div class="text-left me-2">
                                    <p class="text-lg font-black text-fadebook-accent tracking-tighter leading-none">
                                        {{ number_format($service->price, 0) }} 
                                        <span class="text-[9px] ms-0.5">{{ __('messages.egp') }}</span>
                                    </p>
                                </div>
                                <div class="h-8 w-[1px] bg-black/5 dark:bg-white/5"></div>
                                <div class="flex items-center gap-1.5">
                                    <button wire:click="edit({{ $service->id }})" class="p-2 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 active:scale-95 transition-transform cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                                        </svg>
                                    </button>
                                    <button
                                        @click="$dispatch('open-ios-alert', {
                                            title: '{{ __('messages.delete_service_title') }}',
                                            message: '{{ __('messages.delete_service_confirm', ['name' => $service->name]) }}',
                                            action: 'deleteService',
                                            params: {{ $service->id }},
                                            componentId: '{{ $this->getId() }}'
                                        })"
                                        class="p-2 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 active:scale-95 transition-transform cursor-pointer">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                    </button>
                                    <label class="relative inline-flex items-center cursor-pointer ms-1">
                                        <input type="checkbox" class="sr-only peer" {{ $service->is_active ? 'checked' : '' }} wire:click="toggleActive({{ $service->id }})">
                                        <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-fadebook-accent"></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <x-empty-state 
                title="{{ __('messages.no_services') }}"
                description="{{ __('messages.no_services_desc') }}"
            >
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 opacity-60">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                    </svg>
                </x-slot>
            </x-empty-state>
        @endforelse
    </div>

    <!-- Form Bottom Sheet -->
    <x-bottom-sheet wire:model="showForm" :title="$editingId ? __('messages.edit_service') : __('messages.add_new_service')">
        <form wire:submit="save" class="space-y-6 pb-6 pt-2">
            <x-ios-input label="{{ __('messages.service_name_label') }}" wire:model="name" type="text" placeholder="{{ __('messages.service_name_placeholder') }}" />
            
            <x-ios-textarea label="{{ __('messages.service_desc_label') }}" wire:model="description" placeholder="{{ __('messages.service_desc_placeholder') }}" />

            <div class="grid grid-cols-2 gap-4">
                <x-ios-input label="{{ __('messages.price_label') }}" wire:model="price" type="number" dir="ltr" placeholder="0" />
                <x-ios-input label="{{ __('messages.duration_label') }}" wire:model="duration_minutes" type="number" dir="ltr" placeholder="30" />
            </div>

            <x-ios-select 
                label="{{ __('messages.category_label') }}" 
                wire:model="service_category_id"
                :options="$this->categories->pluck('name', 'id')->toArray()"
                placeholder="{{ __('messages.other_category') }}"
            />
            
            <div class="pt-4">
                <x-ios-button type="submit" wire:loading.attr="disabled" target="save">
                    <span wire:loading.remove wire:target="save">{{ __('messages.save_data_btn') }}</span>
                    <span wire:loading wire:target="save">{{ __('messages.saving_btn') }}</span>
                </x-ios-button>
            </div>
        </form>
    </x-bottom-sheet>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
@endpush
