{{-- iOS Select Component --}}
@props(['label' => null, 'error' => null])

<div class="relative px-4 py-3 bg-transparent border-b border-black/5 dark:border-white/5 last:border-0">
    @if($label)
        <label class="block text-[11px] font-medium text-gray-500 dark:text-gray-400 mb-0.5 uppercase tracking-wider">
            {{ $label }}
        </label>
    @endif
    
    <div class="relative flex items-center">
        <select {{ $attributes->merge([
            'class' => 'w-full bg-transparent border-0 p-0 text-[17px] focus:ring-0 text-gray-900 dark:text-white appearance-none cursor-pointer placeholder-gray-400'
        ]) }}>
            {{ $slot }}
        </select>
        
        <div class="absolute inset-y-0 left-0 flex items-center pointer-events-none">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="PS 19 9l-7 7-7-7" />
            </svg>
        </div>
    </div>

    @if($error)
        <p class="mt-1 text-[13px] text-red-500">{{ $error }}</p>
    @endif
</div>
