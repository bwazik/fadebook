{{-- iOS 26 Accent Color Picker --}}
@php
    $palettes = [
        'classic' => ['label' => 'كلاسيك', 'color' => '#ff2d55'],
        'ocean' => ['label' => 'أوشن', 'color' => '#007aff'],
        'mint' => ['label' => 'مينت', 'color' => '#34c759'],
        'sunset' => ['label' => 'صنست', 'color' => '#ff9f0a'],
        'lavender' => ['label' => 'لافندر', 'color' => '#bf5af2'],
    ];
@endphp

<div x-data="{
    current: localStorage.getItem('banhafade_accent') || '#ff9f0a',
    setAccent(color) {
        this.current = color;
        document.documentElement.style.setProperty('--color-banhafade-accent', color);
        localStorage.setItem('banhafade_accent', color);
    }
}">
    <div class="flex items-center gap-3 justify-center py-2">
        @foreach ($palettes as $key => $palette)
            <button type="button" @click="setAccent('{{ $palette['color'] }}')" title="{{ $palette['label'] }}"
                class="w-9 h-9 rounded-full transition-all duration-200 active:scale-90 relative cursor-pointer"
                style="background-color: {{ $palette['color'] }};">
                <div x-show="current === '{{ $palette['color'] }}'"
                    class="absolute inset-x-[-4px] inset-y-[-4px] rounded-full border-2"
                    style="border-color: {{ $palette['color'] }};"></div>
                <svg x-show="current === '{{ $palette['color'] }}'" class="w-4 h-4 text-white absolute inset-0 m-auto"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                </svg>
            </button>
        @endforeach
    </div>
</div>
