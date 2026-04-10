{{-- Glass Card Component --}}
<div {{ $attributes->merge([
    'class' => 'relative overflow-hidden bg-white/70 dark:bg-[#1c1c1e]/70 backdrop-blur-2xl border border-black/5 dark:border-white/10 rounded-[2rem] p-5 shadow-sm transition-all duration-300'
]) }}>
    {{ $slot }}
</div>
