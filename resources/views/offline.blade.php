<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ __('messages.offline_title') }} — BanhaFade</title>
    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen bg-[#f2f2f7] dark:bg-black flex items-center justify-center p-6">
    <div class="w-full max-w-sm">
        <x-glass-card class="text-center">
            <div
                class="w-20 h-20 mx-auto mb-5 rounded-full bg-gray-100 dark:bg-white/10 flex items-center justify-center">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3 3l18 18M10.584 10.587a2 2 0 002.828 2.83M6.343 6.343A7.954 7.954 0 004 12c0 4.418 3.582 8 8 8a7.954 7.954 0 005.657-2.343M9.88 9.88A4.978 4.978 0 007 12c0 2.761 2.239 5 5 5a4.978 4.978 0 002.12-.88M15 12a3 3 0 00-3-3" />
                </svg>
            </div>
            <h1 class="text-xl font-black text-gray-900 dark:text-white mb-2">{{ __('messages.offline_title') }}</h1>
            <p class="text-sm text-gray-500 dark:text-white/50 mb-6">{{ __('messages.offline_message') }}</p>
            <button onclick="window.location.reload()"
                class="w-full py-3 rounded-2xl text-white font-bold text-sm transition-all active:scale-95"
                style="background-color: var(--color-banhafade-accent);">
                {{ __('messages.try_again') }}
            </button>
        </x-glass-card>
    </div>
</body>

</html>
