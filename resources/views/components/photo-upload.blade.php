{{-- Photo Upload Component --}}
@props(['currentPhoto' => null, 'photo' => null, 'wireModel' => 'photo', 'sizeClasses' => 'w-20 h-20', 'label' => '', 'placeholder' => 'images/barber-default.jpg'])

@php
    $inputId = 'photo-upload-' . uniqid();
@endphp

<div class="flex flex-col items-center gap-2">
    {{-- Clickable Avatar for Photo Upload --}}
    <div {{ $attributes->except(['wireModel', 'wire:model', 'wire:model.live'])->merge(['class' => "relative $sizeClasses rounded-full overflow-hidden border-2 border-black/5 dark:border-white/10 cursor-pointer group shrink-0 transition-all duration-200"]) }}
        onclick="document.getElementById('{{ $inputId }}').click()">
        
        {{-- Preview Image --}}
        @if ($photo)
            <img src="{{ $photo->temporaryUrl() }}" class="w-full h-full object-cover">
        @else
            <img src="{{ $currentPhoto
                ? (Str::startsWith($currentPhoto, 'http')
                    ? $currentPhoto
                    : Storage::url($currentPhoto))
                : asset($placeholder) }}"
                referrerpolicy="no-referrer"
                class="w-full h-full object-cover group-hover:opacity-50 transition-opacity duration-200">
        @endif

        {{-- Hover Overlay --}}
        <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-white">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
            </svg>
        </div>

        {{-- Upload Loading State --}}
        <div wire:loading wire:target="{{ $wireModel }}" class="absolute inset-0 bg-black/60 flex items-center justify-center">
            <span class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
        </div>
    </div>

    @if($label)
        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $label }}</span>
    @endif

    {{-- Hidden File Input --}}
    <input type="file" 
        id="{{ $inputId }}" 
        wire:model.live="{{ $wireModel }}"
        class="hidden"
        accept="image/jpeg,image/png,image/webp">
</div>
