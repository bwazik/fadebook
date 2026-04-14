@props(['label' => '', 'id' => ''])

<div>
    @if($label)
        <label for="{{ $id }}" class="block text-xs font-medium text-gray-600 dark:text-white/60 mb-1.5">{{ $label }}</label>
    @endif
    <textarea {{ $attributes->merge([
        'id' => $id,
        'class' => 'liquid-transition w-full rounded-2xl bg-black/5 dark:bg-white/10 border-0 text-gray-900 dark:text-white text-sm px-4 py-3 focus:bg-white dark:focus:bg-[#3a3a3c] focus:ring-2 focus:ring-fadebook-accent/50 outline-none transition-all resize-none placeholder-gray-400 dark:placeholder-white/30',
    ]) }}></textarea>
</div>
