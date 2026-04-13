@props(['step', 'total' => 4])

<div {{ $attributes->merge(['class' => 'flex justify-between mb-8 relative px-2']) }}>
    {{-- Background Line --}}
    <div class="absolute top-1/2 left-4 right-4 h-0.5 bg-gray-200 dark:bg-gray-800 -translate-y-1/2 rounded-full z-0"></div>
    
    {{-- Progress Line --}}
    <div class="absolute top-1/2 right-4 h-0.5 bg-fadebook-accent -translate-y-1/2 rounded-full z-0 transition-all duration-500 ease-[cubic-bezier(0.2,0.8,0.2,1)]"
        :style="'width: calc(' + (({{ $step }} - 1) / {{ $total - 1 }} * 100) + '% - ' + ({{ $step }} === 1 ? '0px' : '32px') + ')'"></div>

    {{-- Step Circles --}}
    @for ($i = 1; $i <= $total; $i++)
        <div class="w-8 h-8 rounded-full flex items-center justify-center text-[10px] font-black relative z-10 transition-all duration-300 border-2"
            :class="{{ $step }} >= {{ $i }} ?
                'bg-fadebook-accent border-fadebook-accent text-white shadow-[0_0_15px_rgba(1,101,225,0.4)]' :
                'bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-800 text-gray-400'">
            {{ $i }}
        </div>
    @endfor
</div>
