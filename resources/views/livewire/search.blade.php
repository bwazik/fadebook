<div>
    <!-- Sticky Search Header -->
    <div class="sticky top-0 z-40 px-4 pt-[calc(1rem+var(--safe-area-top))] pb-4 liquid-glass">
        <div class="flex items-center gap-3">
            <a href="{{ route('home') }}" wire:navigate class="p-2 -mr-2 text-gray-600 rounded-full dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </a>
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </div>
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="query"
                    placeholder="بتدور على محل إيه؟" 
                    autofocus
                    class="block w-full py-3 pr-10 pl-4 text-gray-900 bg-white border border-gray-200 rounded-2xl dark:bg-[#1c1c1e] dark:border-gray-700 dark:text-white focus:ring-fadebook-accent focus:border-fadebook-accent transition-colors shadow-sm"
                >
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="px-0 py-6 pb-24 space-y-8">
        @if(strlen($query) < 2)
            <div class="text-center py-12 px-4">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-gray-100 dark:bg-white/5 mb-6">
                    <svg class="w-10 h-10 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">دور على صالون حلاقة</h3>
                <p class="text-gray-500 dark:text-gray-400">اكتب اسم المحل أو المنطقة عشان تدور</p>
            </div>
        @elseif(empty($results))
            <div class="text-center py-12 px-4">
                <x-empty-state 
                    title="مفيش نتائج" 
                    description="ملقيناش محلات بالاسم ده، جرب اسم تاني." 
                >
                    <x-slot name="icon">
                        <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                    </x-slot>
                </x-empty-state>
            </div>
        @else
            <div class="flex flex-col gap-8 px-4">
                @foreach($results as $shop)
                    <div wire:key="search-shop-{{ $shop->id }}" class="group relative">
                        <x-glass-card class="overflow-hidden border-0 !p-0 shadow-xl dark:shadow-2xl/20 hover:shadow-2xl transition-all duration-500 rounded-[2rem]">
                            <a href="{{ route('shop.show', ['areaSlug' => $shop->area->slug, 'shopSlug' => $shop->slug]) }}" wire:navigate class="block">
                                <!-- Banner Section -->
                                <div class="relative h-48 w-full overflow-hidden">
                                    @php
                                        $banner = $shop->images->where('collection', 'banner')->first();
                                        $logo = $shop->images->where('collection', 'logo')->first();
                                    @endphp
                                    @if($banner)
                                        <img src="{{ Storage::url($banner->path) }}" alt="{{ $shop->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-800 dark:to-gray-900 group-hover:scale-110 transition-transform duration-700"></div>
                                    @endif
                                    
                                    <!-- Online Status Floating Badge -->
                                    <div class="absolute top-4 left-4">
                                        @if($shop->is_online)
                                            <x-badge color="success" class="liquid-glass border-0 rounded-xl shadow-lg !text-[10px] py-1 px-3">متاح الآن</x-badge>
                                        @else
                                            <x-badge color="gray" class="liquid-glass border-0 rounded-xl shadow-lg !text-[10px] py-1 px-3">مغلق</x-badge>
                                        @endif
                                    </div>
                                </div>

                                <!-- Content Section -->
                                <div class="p-6 pt-10 relative">
                                    <!-- Floating Logo -->
                                    <div class="absolute -top-10 right-6 w-20 h-20 rounded-3xl p-1 bg-white/80 dark:bg-black/80 backdrop-blur-xl shadow-2xl border border-white/50 dark:border-white/10 overflow-hidden transform group-hover:-translate-y-1 transition-transform duration-300">
                                        @if($logo)
                                            <img src="{{ Storage::url($logo->path) }}" alt="{{ $shop->name }}" class="w-full h-full object-cover rounded-2xl">
                                        @else
                                            <div class="w-full h-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center rounded-2xl">
                                                <span class="text-2xl font-black text-gray-400 uppercase">{{ mb_substr($shop->name, 0, 1) }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex flex-col gap-4">
                                        <div class="flex flex-col gap-1">
                                            <div class="flex items-center justify-between">
                                                <h3 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">{{ $shop->name }}</h3>
                                                <div class="flex items-center gap-1.5 bg-fadebook-accent/10 sm:bg-transparent px-2 py-1 rounded-xl">
                                                    <svg class="w-4 h-4 text-fadebook-accent fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                    <span class="text-sm font-black text-fadebook-accent">{{ number_format($shop->average_rating, 1) }}</span>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 text-gray-500 dark:text-gray-400 font-bold text-sm">
                                                <svg class="w-4 h-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                                <span>{{ $shop->area->name }}</span>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-3 gap-4 py-4 border-y border-black/5 dark:border-white/5">
                                            <div class="flex flex-col items-center">
                                                <span class="text-xs font-bold text-gray-400 grow text-center">المراجعات</span>
                                                <span class="text-lg font-black text-gray-900 dark:text-white">{{ $shop->total_reviews ?? 0 }}</span>
                                            </div>
                                            <div class="flex flex-col items-center border-x border-black/5 dark:border-white/5">
                                                <span class="text-xs font-bold text-gray-400 grow text-center">المشاهدات</span>
                                                <span class="text-lg font-black text-gray-900 dark:text-white">{{ $shop->total_views ?? 0 }}</span>
                                            </div>
                                            <div class="flex flex-col items-center">
                                                <span class="text-xs font-bold text-gray-400 grow text-center font-tajawal">الحلاقين</span>
                                                <span class="text-lg font-black text-gray-900 dark:text-white">{{ rand(2, 6) }}</span>
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-between gap-4 mt-2">
                                            <div class="flex -space-x-2 space-x-reverse overflow-hidden">
                                                @foreach($shop->barbers->take(3) as $barber)
                                                    <div class="inline-block h-8 w-8 rounded-full ring-2 ring-white dark:ring-black bg-gray-200 dark:bg-gray-800"></div>
                                                @endforeach
                                            </div>
                                            <x-ios-button class="!w-auto !py-2.5 !px-8 shadow-lg shadow-fadebook-accent/30 !rounded-[1.2rem] transform active:scale-95 transition-all">
                                                احجز الآن
                                            </x-ios-button>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </x-glass-card>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Bottom Nav -->
    <x-bottom-nav />
</div>
