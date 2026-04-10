{{-- Empty State Component --}}
@props(['icon' => null, 'title' => '', 'description' => ''])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center py-16 px-6 text-center']) }}>
    @if($icon)
        <div class="w-16 h-16 rounded-3xl bg-black/5 dark:bg-white/10
                    flex items-center justify-center mb-4">
            {{ $icon }}
        </div>
    @endif

    @if($title)
        <p class="text-base font-bold text-gray-900 dark:text-white mb-1">{{ $title }}</p>
    @endif

    @if($description)
        <p class="text-sm text-gray-500 dark:text-white/40 leading-relaxed">{{ $description }}</p>
    @endif

    @if($slot->isNotEmpty())
        <div class="mt-6">{{ $slot }}</div>
    @endif
</div>
