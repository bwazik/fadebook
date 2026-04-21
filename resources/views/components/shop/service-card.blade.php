@props(['service', 'selected' => false, 'href' => null, 'unavailable' => false, 'forceShow' => false, 'showPrices' => true])

@php
    $tag = $href ? 'a' : 'button';
    $isActuallyUnavailable = $unavailable || !$service->is_active;
    $showAsUnavailable = $isActuallyUnavailable && !$forceShow;
@endphp

<{{ $tag }} @if ($href) href="{{ $href }}" wire:navigate @endif
    {{ $attributes->merge([
        'class' =>
            'w-full liquid-glass liquid-button rounded-[1.5rem] p-5 flex items-center justify-between border-2 transition-all shadow-sm text-right group ' .
            ($selected
                ? 'border-banhafade-accent bg-banhafade-accent/5'
                : 'border-white/50 dark:border-white/10 shadow-black/5') .
            ($showAsUnavailable ? ' opacity-50 grayscale' : '') .
            ($isActuallyUnavailable && !$href ? ' cursor-not-allowed' : ''),
    ]) }}>
    <div class="flex-1 pe-4">
        <div class="flex items-center gap-2">
            <h3
                class="text-sm font-black text-gray-900 dark:text-white uppercase leading-tight group-hover:text-banhafade-accent transition-colors">
                {{ $service->name }}
            </h3>
        </div>

        @if ($service->description)
            <p class="text-[10px] text-gray-400 font-bold mt-1 line-clamp-1 italic opacity-80">
                {{ $service->description }}</p>
        @endif

        <div class="flex items-center gap-2 {{ $service->description ? 'mt-2.5' : 'mt-1.5' }}">
            <div
                class="flex items-center gap-1 text-[9px] text-gray-500 dark:text-gray-400 font-black uppercase tracking-wider bg-black/5 dark:bg-white/5 px-2 py-0.5 rounded-lg border border-black/5">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3"
                    stroke="currentColor" class="w-2.5 h-2.5 text-banhafade-accent">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
                {{ $service->duration_minutes }} {{ __('messages.min') }}
            </div>
        </div>
    </div>

    @if ($showPrices)
    <div class="shrink-0 text-left">
        <p class="text-xl font-black text-banhafade-accent tracking-tighter leading-none">
            {{ number_format($service->price, 0) }}
            <span class="text-[10px] ms-1">{{ __('messages.egp') }}</span>
        </p>
    </div>
    @endif
    </{{ $tag }}>
