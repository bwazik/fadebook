<section class="mx-auto max-w-4xl rounded-[2rem] border border-white/10 bg-white/8 p-6 shadow-2xl shadow-black/20 backdrop-blur sm:p-8">
    <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-white">إعداد بيانات الصالون</h1>
            <p class="mt-2 text-sm leading-7 text-stone-300">كمّل 3 خطوات بسيطة عشان طلبك يدخل المراجعة.</p>

            @if ($shop?->status === \App\Enums\ShopStatus::Rejected)
                <div class="mt-4 rounded-2xl border border-rose-400/30 bg-rose-400/10 px-4 py-3 text-sm text-rose-100">
                    <p class="font-medium">الطلب اترفض قبل كده.</p>
                    <p class="mt-1">السبب: {{ $shop->rejection_reason }}</p>
                </div>
            @endif
        </div>

        <div class="flex gap-2 text-xs text-stone-300">
            @foreach ([1 => 'البيانات الأساسية', 2 => 'التواصل واللوجو', 3 => 'الخدمات والمواعيد'] as $number => $label)
                <div class="rounded-full px-3 py-2 {{ $step === $number ? 'bg-orange-500 text-stone-950' : 'border border-white/10 bg-black/20' }}">{{ $label }}</div>
            @endforeach
        </div>
    </div>

    <div class="space-y-6">
        @if ($step === 1)
            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label for="shop_name" class="mb-2 block text-sm text-stone-200">اسم الصالون</label>
                    <input wire:model="shop_name" id="shop_name" type="text" class="w-full rounded-2xl border border-white/10 bg-black/20 px-4 py-3 text-white outline-none transition focus:border-orange-400/60" placeholder="مثال: Fade Cut" />
                    @error('shop_name') <p class="mt-2 text-sm text-rose-300">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="area_id" class="mb-2 block text-sm text-stone-200">المنطقة</label>
                    <select wire:model="area_id" id="area_id" class="w-full rounded-2xl border border-white/10 bg-black/20 px-4 py-3 text-white outline-none transition focus:border-orange-400/60">
                        <option value="">اختار المنطقة</option>
                        @foreach ($areas as $area)
                            <option value="{{ $area->id }}">{{ $area->name }}</option>
                        @endforeach
                    </select>
                    @error('area_id') <p class="mt-2 text-sm text-rose-300">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label for="address" class="mb-2 block text-sm text-stone-200">العنوان</label>
                <textarea wire:model="address" id="address" rows="4" class="w-full rounded-2xl border border-white/10 bg-black/20 px-4 py-3 text-white outline-none transition focus:border-orange-400/60" placeholder="اكتب العنوان بالتفصيل"></textarea>
                @error('address') <p class="mt-2 text-sm text-rose-300">{{ $message }}</p> @enderror
            </div>
        @endif

        @if ($step === 2)
            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label for="phone" class="mb-2 block text-sm text-stone-200">رقم الصالون</label>
                    <input wire:model="phone" id="phone" type="text" inputmode="tel" class="w-full rounded-2xl border border-white/10 bg-black/20 px-4 py-3 text-white outline-none transition focus:border-orange-400/60" placeholder="01012345678" />
                    @error('phone') <p class="mt-2 text-sm text-rose-300">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="logo" class="mb-2 block text-sm text-stone-200">لوجو الصالون</label>
                    <input wire:model="logo" id="logo" type="file" accept="image/png,image/jpeg" class="block w-full rounded-2xl border border-dashed border-white/10 bg-black/20 px-4 py-3 text-sm text-stone-300 file:ml-4 file:rounded-full file:border-0 file:bg-orange-500 file:px-4 file:py-2 file:text-sm file:font-medium file:text-stone-950" />
                    @error('logo') <p class="mt-2 text-sm text-rose-300">{{ $message }}</p> @enderror
                </div>
            </div>
        @endif

        @if ($step === 3)
            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label for="basic_services" class="mb-2 block text-sm text-stone-200">الخدمات الأساسية</label>
                    <textarea wire:model="basic_services" id="basic_services" rows="4" class="w-full rounded-2xl border border-white/10 bg-black/20 px-4 py-3 text-white outline-none transition focus:border-orange-400/60" placeholder="حلاقة، دقن، تنظيف بشرة"></textarea>
                    <p class="mt-2 text-xs text-stone-400">افصل بين كل خدمة والتانية بفاصلة.</p>
                    @error('basic_services') <p class="mt-2 text-sm text-rose-300">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="barbers_count" class="mb-2 block text-sm text-stone-200">عدد الحلاقين</label>
                    <input wire:model="barbers_count" id="barbers_count" type="number" min="1" class="w-full rounded-2xl border border-white/10 bg-black/20 px-4 py-3 text-white outline-none transition focus:border-orange-400/60" placeholder="3" />
                    @error('barbers_count') <p class="mt-2 text-sm text-rose-300">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="space-y-4">
                <h2 class="text-lg font-semibold text-white">مواعيد الشغل</h2>

                @foreach ($opening_hours as $index => $hours)
                    <div class="grid gap-4 rounded-2xl border border-white/10 bg-black/15 p-4 md:grid-cols-[1fr_auto_1fr_1fr] md:items-center">
                        <div>
                            <p class="font-medium text-white">{{ $hours['label'] }}</p>
                        </div>

                        <label class="inline-flex items-center gap-2 text-sm text-stone-300">
                            <input wire:model="opening_hours.{{ $index }}.is_closed" type="checkbox" class="h-4 w-4 rounded border-white/20 bg-black/20 text-orange-500 focus:ring-orange-500" />
                            اليوم ده مقفول
                        </label>

                        <div>
                            <input wire:model="opening_hours.{{ $index }}.open_time" type="time" class="w-full rounded-2xl border border-white/10 bg-black/20 px-4 py-3 text-white outline-none transition focus:border-orange-400/60 disabled:opacity-40" @disabled($hours['is_closed']) />
                            @error('opening_hours.'.$index.'.open_time') <p class="mt-2 text-sm text-rose-300">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <input wire:model="opening_hours.{{ $index }}.close_time" type="time" class="w-full rounded-2xl border border-white/10 bg-black/20 px-4 py-3 text-white outline-none transition focus:border-orange-400/60 disabled:opacity-40" @disabled($hours['is_closed']) />
                            @error('opening_hours.'.$index.'.close_time') <p class="mt-2 text-sm text-rose-300">{{ $message }}</p> @enderror
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="mt-8 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">
        <button type="button" wire:click="previousStep" class="rounded-2xl border border-white/10 px-5 py-3 text-sm font-semibold text-stone-100 transition hover:border-orange-400/60 {{ $step === 1 ? 'invisible' : '' }}">السابق</button>

        <div class="flex justify-end gap-3">
            @if ($step < 3)
                <button type="button" wire:click="nextStep" class="rounded-2xl bg-orange-500 px-5 py-3 text-sm font-semibold text-stone-950 transition hover:bg-orange-400">التالي</button>
            @else
                <button type="button" wire:click="save" class="rounded-2xl bg-orange-500 px-5 py-3 text-sm font-semibold text-stone-950 transition hover:bg-orange-400">ابعت للمراجعة</button>
            @endif
        </div>
    </div>
</section>
