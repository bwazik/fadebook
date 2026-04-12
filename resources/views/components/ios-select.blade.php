{{-- iOS Select Component — Fixed version --}}
@props(['options' => [], 'placeholder' => 'اختار...', 'disabled' => false])

<div x-data="{
    id: $id('ios-select'),
    open: false,
    value: @entangle($attributes->wire('model')),
    getLabel(rootEl) {
        try {
            const opts = JSON.parse(rootEl.dataset.options || '{}');
            return opts[this.value] ?? rootEl.dataset.placeholder;
        } catch (e) {
            return rootEl.dataset.placeholder;
        }
    },
    toggle() { this.open = !this.open; }
}" data-options="{{ empty($options) ? '{}' : json_encode($options, JSON_UNESCAPED_UNICODE) }}"
    data-placeholder="{{ $placeholder }}" x-id="['ios-select']" @click.away="open = false"
    @ios-select-toggled.window="if ($event.detail !== id) open = false"
    x-effect="if (open) $dispatch('ios-select-toggled', id)" {{ $attributes->merge(['class' => 'relative w-full']) }}>
    <button type="button" @click="toggle()" {{ $disabled ? 'disabled' : '' }}
        class="w-full flex items-center justify-between rounded-2xl bg-black/5 dark:bg-white/10
               text-gray-900 dark:text-white text-sm px-4 py-3.5
               focus:outline-none focus:ring-2 focus:ring-[--color-fadebook-accent]/50
               transition-all disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
        :class="open ? 'ring-2 ring-[--color-fadebook-accent]/50 bg-white dark:bg-[#3a3a3c]' : ''">
        <div class="flex items-center gap-2 truncate">
            @if (isset($icon))
                <span class="shrink-0">{{ $icon }}</span>
            @endif
            <span class="truncate" x-text="getLabel($root)"
                :class="!value ? 'text-gray-400 dark:text-white/40' : 'font-medium'">
            </span>
        </div>
        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 shrink-0" :class="open ? 'rotate-180' : ''"
            fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
        class="absolute z-50 w-full mt-2 rounded-3xl
               liquid-panel overflow-hidden"
        style="display: none;">
        <ul class="max-h-60 overflow-y-auto overscroll-contain py-1">
            @forelse($options as $val => $label)
                <li wire:key="select-opt-{{ $val }}">
                    <button type="button" @click="value = '{{ $val }}'; open = false;"
                        class="w-full text-right px-4 py-3 text-sm flex items-center justify-between
                               hover:bg-black/5 dark:hover:bg-white/10 transition-colors
                               border-b border-black/5 dark:border-white/5 last:border-0 cursor-pointer"
                        :class="value == '{{ $val }}' ?
                            'text-[--color-fadebook-accent] font-bold bg-[--color-fadebook-accent]/5' :
                            'text-gray-700 dark:text-white/80'">
                        <span class="truncate">{{ $label }}</span>
                        <svg x-show="value == '{{ $val }}'"
                            class="w-4 h-4 text-[--color-fadebook-accent] shrink-0" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M5 13l4 4L19 7" />
                        </svg>
                    </button>
                </li>
            @empty
                <li wire:key="select-opt-empty">
                    <div class="px-4 py-3 text-sm text-gray-500 dark:text-white/40 text-center">
                        مفيش اختيارات...
                    </div>
                </li>
            @endforelse
        </ul>
    </div>
</div>
