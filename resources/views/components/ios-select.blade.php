@props(['options' => [], 'placeholder' => 'اختار...', 'disabled' => false, 'label' => '', 'multiple' => false])

@php
    // Prepare options for JSON pass-through to Alpine
    $optionsJson = empty($options) ? '{}' : json_encode($options, JSON_UNESCAPED_UNICODE);
@endphp

<div x-data="{
    id: $id('ios-select'),
    open: false,
    openUp: false,
    multiple: {{ $multiple ? 'true' : 'false' }},
    value: @entangle($attributes->wire('model')),
    getLabel(rootEl) {
        try {
            const opts = JSON.parse(rootEl.dataset.options || '{}');
            if (this.multiple) {
                if (!Array.isArray(this.value) || this.value.length === 0) return rootEl.dataset.placeholder;
                return this.value.map(v => opts[v] || v).join(' • ');
            }
            return opts[this.value] ?? rootEl.dataset.placeholder;
        } catch (e) {
            return rootEl.dataset.placeholder;
        }
    },
    toggle() {
        if (this.disabled) return;
        this.open = !this.open;
        if (this.open) {
            this.$nextTick(() => {
                const rect = this.$root.getBoundingClientRect();
                const vh = window.innerHeight;
                // If space below is less than 300px and there's more space above
                this.openUp = (vh - rect.bottom < 300) && (rect.top > (vh - rect.bottom));
            });
        }
    },
    select(val) {
        if (this.multiple) {
            if (!Array.isArray(this.value)) this.value = [];

            const index = this.value.indexOf(val);
            if (index > -1) {
                this.value.splice(index, 1);
            } else {
                this.value.push(val);
            }
        } else {
            this.value = val;
            this.open = false;
        }
    },
    isActive(val) {
        if (this.multiple) {
            return Array.isArray(this.value) && this.value.indexOf(val) > -1;
        }
        return this.value == val;
    },
    count() {
        return Array.isArray(this.value) ? this.value.length : 0;
    }
}" data-options="{{ $optionsJson }}" data-placeholder="{{ $placeholder }}" x-id="['ios-select']"
    @click.away="open = false" @ios-select-toggled.window="if ($event.detail !== id) open = false"
    x-effect="if (open) $dispatch('ios-select-toggled', id)" {{ $attributes->merge(['class' => 'relative w-full']) }}>

    @if ($label)
        <label class="block text-xs font-medium text-gray-600 dark:text-white/60 mb-1.5">{{ $label }}</label>
    @endif

    <button type="button" @click="toggle()" {{ $disabled ? 'disabled' : '' }}
        class="w-full flex items-center justify-between rounded-2xl bg-black/5 dark:bg-white/10
               text-gray-900 dark:text-white text-sm px-4 py-3.5
               focus:outline-none focus:ring-2 focus:ring-banhafade-accent/50
               transition-all disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
        :class="open ? 'ring-2 ring-banhafade-accent/50 bg-white dark:bg-[#3a3a3c]' : ''">
        <div class="flex items-center gap-2 truncate">
            @if (isset($icon))
                <span class="shrink-0">{{ $icon }}</span>
            @endif
            <span class="truncate" x-text="getLabel($root)"
                :class="count() === 0 && !isActive(value) ? 'text-gray-400 dark:text-white/40' : 'font-bold'">
            </span>
        </div>
        <div class="flex items-center gap-2">
            <template x-if="multiple && count() > 0">
                <span
                    class="bg-banhafade-accent text-white text-[10px] font-black w-5 h-5 rounded-full flex items-center justify-center"
                    x-text="count()">
                </span>
            </template>
            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0"
                :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </button>

    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute z-[100] w-full rounded-3xl
               liquid-panel overflow-hidden border border-white/20 shadow-2xl"
        :class="openUp ? 'bottom-full mb-2 origin-bottom' : 'top-full mt-2 origin-top'" style="display: none;">
        <ul class="max-h-60 overflow-y-auto overscroll-contain py-1 no-scrollbar">
            @forelse($options as $val => $label)
                <li wire:key="select-opt-{{ $val }}">
                    <button type="button" @click="select('{{ $val }}')"
                        class="w-full text-right px-4 py-4 text-xs flex items-center justify-between
                               hover:bg-black/5 dark:hover:bg-white/10 transition-colors
                               border-b border-black/5 dark:border-white/5 last:border-0 cursor-pointer"
                        :class="isActive('{{ $val }}') ?
                            'text-banhafade-accent font-bold bg-banhafade-accent/5' :
                            'text-gray-700 dark:text-white/80'">
                        <span class="truncate text-bold">{{ $label }}</span>
                        <div class="flex items-center gap-2">
                            <template x-if="multiple">
                                <div class="w-5 h-5 rounded-md border-2 transition-all flex items-center justify-center"
                                    :class="isActive('{{ $val }}') ? 'bg-banhafade-accent border-banhafade-accent' :
                                        'border-gray-300 dark:border-gray-600'">
                                    <svg x-show="isActive('{{ $val }}')" class="w-3.5 h-3.5 text-white"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                            d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                            </template>
                            <template x-if="!multiple">
                                <svg x-show="isActive('{{ $val }}')"
                                    class="w-4 h-4 text-banhafade-accent shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </template>
                        </div>
                    </button>
                </li>
            @empty
                <li wire:key="select-opt-empty">
                    <div class="px-4 py-4 text-xs text-gray-500 dark:text-white/40 text-center font-bold">
                        مفيش اختيارات...
                    </div>
                </li>
            @endforelse
        </ul>
    </div>
</div>
