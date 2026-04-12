{{-- iOS Input Component --}}
@props(['label' => '', 'id' => '', 'type' => 'text', 'dir' => 'auto', 'labelWidth' => 'w-16'])

<div>
    @if($label)
        <label for="{{ $id }}" class="block text-xs font-medium text-gray-600 dark:text-white/60 mb-1.5">{{ $label }}</label>
    @endif
    <div class="relative">
        <input {{ $attributes->merge([
            'type' => $type,
            'id' => $id,
            'dir' => $dir,
            'class' => 'liquid-transition w-full rounded-2xl bg-black/5 dark:bg-white/10 border-0 text-gray-900 dark:text-white text-sm px-4 py-3.5 focus:bg-white dark:focus:bg-[#3a3a3c] focus:ring-2 focus:ring-fadebook-accent/50 outline-none ' . ($dir === 'ltr' ? 'text-left' : 'text-right') . '
                        [&:-webkit-autofill]:[transition:background-color_9999999s_ease-in-out_0s]
                        [&:-webkit-autofill]:[-webkit-text-fill-color:inherit]
                        dark:[&:-webkit-autofill]:[-webkit-text-fill-color:#fff]'
        ]) }}>
    </div>
</div>
