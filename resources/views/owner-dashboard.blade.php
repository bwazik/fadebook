<x-layouts.app title="لوحة الصالون">
    <section class="rounded-[2rem] border border-emerald-400/20 bg-emerald-400/10 p-6 text-emerald-50 shadow-2xl shadow-black/20">
        <h1 class="text-2xl font-semibold">{{ $shop->name }}</h1>
        <p class="mt-3 text-sm leading-7 text-emerald-100/90">الصالون متفعل دلوقتي، وتقدر تعتبر الصفحة دي بداية لوحة الإدارة لحد ما يتوصل باقي النظام.</p>
        <dl class="mt-6 grid gap-4 sm:grid-cols-2">
            <div class="rounded-2xl border border-white/10 bg-black/15 p-4">
                <dt class="text-xs text-emerald-100/70">المنطقة</dt>
                <dd class="mt-2 font-medium">{{ $shop->area->name }}</dd>
            </div>
            <div class="rounded-2xl border border-white/10 bg-black/15 p-4">
                <dt class="text-xs text-emerald-100/70">رقم الصالون</dt>
                <dd class="mt-2 font-medium">{{ $shop->phone }}</dd>
            </div>
        </dl>
    </section>
</x-layouts.app>
