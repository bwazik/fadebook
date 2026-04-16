<div x-data="{
    show: false,
    title: '',
    message: '',
    action: '',
    params: null,
    componentId: null,
    open(data) {
        this.title = data.title || '';
        this.message = data.message || '';
        this.action = data.action || '';
        this.params = data.params ?? null;
        this.componentId = data.componentId || null;
        this.show = true;
    },
    confirm() {
        if (this.componentId && this.action) {
            Livewire.find(this.componentId).call(this.action, this.params);
        }
        this.show = false;
    },
    cancel() {
        this.show = false;
    }
}" @open-ios-alert.window="open($event.detail)" x-show="show" x-cloak
    x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-[110] flex items-center justify-center bg-black/40 backdrop-blur-sm">

    <div x-show="show" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
        class="w-[270px] bg-white/80 dark:bg-[#2c2c2e]/80 backdrop-blur-3xl rounded-[1.25rem] flex flex-col text-center overflow-hidden border border-black/5 dark:border-white/10 shadow-2xl"
        @click.stop>

        {{-- Content --}}
        <div class="px-5 pt-5 pb-4">
            <h3 class="text-[17px] font-black text-gray-900 dark:text-white mb-1 leading-tight" x-text="title"></h3>
            <p class="text-[13px] text-gray-500 dark:text-gray-400 font-medium leading-relaxed" x-text="message"></p>
        </div>

        <div class="border-t border-black/5 dark:border-white/10 flex">
            <button @click="cancel()"
                class="flex-1 py-3 text-[17px] font-bold text-gray-400 dark:text-gray-500 border-l border-black/5 dark:border-white/10 active:bg-black/5 dark:active:bg-white/10 transition-colors cursor-pointer">
                {{ __('messages.back') }}
            </button>
            <button @click="confirm()"
                class="flex-1 py-3 text-[17px] font-black text-fadebook-accent active:bg-black/5 dark:active:bg-white/10 transition-colors cursor-pointer">
                {{ __('messages.confirm') }}
            </button>
        </div>
    </div>
</div>
