<div class="pb-[calc(5rem+var(--safe-area-bottom)+64px)] relative min-h-screen pt-4 px-4">
    <div class="mb-6 mt-4 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">
                {{ __('messages.categories_title') }}
            </h1>
            <p class="text-[10px] text-gray-500 font-bold mt-0.5 uppercase">
                {{ __('messages.manage_categories_desc') }}
            </p>
        </div>
        <button wire:click="create"
            class="w-10 h-10 rounded-full bg-banhafade-accent text-white flex items-center justify-center shadow-md shadow-banhafade-accent/30 active:scale-95 transition-transform cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
        </button>
    </div>

    <!-- Alert for info -->
    <div class="mb-6 px-2">
        <div
            class="bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-800/20 rounded-[1.2rem] p-4 flex gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                stroke="currentColor" class="w-5 h-5 text-blue-600 dark:text-blue-400 shrink-0 mt-0.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
            </svg>
            <p class="text-[11px] text-blue-700 dark:text-blue-300 font-bold leading-relaxed">
                {{ __('messages.categories_info_alert') }}
            </p>
        </div>
    </div>

    <!-- Categories List -->
    <div class="space-y-3" wire:sort="updateOrder">
        @forelse($this->categories as $category)
            <div wire:sort:item="{{ $category->id }}" wire:key="category-{{ $category->id }}"
                class="liquid-glass rounded-[1.5rem] p-4 flex items-center justify-between border-white/30 dark:border-white/10 shadow-sm">
                <div class="flex items-center gap-4">
                    <div wire:sort:handle
                        class="w-10 h-10 rounded-2xl bg-black/5 dark:bg-white/5 flex items-center justify-center text-gray-400 cursor-grab active:cursor-grabbing shrink-0 transition-colors hover:text-gray-600 dark:hover:text-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 12h16.5m-16.5-6.75h16.5m-16.5 13.5h16.5" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase leading-none">
                            {{ $category->name }}
                        </h3>
                        <p class="text-[10px] text-gray-500 font-bold mt-1.5 lowercase leading-none">
                            {{ __('messages.services_count_label', ['count' => $category->services_count ?? $category->services()->count()]) }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-1.5">
                    <button wire:click="edit({{ $category->id }})"
                        class="p-2 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 active:scale-95 transition-transform cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                        </svg>
                    </button>
                    <button
                        @click="$dispatch('open-ios-alert', {
                            title: '{{ __('messages.delete_category_title') }}',
                            message: '{{ __('messages.delete_category_confirm', ['name' => $category->name]) }}',
                            action: 'deleteCategory',
                            params: {{ $category->id }},
                            componentId: '{{ $this->getId() }}'
                        })"
                        class="p-2 rounded-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 active:scale-95 transition-transform cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-4 h-4">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                    </button>
                </div>
            </div>
        @empty
            <x-empty-state title="{{ __('messages.no_categories') }}"
                description="{{ __('messages.no_categories_desc') }}">
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="w-8 h-8 opacity-60">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                    </svg>
                </x-slot>
            </x-empty-state>
        @endforelse
    </div>

    <!-- Form Bottom Sheet -->
    <x-bottom-sheet wire:model="showForm" :title="$editingId ? __('messages.edit_category') : __('messages.add_new_category')">
        <form wire:submit="save" class="space-y-6 pb-6 pt-2">
            <x-ios-input label="{{ __('messages.category_name_label') }}" wire:model="name" type="text"
                placeholder="{{ __('messages.category_name_placeholder') }}" />

            <div class="pt-4">
                <x-ios-button type="submit" wire:loading.attr="disabled" target="save">
                    <span wire:loading.remove wire:target="save">{{ __('messages.save_category_btn') }}</span>
                    <span wire:loading wire:target="save">{{ __('messages.saving_btn') }}</span>
                </x-ios-button>
            </div>
        </form>
    </x-bottom-sheet>
</div>

