{{-- Toast Component --}}
<div x-data="{
    show: false,
    message: '',
    type: 'success',
    timeout: null,
    progress: 100,
    progressInterval: null,
    duration: 3000,
    toast(data) {
        this.message = data.message || '';
        this.type = data.type || 'success';
        this.show = true;
        this.progress = 100;

        clearTimeout(this.timeout);
        clearInterval(this.progressInterval);

        const startTime = Date.now();
        this.progressInterval = setInterval(() => {
            const elapsed = Date.now() - startTime;
            this.progress = Math.max(0, 100 - (elapsed / this.duration * 100));
            if (this.progress === 0) {
                clearInterval(this.progressInterval);
            }
        }, 16); // ~60fps

        this.timeout = setTimeout(() => {
            this.show = false;
            clearInterval(this.progressInterval);
        }, this.duration);
    }
}" @toast.window="toast($event.detail)"
    class="fixed top-[calc(1rem+env(safe-area-inset-top))] left-1/2 -translate-x-1/2 z-[100]">

    <div x-show="show" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-4 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 -translate-y-4 scale-95" class="relative" style="display: none;">

        {{-- Progress Border Ring --}}
        <div class="absolute -inset-[2px] rounded-[1.9rem] pointer-events-none transition-none"
            :style="`background: conic-gradient(from -90deg, ${type === 'error' ? 'rgb(239 68 68)' : 'rgb(34 197 94)'} 0% ${progress}%, transparent ${progress}% 100%);`">
        </div>

        {{-- Toast Content --}}
        <div class="relative flex items-center gap-3 liquid-panel rounded-[1.8rem] px-5 py-3 transition-all duration-300 shadow-xl border border-white/20 dark:border-white/10 w-max max-w-[calc(100vw-3rem)] sm:max-w-md mx-auto">
            {{-- Success Icon --}}
            <template x-if="type === 'success'">
                <div class="w-6 h-6 rounded-full bg-green-500/15 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                        stroke="currentColor" class="w-3.5 h-3.5 text-green-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                </div>
            </template>

            {{-- Error Icon --}}
            <template x-if="type === 'error'">
                <div class="w-6 h-6 rounded-full bg-red-500/15 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                        stroke="currentColor" class="w-3.5 h-3.5 text-red-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
            </template>

            <span class="text-xs font-black leading-tight tracking-tight whitespace-normal"
                :class="type === 'error' ? 'text-red-500 dark:text-red-400' : 'text-gray-900 dark:text-white'"
                x-text="message">
            </span>
        </div>
    </div>
</div>
