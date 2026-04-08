<section class="mx-auto max-w-2xl rounded-[2rem] border border-white/10 bg-white/8 p-6 shadow-2xl shadow-black/20 backdrop-blur sm:p-8">
    <div class="mb-8">
        <h1 class="text-2xl font-semibold text-white">سجّل حساب جديد</h1>
        <p class="mt-2 text-sm leading-7 text-stone-300">اختار نوع الحساب وابدأ برقم موبايل مصري وكلمة سر آمنة.</p>
    </div>

    <form wire:submit="register" class="space-y-5">
        <div>
            <label for="name" class="mb-2 block text-sm text-stone-200">الاسم</label>
            <input wire:model="name" id="name" type="text" class="w-full rounded-2xl border border-white/10 bg-black/20 px-4 py-3 text-white outline-none transition focus:border-orange-400/60" placeholder="اكتب اسمك" />
            @error('name') <p class="mt-2 text-sm text-rose-300">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="role" class="mb-2 block text-sm text-stone-200">نوع الحساب</label>
            <select wire:model="role" id="role" class="w-full rounded-2xl border border-white/10 bg-black/20 px-4 py-3 text-white outline-none transition focus:border-orange-400/60">
                <option value="{{ \App\Enums\UserRole::Client->value }}">عميل</option>
                <option value="{{ \App\Enums\UserRole::BarberOwner->value }}">صاحب صالون</option>
            </select>
            @error('role') <p class="mt-2 text-sm text-rose-300">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="phone" class="mb-2 block text-sm text-stone-200">رقم الموبايل</label>
            <input wire:model="phone" id="phone" type="text" inputmode="tel" class="w-full rounded-2xl border border-white/10 bg-black/20 px-4 py-3 text-white outline-none transition focus:border-orange-400/60" placeholder="01012345678" />
            @error('phone') <p class="mt-2 text-sm text-rose-300">{{ $message }}</p> @enderror
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="password" class="mb-2 block text-sm text-stone-200">كلمة السر</label>
                <input wire:model="password" id="password" type="password" class="w-full rounded-2xl border border-white/10 bg-black/20 px-4 py-3 text-white outline-none transition focus:border-orange-400/60" placeholder="8 حروف أو أكتر" />
                @error('password') <p class="mt-2 text-sm text-rose-300">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="mb-2 block text-sm text-stone-200">تأكيد كلمة السر</label>
                <input wire:model="password_confirmation" id="password_confirmation" type="password" class="w-full rounded-2xl border border-white/10 bg-black/20 px-4 py-3 text-white outline-none transition focus:border-orange-400/60" placeholder="اكتبها تاني" />
            </div>
        </div>

        <button type="submit" class="w-full rounded-2xl bg-orange-500 px-5 py-3 text-sm font-semibold text-stone-950 transition hover:bg-orange-400">كمّل التسجيل</button>
    </form>

    <p class="mt-6 text-center text-sm text-stone-300">عندك حساب بالفعل؟ <a href="{{ route('login') }}" class="font-medium text-orange-300 hover:text-orange-200">سجّل دخول</a></p>
</section>
