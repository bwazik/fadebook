{{-- iOS Textarea Component --}}
{{-- Usage: <x-ios-textarea wire:model="notes" placeholder="ملاحظات..." rows="4" /> --}}
<textarea {{ $attributes->merge([
    'class' => 'w-full rounded-2xl bg-black/5 dark:bg-white/10 border-0 text-gray-900 dark:text-white text-sm px-4 py-3 focus:bg-white dark:focus:bg-[#3a3a3c] focus:ring-2 focus:outline-none transition-all resize-none placeholder-gray-400 dark:placeholder-white/30',
    'style' => 'focus:ring-color: var(--color-fadebook-accent);'
]) }}></textarea>
