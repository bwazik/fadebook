<x-layout.app>
    <x-page-header title="الملف الشخصي" />

    <div class="mt-6 space-y-6">
        {{-- Profile Info Section --}}
        <x-glass-card class="flex flex-col items-center">
            <x-avatar :src="auth()->user()->image_path ? Storage::url(auth()->user()->image_path) : null" :name="auth()->user()->name" size="xl" />
            <h2 class="mt-4 text-xl font-bold">{{ auth()->user()->name }}</h2>
            <p class="text-sm text-gray-500 dark:text-white/40">{{ auth()->user()->phone }}</p>
        </x-glass-card>

        {{-- Accent Color Selection --}}
        <div>
            <x-section-header title="لون التطبيق" />
            <x-ios-input-group class="p-4">
                <x-accent-picker />
            </x-ios-input-group>
        </div>

        {{-- Account Settings --}}
        <div>
            <x-section-header title="الإعدادات بك" />
            <x-ios-input-group>
                <a href="#" class="flex items-center justify-between p-4 border-b border-black/5 dark:border-white/10 last:border-0 hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
                    <span class="text-sm font-medium">تعديل البيانات</span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <a href="#" class="flex items-center justify-between p-4 border-b border-black/5 dark:border-white/10 last:border-0 hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
                    <span class="text-sm font-medium">تغيير كلمة السر</span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
            </x-ios-input-group>
        </div>

        {{-- Logout --}}
        <div class="px-4">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-ios-button variant="danger" onclick="event.preventDefault(); this.closest('form').submit();">
                    تسجيل الخروج
                </x-ios-button>
            </form>
        </div>
    </div>
</x-layout.app>
