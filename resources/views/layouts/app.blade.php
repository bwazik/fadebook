<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="theme-color" content="#f2f2f7" media="(prefers-color-scheme: light)">
        <meta name="theme-color" content="#000000" media="(prefers-color-scheme: dark)">

        <title>{{ config('app.name', 'FadeBook') }} | احجز جلسة حلاقة اليوم</title>
        <meta name="description" content="FadeBook - احجز موعد حلاقة سهل وسريع مع أفضل الحلاقين والصالونات في مصر. تطبيق محترف 100% مصري.">
        <meta name="keywords" content="حلاقة, صالون حلاقة, حجز موعد, حلاق, FadeBook, حلاقين مصر, صالون, حلاقة رجال">
        <meta name="author" content="FadeBook Team">
        <meta name="robots" content="index, follow">
        <link rel="canonical" href="{{ url()->current() }}">

        <!-- OG Meta Tags -->
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="FadeBook - احجز حلاقتك الآن">
        <meta property="og:description" content="احجز موعد حلاقة مع أفضل الحلاقين في مصر بسهولة وسرعة">
        <meta property="og:image" content="{{ asset('icons/og-image.png') }}">
        <meta property="og:locale" content="ar_EG">

        <!-- Twitter Cards -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:url" content="{{ url()->current() }}">
        <meta name="twitter:title" content="FadeBook - احجز حلاقتك الآن">
        <meta name="twitter:description" content="احجز موعد حلاقة مع أفضل الحلاقين في مصر بسهولة وسرعة">
        <meta name="twitter:image" content="{{ asset('icons/og-image.png') }}">

        <!-- PWA Manifest -->
        <link rel="manifest" href="/manifest.json">
        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-title" content="FadeBook" />

        <!-- Favicon Setup -->
        <link rel="icon" type="image/png" href="{{ asset('icons/favicon-96x96.png') }}" sizes="96x96" />
        <link rel="icon" type="image/svg+xml" href="{{ asset('icons/favicon.svg') }}" />
        <link rel="shortcut icon" href="{{ asset('icons/favicon.ico') }}" />
        <link rel="apple-touch-icon" href="{{ asset('icons/icon-192x192.png') }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles

        <script>
            // Apply dark mode before rendering
            if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>

        <script>
            // Global app state
            window.FadeBook = {
                isAuthenticated: @json(auth()->check()),
                currentRoute: @json(Route::currentRouteName()),
                hasCompletedOnboarding: @json(auth()->check() ? auth()->user()->is_onboarded : false)
            };

            // Apply saved accent color
            (function() {
                const saved = localStorage.getItem('fadebook_accent');
                if (saved) {
                    document.documentElement.style.setProperty('--color-fadebook-accent', saved);
                }
            })();
        </script>
    </head>
    <body data-route="{{ Route::currentRouteName() }}" class="bg-[#f2f2f7] dark:bg-[#000000] font-tajawal antialiased text-gray-900 dark:text-gray-100 flex justify-center min-h-screen selection:bg-fadebook-accent/30">
        <div class="w-full min-h-screen">
            <!-- Verification Reminder (Soft Verification) -->
            <x-verification-reminder />

            <!-- Main Content Container -->
            <main class="w-full max-w-md mx-auto relative min-h-screen pb-32 pt-6 px-4 shadow-2xl bg-white/30 dark:bg-white/5">
                <!-- Header -->
                {{-- Global Header - Commented out per user request --}}
                {{--
                <header class="sticky top-0 z-40 -mx-4 -mt-6 px-5 py-2.5 flex items-center gap-4 bg-white/80 dark:bg-[#1c1c1e]/80 backdrop-blur-2xl border-b border-black/5 dark:border-white/10 transition-colors duration-300">
                    <div class="font-black text-2xl tracking-tighter drop-shadow-sm">Fade<span class="text-fadebook-accent">Book</span></div>
                    <div class="flex-1 min-w-0 flex items-center gap-3 justify-end">
                        <!-- Theme Toggle moved to Profile -->
                        <button x-data="{
                                isDark: document.documentElement.classList.contains('dark'),
                                toggle() {
                                    this.isDark = !this.isDark;
                                    document.documentElement.classList.toggle('dark', this.isDark);
                                    localStorage.setItem('darkMode', this.isDark);
                                }
                            }"
                            @click="toggle()"
                            class="p-2 rounded-full hover:bg-black/5 dark:hover:bg-white/10 liquid-button">
                            <svg x-show="isDark" x-cloak class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <svg x-show="!isDark" x-cloak class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </button>

                        <!-- Notifications moved to Profile -->
                        @auth
                            <button class="p-2 rounded-full hover:bg-black/5 dark:hover:bg-white/10 relative liquid-button">
                                <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0018 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                            </button>
                        @endauth
                    </div>
                </header>
                --}}

                {{ $slot }}
            </main>

            <!-- Bottom Navigation -->
            @if(!request()->routeIs('login', 'register', 'verification.notice', 'password.request', 'password.reset'))
                <x-bottom-nav />
            @endif

            <!-- Global Interactive Components -->
            <x-toast />
            <x-ios-alert />
        </div>

        @livewireScripts

        {{-- Session Toast Bridge --}}
        @if(session()->has('toast'))
            <script>
                document.addEventListener('livewire:navigated', () => {
                    setTimeout(() => {
                        window.dispatchEvent(new CustomEvent('toast', {
                            detail: @json(session('toast'))
                        }));
                    }, 100);
                }, { once: true });
            </script>
        @endif

        <script>
            // Capture PWA install prompt
            window.deferredPwaPrompt = null;
            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                window.deferredPwaPrompt = e;
                window.dispatchEvent(new Event('pwa-ready'));
            });

            document.addEventListener('alpine:init', () => {
                Alpine.store('nav', {
                    hidden: false,
                    currentRoute: @json(Route::currentRouteName())
                });

                // PWA Prompt Alpine Data
                Alpine.data('pwaPrompt', () => ({
                    deferredPrompt: null,
                    init() {
                        const handlePrompt = () => {
                            this.deferredPrompt = window.deferredPwaPrompt;
                            if (!this.deferredPrompt) return;

                            const dismissedAt = localStorage.getItem('fadebook_pwa_dismissed');
                            const now = new Date().getTime();
                            const threeDays = 3 * 24 * 60 * 60 * 1000;

                            if (!dismissedAt || (now - dismissedAt > threeDays)) {
                                setTimeout(() => {
                                    window.dispatchEvent(new CustomEvent('open-pwa-modal'));
                                }, 2000);
                            }
                        };

                        if (window.deferredPwaPrompt) {
                            handlePrompt();
                        } else {
                            window.addEventListener('pwa-ready', handlePrompt);
                        }
                    },
                    installPwa() {
                        if (this.deferredPrompt) {
                            this.deferredPrompt.prompt();
                            this.deferredPrompt.userChoice.then((choiceResult) => {
                                if (choiceResult.outcome === 'accepted') {
                                    console.log('User accepted PWA install');
                                }
                                this.deferredPrompt = null;
                                window.dispatchEvent(new CustomEvent('close-pwa-modal'));
                            });
                        }
                    },
                    dismissPwa() {
                        localStorage.setItem('fadebook_pwa_dismissed', new Date().getTime());
                        window.dispatchEvent(new CustomEvent('close-pwa-modal'));
                    }
                }));

                // Notification Prompt Alpine Data
                Alpine.data('notificationPrompt', () => ({
                    init() {
                        if (!window.FadeBook.isAuthenticated) return;

                        setTimeout(() => {
                            if (Notification.permission === 'default') {
                                const dismissedAt = localStorage.getItem('fadebook_notification_dismissed');
                                const now = new Date().getTime();
                                const week = 7 * 24 * 60 * 60 * 1000;

                                if (!dismissedAt || (now - dismissedAt > week)) {
                                    window.dispatchEvent(new CustomEvent('open-notification-modal'));
                                }
                            }
                        }, 5000);
                    },
                    async enable() {
                        Notification.requestPermission();
                        window.dispatchEvent(new CustomEvent('close-notification-modal'));
                    },
                    dismiss() {
                        localStorage.setItem('fadebook_notification_dismissed', new Date().getTime());
                        window.dispatchEvent(new CustomEvent('close-notification-modal'));
                    }
                }));
            });

            // Also listen on document for Livewire dispatched events
            document.addEventListener('hide-bottom-nav', () => {
                if (window.Alpine) Alpine.store('nav').hidden = true;
            });
            document.addEventListener('show-bottom-nav', () => {
                if (window.Alpine) Alpine.store('nav').hidden = false;
            });
        </script>

        <!-- PWA Install Modal -->
        <div x-data="pwaPrompt">
            <div x-data="{ open: false }" x-on:open-pwa-modal.window="open = true; $dispatch('hide-bottom-nav')"
                x-on:close-pwa-modal.window="open = false; $dispatch('show-bottom-nav')" x-show="open"
                style="display: none;" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-out duration-300" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-[60] bg-black/40 backdrop-blur-md flex items-end justify-center"
                @click.self="dismissPwa()">

                <div x-show="open" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
                    x-transition:leave="transition ease-out duration-300" x-transition:leave-start="translate-y-0"
                    x-transition:leave-end="translate-y-full"
                    class="bg-white/80 dark:bg-[#1c1c1e]/80 backdrop-blur-3xl border-t border-white/50 dark:border-white/10 rounded-t-[2rem] w-full max-w-md shadow-2xl relative"
                    @click.stop>

                    <div class="flex justify-center pt-4 pb-2">
                        <div class="w-10 h-1 rounded-full bg-gray-300 dark:bg-white/20"></div>
                    </div>

                    <div class="p-6 pt-2 pb-[calc(1.5rem+env(safe-area-inset-bottom))] text-center">
                        <img src="{{ asset('icons/icon-192x192.png') }}" alt="FadeBook Icon"
                            class="w-20 h-20 mx-auto rounded-3xl shadow-xl border border-black/5 dark:border-white/10 mb-5">
                        <h3 class="font-bold text-xl text-gray-900 dark:text-white mb-2">نزل تطبيق FadeBook 📱</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 leading-relaxed px-2">
                            تجربة أسرع، إشعارات حية، ومساحة أقل من 1 ميجا!
                        </p>
                        <div class="flex flex-col gap-3">
                            <button @click="installPwa()"
                                class="w-full py-3.5 rounded-2xl bg-fadebook-accent text-white font-bold text-sm active:scale-95 transition-all shadow-md cursor-pointer">
                                تثبيت الآن
                            </button>
                            <button @click="dismissPwa()"
                                class="w-full py-3.5 rounded-2xl bg-black/5 dark:bg-white/10 text-gray-700 dark:text-white/70 font-bold text-sm active:scale-95 transition-all cursor-pointer">
                                ليس الآن
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notification Permission Modal -->
        <div x-data="notificationPrompt">
            <div x-data="{ open: false }" x-on:open-notification-modal.window="open = true; $dispatch('hide-bottom-nav')"
                x-on:close-notification-modal.window="open = false; $dispatch('show-bottom-nav')" x-show="open"
                style="display: none;" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-out duration-300" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 z-[60] bg-black/40 backdrop-blur-md flex items-end justify-center"
                @click.self="dismiss()">

                <div x-show="open" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
                    x-transition:leave="transition ease-out duration-300" x-transition:leave-start="translate-y-0"
                    x-transition:leave-end="translate-y-full"
                    class="bg-white/80 dark:bg-[#1c1c1e]/80 backdrop-blur-3xl border-t border-white/50 dark:border-white/10 rounded-t-[2rem] w-full max-w-md shadow-2xl relative"
                    @click.stop>

                    <div class="flex justify-center pt-4 pb-2">
                        <div class="w-10 h-1 rounded-full bg-gray-300 dark:bg-white/20"></div>
                    </div>

                    <div class="p-6 pt-2 pb-[calc(1.5rem+env(safe-area-inset-bottom))] text-center">
                        <div class="w-20 h-20 mx-auto rounded-3xl bg-fadebook-accent/15 flex items-center justify-center text-fadebook-accent text-4xl mb-5 shadow-xl border border-fadebook-accent/10">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-10">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-xl text-gray-900 dark:text-white mb-2">خليك أول واحد يعرف كل جديد! 🔔</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-6 leading-relaxed px-2">
                            فعل الإشعارات عشان يوصلك تنبيهات فورية بالحجوزات الجديدة والرسائل وأهم التحديثات.
                        </p>
                        <div class="flex flex-col gap-3">
                            <button @click="enable()"
                                class="w-full py-3.5 rounded-2xl bg-fadebook-accent text-white font-bold text-sm active:scale-95 transition-all shadow-md cursor-pointer">
                                تفعيل الآن
                            </button>
                            <button @click="dismiss()"
                                class="w-full py-3.5 rounded-2xl bg-black/5 dark:bg-white/10 text-gray-700 dark:text-white/70 font-bold text-sm active:scale-95 transition-all cursor-pointer">
                                ليس الآن
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Service Worker Registration -->
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js');
                });
            }

            // Re-apply dark mode and update route state on Livewire navigation
            function onNavigate() {
                // Update Dark Mode
                if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }

                // Update Route Info
                if (window.FadeBook) {
                    window.FadeBook.currentRoute = document.body.dataset.route || '';
                }
                if (window.Alpine) {
                    Alpine.store('nav').currentRoute = document.body.dataset.route || '';
                }
            }
            document.addEventListener('livewire:navigated', onNavigate);
        </script>
        @stack('scripts')
    </body>
</html>
