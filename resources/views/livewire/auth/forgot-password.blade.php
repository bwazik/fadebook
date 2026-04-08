<section class="mx-auto max-w-xl rounded-[2rem] border border-white/10 bg-white/8 p-6 shadow-2xl shadow-black/20 backdrop-blur sm:p-8">
    <div class="mb-8">
        <h1 class="text-2xl font-semibold text-white">استرجاع كلمة السر</h1>
        <p class="mt-2 text-sm leading-7 text-stone-300">اكتب رقم موبايلك، ولو الحساب موجود هنبعتلك كود على واتساب فورًا.</p>
    </div>

    <form wire:submit="sendOtp" class="space-y-5">
        <div>
            <label for="phone" class="mb-2 block text-sm text-stone-200">رقم الموبايل</label>
            <input wire:model="phone" id="phone" type="text" inputmode="tel" class="w-full rounded-2xl border border-white/10 bg-black/20 px-4 py-3 text-white outline-none transition focus:border-orange-400/60" placeholder="01012345678" />
            @error('phone') <p class="mt-2 text-sm text-rose-300">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="w-full rounded-2xl bg-orange-500 px-5 py-3 text-sm font-semibold text-stone-950 transition hover:bg-orange-400">ابعت الكود</button>
    </form>
</section>
