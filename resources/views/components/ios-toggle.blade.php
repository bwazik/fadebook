{{-- iOS Toggle Component --}}
@props(['label' => '', 'description' => ''])

<div class="flex items-center justify-between py-1 px-4">
    <div>
        <label class="text-sm font-medium text-gray-900 dark:text-white">{{ $label }}</label>
        @if ($description)
            <p class="text-xs text-gray-500 dark:text-white/40">{{ $description }}</p>
        @endif
    </div>
    <label class="relative inline-flex items-center cursor-pointer" dir="ltr">
        <input type="checkbox" {{ $attributes->merge(['class' => 'sr-only peer']) }}>
        <div
            class="w-11 h-6 bg-gray-300 dark:bg-white/10 rounded-full peer peer-checked:bg-banhafade-accent peer-focus:outline-none transition-colors after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all after:shadow-sm peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white">
        </div>
    </label>
</div>
