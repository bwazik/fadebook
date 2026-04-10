<?php
declare(strict_types=1);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" x-data="{ 
    darkMode: localStorage.getItem('darkMode') === 'true' || (!('darkMode' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)
}" :class="{ 'dark': darkMode }">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
        <meta name="theme-color" content="#0f172a" media="(prefers-color-scheme: dark)">

        <title>{{ config('app.name', 'FadeBook') }}</title>

        <!-- PWA Manifest -->
        <link rel="manifest" href="/manifest.json">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800;900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 selection:bg-fadebook-accent selection:text-white pb-[calc(80px+var(--safe-area-bottom))]">
        <div class="min-h-screen">
            <!-- Verification Reminder (Soft Verification) -->
            <x-verification-reminder />

            <!-- Main Content -->
            <main class="max-w-md mx-auto min-h-screen px-4 pt-[var(--safe-area-top)]">
                {{ $slot }}
            </main>

            <!-- Bottom Navigation -->
            @if(!request()->routeIs('login', 'register', 'verification.notice', 'password.request', 'password.reset'))
                <x-bottom-nav />
            @endif

            <!-- Toast Messages -->
            <x-toast />
        </div>

        @livewireScripts
        <script>
            // Register Service Worker for PWA
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js');
                });
            }
        </script>
    </body>
</html>
