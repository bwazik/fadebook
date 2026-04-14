<div class="pb-[calc(5rem+var(--safe-area-bottom)+64px)] relative min-h-screen pt-4 px-4">
    <!-- Header -->
    <div class="mb-6 mt-4">
        <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tighter">
            العملاء
        </h1>
        <p class="text-sm text-gray-500 font-bold mt-1">
            سجل عملاء المحل
        </p>
    </div>

    <!-- Search -->
    <div class="mb-6 relative">
        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
        </div>
        <input type="text" wire:model.live.debounce.300ms="search"
            class="block w-full py-3 pr-11 pl-4 text-gray-900 bg-white border border-gray-200 rounded-[1.2rem] text-sm focus:ring-fadebook-accent focus:border-fadebook-accent dark:bg-[#1c1c1e] dark:border-white/10 dark:placeholder-gray-500 dark:text-white text-right"
            placeholder="ابحث بالاسم أو الرقم...">
    </div>

    <!-- Clients List -->
    <div class="space-y-4">
        @forelse($this->clients as $client)
            <div class="liquid-glass rounded-[1.5rem] p-4 border border-white/20 shadow-sm flex items-center gap-4">
                <div class="shrink-0">
                    <div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-gray-800 flex items-center justify-center border border-black/5 dark:border-white/10 shadow-sm">
                        <span class="text-lg text-gray-400 font-black">{{ mb_substr($client->name, 0, 1) }}</span>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase leading-tight truncate mb-1">
                        {{ $client->name }}
                    </h3>
                    <p class="text-[10px] text-gray-500 font-bold uppercase tracking-widest truncate" dir="ltr">
                        {{ $client->phone }}
                    </p>
                </div>
                <div class="shrink-0 text-center">
                    <span class="block text-xl font-black text-fadebook-accent leading-none">{{ $client->total_visits }}</span>
                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">زيارة</span>
                </div>
            </div>
        @empty
            <x-empty-state 
                title="لا يوجد عملاء"
                description="لم يقم أي عميل بالحجز في محلك حتى الآن."
            >
                <x-slot name="icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 opacity-60">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                </x-slot>
            </x-empty-state>
        @endforelse
    </div>
</div>
