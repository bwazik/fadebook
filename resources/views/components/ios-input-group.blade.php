{{-- iOS Input Group Component --}}
{{-- Usage: <x-ios-input-group><x-ios-input label="الاسم" .../></x-ios-input-group> --}}
<div {{ $attributes->merge([
    'class' => 'bg-white/70 dark:bg-[#1c1c1e]/70 backdrop-blur-3xl border border-black/5 dark:border-white/10 rounded-3xl overflow-hidden shadow-sm'
]) }}>
    {{ $slot }}
</div>
