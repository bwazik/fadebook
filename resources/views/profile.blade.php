<x-layout.app>
    <x-page-header title="الملف الشخصي" />

    <div class="mt-6 space-y-6">
        {{-- Profile Info Section --}}
        <x-glass-card class="flex flex-col items-center">
            <x-avatar :src="auth()->user()->image_path ? Storage::url(auth()->user()->image_path) : null" :name="auth()->user()->name" size="xl" />
            <h2 class="mt-4 text-xl font-bold">{{ auth()->user()->name }}</h2>
            <p class="text-sm text-gray-500 dark:text-white/40">{{ auth()->user()->phone }}</p>
        </x-glass-card>

        {{-- Appearance Settings --}}
        <div>
            <x-section-header title="المظهر" />
            <x-ios-input-group>
                <div class="flex items-center justify-between p-4 border-b border-black/5 dark:border-white/10 last:border-0">
                    <div class="flex flex-col">
                        <span class="text-sm font-medium">الوضع الليلي</span>
                        <span class="text-xs text-gray-400">تغيير مظهر التطبيق</span>
                    </div>
                    <div x-data="{ 
                        isDark: document.documentElement.classList.contains('dark'),
                        toggle() {
                            this.isDark = !this.isDark;
                            document.documentElement.classList.toggle('dark', this.isDark);
                            localStorage.setItem('darkMode', this.isDark);
                        }
                    }">
                        <x-ios-toggle x-model="isDark" @click="toggle()" />
                    </div>
                </div>
                
                <div class="p-4 border-b border-black/5 dark:border-white/10 last:border-0">
                    <div class="flex flex-col gap-2">
                        <span class="text-sm font-medium">لون التطبيق</span>
                        <x-accent-picker />
                    </div>
                </div>
            </x-ios-input-group>
        </div>

        {{-- Notifications --}}
        <div>
            <x-section-header title="الإشعارات" />
            <x-ios-input-group>
                <a href="#" class="flex items-center justify-between p-4 hover:bg-black/5 dark:hover:bg-white/5 transition-colors">
                    <div class="flex flex-col">
                        <span class="text-sm font-medium">إعدادات الإشعارات</span>
                        <span class="text-xs text-gray-400">تحكم فيما يصلك من تنبيهات</span>
                    </div>
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
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
