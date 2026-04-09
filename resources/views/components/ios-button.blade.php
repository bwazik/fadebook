{{-- iOS Button Component --}}
{{-- Usage: <x-ios-button :target="'save'">حفظ</x-ios-button> --}}
@props(['target' => null])

<button {{ $attributes->merge([
    'type' => 'submit',
    'class' => 'w-full mt-4 py-3.5 rounded-2xl font-bold active:scale-95 transition-all disabled:opacity-50 flex justify-center items-center gap-2 text-white',
    'style' => 'background-color: var(--color-fadebook-accent); box-shadow: 0 8px 24px color-mix(in srgb, var(--color-fadebook-accent) 30%, transparent);'
]) }}>
    @if($target)
        <span wire:loading.remove wire:target="{{ $target }}">{{ $slot }}</span>
        <span wire:loading wire:target="{{ $target }}" class="w-5 h-5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
        <span wire:loading wire:target="{{ $target }}">اتقل...</span>
    @else
        {{ $slot }}
    @endif
</button>
