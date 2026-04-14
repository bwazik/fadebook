<div class="pb-[calc(5rem+var(--safe-area-bottom)+64px)] relative min-h-screen pt-4 px-4 overflow-y-auto">
    <!-- Header -->
    <div class="mb-6 mt-4">
        <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">
            إعدادات المحل
        </h1>
    </div>

    <form wire:submit="save" class="space-y-6 pb-20">
        <!-- Logo & Banner -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Logo Upload -->
            <div class="liquid-glass rounded-3xl p-5 border border-white/20 shadow-sm flex flex-col items-center">
                <h2 class="text-xs font-black text-fadebook-accent uppercase tracking-[0.2em] mb-4 w-full text-center">
                    شعار المحل</h2>
                <div class="relative group">
                    <div
                        class="w-24 h-24 rounded-full overflow-hidden border-2 border-fadebook-accent/30 shadow-lg bg-black/5 dark:bg-white/5 flex items-center justify-center relative">
                        @if ($logo)
                            <img src="{{ $logo->temporaryUrl() }}" class="w-full h-full object-cover">
                        @elseif($shop->getImage('logo')->exists())
                            <img src="{{ Storage::url($shop->getImage('logo')->first()->path) }}"
                                class="w-full h-full object-cover">
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                            </svg>
                        @endif

                        <label
                            class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer backdrop-blur-[2px]">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="w-6 h-6 text-white">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                            <input type="file" wire:model="logo" class="hidden" accept="image/*">
                        </label>
                    </div>
                </div>
                <div wire:loading wire:target="logo" class="mt-2">
                    <p class="text-[9px] font-black text-fadebook-accent animate-pulse">جاري الرفع...</p>
                </div>
            </div>

            <!-- Banner Upload -->
            <div class="liquid-glass rounded-3xl p-5 border border-white/20 shadow-sm flex flex-col items-center">
                <h2 class="text-xs font-black text-fadebook-accent uppercase tracking-[0.2em] mb-4 w-full text-center">
                    غلاف المحل</h2>
                <div class="relative group w-full">
                    <div
                        class="w-full h-24 rounded-2xl overflow-hidden border-2 border-fadebook-accent/30 shadow-lg bg-black/5 dark:bg-white/5 flex items-center justify-center relative">
                        @if ($banner)
                            <img src="{{ $banner->temporaryUrl() }}" class="w-full h-full object-cover">
                        @elseif($shop->getImage('banner')->exists())
                            <img src="{{ Storage::url($shop->getImage('banner')->first()->path) }}"
                                class="w-full h-full object-cover">
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>
                        @endif

                        <label
                            class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity cursor-pointer backdrop-blur-[2px]">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                                stroke="currentColor" class="w-6 h-6 text-white">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                            </svg>
                            <input type="file" wire:model="banner" class="hidden" accept="image/*">
                        </label>
                    </div>
                </div>
                <div wire:loading wire:target="banner" class="mt-2">
                    <p class="text-[9px] font-black text-fadebook-accent animate-pulse">جاري الرفع...</p>
                </div>
            </div>
        </div>

        <!-- Gallery -->
        <div class="liquid-glass rounded-3xl p-5 border border-white/20 shadow-sm">
            <h2 class="text-xs font-black text-fadebook-accent uppercase tracking-[0.2em] mb-4">معرض الصور</h2>

            <div class="grid grid-cols-3 gap-3">
                <!-- Existing Gallery -->
                @foreach ($gallery as $img)
                    <div class="relative aspect-square rounded-xl overflow-hidden group border border-white/10 shadow-sm"
                        data-id="{{ $img['id'] }}">
                        <img src="{{ $img['url'] }}" class="w-full h-full object-cover">
                        <button type="button" wire:click="deleteGalleryImage({{ $img['id'] }})"
                            class="absolute top-1 right-1 w-6 h-6 rounded-full bg-red-500/80 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity backdrop-blur-sm cursor-pointer">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="2.5" stroke="currentColor" class="w-3.5 h-3.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                @endforeach

                <!-- New Gallery Previews -->
                @foreach ($newGalleryImages as $index => $img)
                    <div
                        class="relative aspect-square rounded-xl overflow-hidden border border-fadebook-accent/30 shadow-sm">
                        <img src="{{ $img->temporaryUrl() }}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-fadebook-accent/20 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="2.5" stroke="currentColor" class="w-4 h-4 text-white animate-pulse">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                        </div>
                    </div>
                @endforeach

                <!-- Upload Button -->
                <label
                    class="aspect-square rounded-xl border-2 border-dashed border-fadebook-accent/30 bg-fadebook-accent/5 flex flex-col items-center justify-center cursor-pointer active:scale-95 transition-all group overflow-hidden relative">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor"
                        class="w-6 h-6 text-fadebook-accent group-hover:scale-110 transition-transform">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span class="text-[8px] font-black text-fadebook-accent uppercase tracking-widest mt-1">إضافة
                        صورة</span>
                    <input type="file" wire:model="newGalleryImages" class="hidden" multiple accept="image/*">

                    <div wire:loading wire:target="newGalleryImages"
                        class="absolute inset-0 bg-white/60 dark:bg-black/60 backdrop-blur-sm flex items-center justify-center">
                        <div
                            class="w-4 h-4 border-2 border-fadebook-accent border-t-transparent rounded-full animate-spin">
                        </div>
                    </div>
                </label>
            </div>
        </div>

        <!-- Basic Info -->
        <div class="liquid-glass rounded-3xl p-5 border border-white/20 shadow-sm space-y-4">
            <h2 class="text-xs font-black text-fadebook-accent uppercase tracking-[0.2em] mb-4">البيانات الأساسية</h2>

            <x-ios-input label="اسم المحل" wire:model="name" type="text" />
            <x-ios-input label="رقم الهاتف" wire:model="phone" type="tel" dir="ltr" />
            <x-ios-input label="العنوان التفصيلي" wire:model="address" type="text" />

            <x-ios-select label="المنطقة" wire:model="area_id" :options="$areas->pluck('name', 'id')->toArray()" />

            <x-ios-textarea label="وصف المحل" wire:model="description" rows="3" />
        </div>

        <!-- Booking Settings -->
        <div class="liquid-glass rounded-3xl p-5 border border-white/20 shadow-sm space-y-6">
            <h2 class="text-xs font-black text-fadebook-accent uppercase tracking-[0.2em] mb-4">إعدادات الحجز</h2>

            <!-- Online Toggle -->
            <div @click="$refs.onlineToggle.click()"
                class="flex items-center justify-between p-4 rounded-2xl bg-black/5 dark:bg-white/10 cursor-pointer active:scale-[0.98] transition-all">
                <div>
                    <p class="text-sm font-black text-gray-900 dark:text-white leading-none">متاح للحجز</p>
                    <p class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-widest">المحل يستقبل حجوزات
                        حالياً</p>
                </div>
                <div class="relative inline-flex items-center">
                    <input type="checkbox" wire:model="is_online" x-ref="onlineToggle" class="sr-only peer">
                    <div
                        class="w-11 h-6 bg-gray-200/50 peer-focus:outline-none rounded-full peer dark:bg-white/5 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-fadebook-accent">
                    </div>
                </div>
            </div>

            <x-ios-input label="أيام الحجز المسبق" wire:model="advance_booking_days" type="number"
                help="أقصى عدد أيام يمكن للعميل الحجز خلالها" />

            <x-ios-select label="طريقة اختيار الحلاق" wire:model="barber_selection_mode" :options="[
                '1' => 'أي حلاق متاح (توزيع تلقائي)',
                '2' => 'العميل يختار الحلاق',
            ]" />

            <x-ios-select label="نظام الدفع" wire:model.live="payment_mode" :options="[
                '0' => 'بدون دفع (كاش في المحل)',
                '1' => 'عربون (نسبة مئوية)',
                '2' => 'دفع كامل القيمة',
            ]" />

            @if ($payment_mode == 1)
                <x-ios-input label="نسبة العربون (%)" wire:model="deposit_percentage" type="number" />
            @endif
        </div>

        <!-- Opening Hours -->
        <div class="liquid-glass rounded-3xl p-5 border border-white/20 shadow-sm space-y-4">
            <h2 class="text-xs font-black text-fadebook-accent uppercase tracking-[0.2em] mb-4">مواعيد العمل</h2>

            <div class="space-y-3">
                @foreach ($days as $day)
                    <div
                        class="p-4 rounded-2xl transition-all {{ $opening_hours[$day]['is_open'] ? 'bg-black/5 dark:bg-white/10 border border-white/10' : 'bg-transparent border border-dashed border-black/10 dark:border-white/10 opacity-60' }}">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-black text-gray-900 dark:text-white capitalize">
                                {{ __('messages.day_' . $day) }}
                            </span>
                            <div @click="$refs.toggle_{{ $day }}.click()"
                                class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" wire:model.live="opening_hours.{{ $day }}.is_open"
                                    x-ref="toggle_{{ $day }}" class="sr-only peer">
                                <div
                                    class="w-11 h-6 bg-gray-200/50 peer-focus:outline-none rounded-full peer dark:bg-white/5 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-fadebook-accent">
                                </div>
                            </div>
                        </div>

                        @if ($opening_hours[$day]['is_open'])
                            <div class="flex gap-3 animate-in fade-in slide-in-from-top-2 duration-300">
                                <div class="flex-1">
                                    <label
                                        class="block text-[8px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1.5 ms-2">من</label>
                                    <input type="time" wire:model="opening_hours.{{ $day }}.open"
                                        class="w-full bg-black/5 dark:bg-white/5 rounded-xl border-0 text-gray-900 dark:text-white text-xs font-bold p-3 focus:ring-2 focus:ring-fadebook-accent/50 outline-none">
                                </div>
                                <div class="flex-1">
                                    <label
                                        class="block text-[8px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1.5 ms-2">إلى</label>
                                    <input type="time" wire:model="opening_hours.{{ $day }}.close"
                                        class="w-full bg-black/5 dark:bg-white/5 rounded-xl border-0 text-gray-900 dark:text-white text-xs font-bold p-3 focus:ring-2 focus:ring-fadebook-accent/50 outline-none">
                                </div>
                            </div>
                        @else
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center py-1">
                                مغلق</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <div class="sticky bottom-[calc(var(--safe-area-bottom)+80px)] z-20">
            <x-ios-button type="submit" wire:loading.attr="disabled" target="save">
                حفظ الإعدادات
            </x-ios-button>
        </div>
    </form>
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
@endpush
