<section class="mx-auto max-w-3xl rounded-[2rem] border border-sky-400/20 bg-sky-400/10 p-6 shadow-2xl shadow-black/20 sm:p-8">
    <span class="inline-flex rounded-full border border-sky-300/20 bg-sky-300/10 px-3 py-1 text-xs font-medium text-sky-100">{{ $shop->status->label() }}</span>
    <h1 class="mt-4 text-3xl font-semibold text-white">طلبك تحت المراجعة</h1>
    <p class="mt-3 text-sm leading-7 text-sky-50/90">استلمنا بيانات صالون <span class="font-semibold">{{ $shop->name }}</span>، وهنتواصل معاك أول ما المراجعة تخلص.</p>

    <div class="mt-8 grid gap-4 sm:grid-cols-2">
        <div class="rounded-2xl border border-white/10 bg-black/15 p-4">
            <p class="text-xs text-sky-100/70">المنطقة</p>
            <p class="mt-2 font-medium text-white">{{ $shop->area->name }}</p>
        </div>
        <div class="rounded-2xl border border-white/10 bg-black/15 p-4">
            <p class="text-xs text-sky-100/70">رقم الصالون</p>
            <p class="mt-2 font-medium text-white">{{ $shop->phone }}</p>
        </div>
    </div>
</section>
