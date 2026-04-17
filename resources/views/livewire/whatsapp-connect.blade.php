<div class="pb-[calc(5rem+var(--safe-area-bottom)+64px)] min-h-screen pt-4 px-4 relative">
    {{-- ═══════════════════════════════ --}}
    {{-- 1. HEADING HEADER               --}}
    {{-- ═══════════════════════════════ --}}
    <div class="mb-6 mt-4">
        <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">
            ربط حساب واتساب
        </h1>
    </div>

    {{-- ═══════════════════════════════ --}}
    {{-- 2. MAIN CONTENT (NO CARD)       --}}
    {{-- ═══════════════════════════════ --}}
    <div class="flex flex-col items-center justify-center text-center mt-8">
        <p class="text-sm font-bold text-gray-500 dark:text-gray-400 leading-relaxed mb-8 px-4">
            قم بتوليد الكود ومسحه باستخدام هاتفك لربط حساب الواتساب بالمنصة وإرسال الإشعارات.
        </p>

        <div class="min-h-[250px] flex flex-col items-center justify-center mb-8 relative w-full">
            <!-- Loading State -->
            <div wire:loading wire:target="fetchQr" class="absolute inset-0 flex items-center justify-center z-10 rounded-xl">
                <div class="animate-spin rounded-full h-12 w-12 border-4 border-banhafade-accent border-t-transparent"></div>
            </div>

            <!-- Success State -->
            @if($connected)
                <div class="flex flex-col items-center animate-in zoom-in-95 duration-300">
                    <div class="h-20 w-20 bg-green-100 dark:bg-green-900/30 text-green-500 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                </div>
            @endif

            <!-- QR Code Display -->
            @if($qr && !$connected)
                <img src="{{ str_starts_with($qr, 'data:') ? $qr : 'data:image/png;base64,' . $qr }}" class="w-64 h-64 border-4 border-white dark:border-gray-800 shadow-2xl rounded-[2.5rem] animate-in zoom-in-95 duration-300 object-cover" alt="WhatsApp QR Code"/>
            @endif
        </div>

        <x-ios-button wire:click="fetchQr" wire:loading.attr="disabled" :disabled="$connected" class="!h-12 px-8 bg-banhafade-accent !text-white !rounded-2xl font-black shadow-xl active:scale-95 transition-all text-sm w-full max-w-[300px]">
            <span wire:loading.remove wire:target="fetchQr">
                {{ $connected ? 'تم الربط' : 'إنشاء / تحديث الكود (QR Code)' }}
            </span>
            <span wire:loading wire:target="fetchQr">جاري التحميل...</span>
        </x-ios-button>
    </div>
</div>
