{{-- Toast Component --}}
<div
    x-data="{
        show: false,
        message: '',
        type: 'info',
        timeout: null,
        showToast(event) {
            this.message = event.detail[0]?.message || event.detail.message;
            this.type = event.detail[0]?.type || event.detail.type || 'info';
            this.show = true;
            
            if (this.timeout) clearTimeout(this.timeout);
            
            this.timeout = setTimeout(() => {
                this.show = false;
            }, 3000);
        },
        init() {
            @if(session()->has('success'))
                this.message = '{{ session('success') }}';
                this.type = 'success';
                this.show = true;
                this.timeout = setTimeout(() => { this.show = false; }, 3000);
            @endif
            @if(session()->has('error'))
                this.message = '{{ session('error') }}';
                this.type = 'error';
                this.show = true;
                this.timeout = setTimeout(() => { this.show = false; }, 3000);
            @endif
        }
    }"
    x-on:toast.window="showToast($event)"
    x-show="show"
    x-transition:enter="transition ease-out duration-300 transform"
    x-transition:enter-start="translate-y-full opacity-0 scale-95"
    x-transition:enter-end="translate-y-0 opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-200 transform"
    x-transition:leave-start="translate-y-0 opacity-100 scale-100"
    x-transition:leave-end="translate-y-full opacity-0 scale-95"
    class="fixed top-12 left-1/2 -translate-x-1/2 z-[100] w-[90%] max-w-sm pointer-events-none"
    style="display: none;"
>
    <div
        :class="{
            'bg-black/80 dark:bg-white/90 text-white dark:text-black': type === 'info',
            'bg-green-600/90 text-white': type === 'success',
            'bg-red-600/90 text-white': type === 'error',
            'bg-yellow-500/90 text-white': type === 'warning',
        }"
        class="backdrop-blur-2xl rounded-2xl px-5 py-3.5 shadow-2xl flex items-center gap-3 border border-white/10 dark:border-black/5"
    >
        <div class="flex-1 text-[15px] font-semibold text-center">
            <span x-text="message"></span>
        </div>
    </div>
</div>
