<x-layouts.app title="السوق">
    <section class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
        <div class="rounded-[2rem] border border-white/10 bg-white/8 p-6 shadow-2xl shadow-black/20 backdrop-blur sm:p-8">
            <span class="inline-flex rounded-full border border-orange-400/30 bg-orange-400/10 px-3 py-1 text-xs font-medium text-orange-200">جاهز للحجز</span>
            <h1 class="mt-4 text-3xl font-semibold leading-tight text-white sm:text-5xl">احجز أقرب صالون ليك بسهولة ومن غير لف كتير</h1>
            <p class="mt-4 max-w-2xl text-sm leading-7 text-stone-300 sm:text-base">ابدأ كعميل لو عايز تتصفح وتحجز، أو سجّل كصاحب صالون لو عايز تضيف مكانك وتوصّل لعملاء أكتر.</p>

            <div class="mt-8 flex flex-wrap gap-3">
                <a href="{{ route('register') }}" class="rounded-full bg-orange-500 px-5 py-3 text-sm font-semibold text-stone-950 transition hover:bg-orange-400">سجّل حساب جديد</a>
                <a href="{{ route('login') }}" class="rounded-full border border-white/10 px-5 py-3 text-sm font-semibold text-stone-100 transition hover:border-orange-400/60">عندي حساب بالفعل</a>
            </div>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-1">
            <div class="rounded-[2rem] border border-white/10 bg-black/20 p-5">
                <h2 class="text-lg font-semibold text-white">لأصحاب الصالونات</h2>
                <p class="mt-2 text-sm leading-7 text-stone-300">أضف اسم الصالون، المنطقة، المواعيد، والخدمات في خطوات بسيطة وبعدها الطلب يروح للمراجعة.</p>
            </div>
            <div class="rounded-[2rem] border border-white/10 bg-black/20 p-5">
                <h2 class="text-lg font-semibold text-white">أمان أفضل</h2>
                <p class="mt-2 text-sm leading-7 text-stone-300">الدخول برقم الموبايل المصري، وحد للمحاولات الغلط، واسترجاع كلمة السر بكود واتساب.</p>
            </div>
        </div>
    </section>
</x-layouts.app>
