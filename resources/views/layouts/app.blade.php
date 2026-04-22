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

    <!-- Security -->
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <!-- Preconnect for Performance -->
    <link rel="preconnect" href="https://www.googletagmanager.com">
    <link rel="preconnect" href="https://www.google-analytics.com">

    <!-- Dynamic SEO Tags -->
    <title>
        @hasSection('title')
            @yield('title') | {{ config('app.name', 'BanhaFade') }}
        @else
            {{ config('app.name', 'BanhaFade') }} | الحلاقة بطريقة مختلفة
        @endif
    </title>
    <meta name="description" content="@yield('meta_description', 'BanhaFade - احجز موعد حلاقة سهل وسريع مع أفضل الحلاقين والصالونات في مصر. تطبيق محترف 100% مصري.')">
    <meta name="keywords" content="@yield('meta_keywords', 'حلاقين بنها, صالونات حلاقة في بنها, حجز موعد حلاقة بنها, أفضل حلاق في بنها, كوافير رجالي بنها, BanhaFade, بنها فيد, حلاقة رجال بنها, صالون حلاقة')">
    <meta name="author" content="BanhaFade Team">
    <meta name="robots" content="@yield('robots', 'index, follow')">
    <link rel="canonical" href="@yield('canonical', request()->fullUrl())">

    <!-- OG Meta Tags -->
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ request()->fullUrl() }}">
    <meta property="og:title" content="@yield('og_title', 'BanhaFade - الحلاقة بطريقة مختلفة')">
    <meta property="og:description" content="@yield('og_description', 'احجز موعد حلاقة مع أفضل الحلاقين في مصر بسهولة وسرعة')">
    <meta property="og:image" content="@yield('og_image', asset('icons/og-image.png'))">
    <meta property="og:locale" content="ar_EG">

    <!-- Twitter Cards -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="{{ request()->fullUrl() }}">
    <meta name="twitter:title" content="@yield('og_title', 'BanhaFade - احجز حلاقتك الآن')">
    <meta name="twitter:description" content="@yield('og_description', 'احجز موعد حلاقة مع أفضل الحلاقين في مصر بسهولة وسرعة')">
    <meta name="twitter:image" content="@yield('og_image', asset('icons/og-image.png'))">

    <!-- PWA Manifest & Icons -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-title" content="BanhaFade" />
    <link rel="icon" type="image/png" href="{{ asset('icons/favicon-96x96.png') }}" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('icons/favicon.svg') }}" />
    <link rel="shortcut icon" href="{{ asset('icons/favicon.ico') }}" />
    <link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <script>
        // Apply dark mode before rendering to prevent FOUC
        if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia(
                '(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        // Apply saved accent color
        (function() {
            const saved = localStorage.getItem('banhafade_accent');
            if (saved) {
                document.documentElement.style.setProperty('--color-banhafade-accent', saved);
            }
        })();
    </script>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-KDJMFY00DP"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-KDJMFY00DP');
    </script>

    <!-- Structured Data (JSON-LD) -->
    @hasSection('schema')
        @yield('schema')
    @else
        <script type="application/ld+json">
        {
          "&#64;context": "https://schema.org",
          "&#64;type": "SoftwareApplication",
          "name": "BanhaFade",
          "operatingSystem": "Web, iOS, Android",
          "applicationCategory": "LifestyleApplication",
          "description": "منصة حجز مواعيد صالونات الحلاقة في مصر",
          "offers": {
            "&#64;type": "Offer",
            "price": "0",
            "priceCurrency": "EGP"
          }
        }
        </script>
    @endif
</head>

<body data-route="{{ Route::currentRouteName() }}"
    class="bg-[#f2f2f7] dark:bg-[#000000] font-tajawal antialiased text-gray-900 dark:text-gray-100 flex justify-center min-h-screen selection:bg-banhafade-accent/30">
    <div class="w-full min-h-screen">
        <!-- Verification Reminder (Soft Verification) -->
        <x-verification-reminder />

        <!-- Main Content Container -->
        <main
            class="w-full max-w-md mx-auto relative min-h-screen pb-32 pt-6 px-4 shadow-2xl bg-white/30 dark:bg-white/5">

            <livewire:global-search />

            {{ $slot }}
        </main>

        <!-- Bottom Navigation -->
        @if (!request()->routeIs('login', 'register', 'verification.notice', 'password.request', 'password.reset'))
            <x-bottom-nav :hide-bottom-nav="$hideBottomNav ?? false" />
        @endif

        <!-- Global Interactive Components -->
        <x-toast />
        <x-ios-alert />
    </div>

    @livewireScripts

    {{-- Session Toast Bridge --}}
    @if (session()->has('toast'))
        <script>
            document.addEventListener('livewire:navigated', () => {
                setTimeout(() => {
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: @json(session('toast'))
                    }));
                }, 100);
            }, {
                once: true
            });
        </script>
    @endif

    <script>
        // Global app state (Moved to end of body to prevent render blocking)
        window.BanhaFade = {
            isAuthenticated: @json(auth()->check()),
            currentRoute: @json(Route::currentRouteName()),
            hasCompletedOnboarding: @json(auth()->check() ? auth()->user()->is_onboarded : false)
        };

        // ── Alpine component stores ────────────────────────────────────────────
        if (!window._banhafadeAlpineInitDone) {
            window._banhafadeAlpineInitDone = true;

            document.addEventListener('alpine:init', () => {
                Alpine.store('nav', {
                    hidden: false,
                    currentRoute: @json(Route::currentRouteName())
                });
            });
        }

        // ── Bottom nav bridge ──────────────────────────────────────────────────
        // Listens for CustomEvents dispatched above and syncs the Alpine store.
        if (!window._banhafadeNavListenersAdded) {
            window._banhafadeNavListenersAdded = true;
            window.addEventListener('hide-bottom-nav', () => {
                if (window.Alpine) Alpine.store('nav').hidden = true;
            });
            window.addEventListener('show-bottom-nav', () => {
                if (window.Alpine) Alpine.store('nav').hidden = false;
            });
        }

        // ── Livewire Global Error Handler ──────────────────────────────────────
        document.addEventListener('livewire:init', () => {
            Livewire.interceptRequest(({
                onError,
                onFailure
            }) => {
                onError(({
                    response,
                    preventDefault
                }) => {
                    preventDefault(); // Stops the scary default modal

                    let message = 'حدث خطأ أثناء معالجة طلبك.';

                    if (response.status === 419) {
                        message = 'انتهت صلاحية الجلسة، يرجى تحديث الصفحة.';
                    } else if (response.status === 404) {
                        message = 'لم يتم العثور على العنصر المطلوب.';
                    } else if (response.status === 500) {
                        message = 'خطأ في الخادم، يرجى المحاولة لاحقاً.';
                    } else if (response.status === 403) {
                        message = 'غير مصرح لك بإجراء هذه العملية.';
                    } else if (response.status === 401) {
                        message = 'يرجى تسجيل الدخول أولاً.';
                    } else if (response.status === 422) {
                        message = 'بعض البيانات المدخلة غير صحيحة.';
                    }

                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: {
                            type: 'error',
                            message: message
                        }
                    }));
                });

                onFailure(({
                    preventDefault
                }) => {
                    preventDefault(); // Network error (offline usually)
                    window.dispatchEvent(new CustomEvent('toast', {
                        detail: {
                            type: 'error',
                            message: 'خطأ في الاتصال بالإنترنت. يرجى المحاولة مجدداً.'
                        }
                    }));
                });
            });
        });
    </script>

    <script>
        // Re-apply dark mode, accent color, and update route state on Livewire navigation
        function onNavigate() {
            // Update Dark Mode
            if (localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia(
                    '(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }

            // Re-apply Accent Color
            const savedAccent = localStorage.getItem('banhafade_accent');
            if (savedAccent) {
                document.documentElement.style.setProperty('--color-banhafade-accent', savedAccent);
            }

            // Update Route Info
            const currentRoute = document.body.dataset.route || '';
            if (window.BanhaFade) {
                window.BanhaFade.currentRoute = currentRoute;
            }
            if (window.Alpine && Alpine.store('nav')) {
                Alpine.store('nav').currentRoute = currentRoute;
            }
        }

        if (!window._banhafadeNavigateListenerAdded) {
            window._banhafadeNavigateListenerAdded = true;
            document.addEventListener('livewire:navigated', onNavigate);
        }
    </script>
    @stack('scripts')
</body>

</html>
