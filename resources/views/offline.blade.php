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
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-10 h-10 text-gray-400">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8.288 15.038a5.25 5.25 0 0 1 7.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 0 1 1.06 0Z" />
                </svg>
            </div>
            <h1 class="text-xl font-black text-gray-900 dark:text-white mb-2">{{ __('messages.offline_title') }}</h1>
            <p class="text-sm text-gray-500 dark:text-white/50 mb-6">{{ __('messages.offline_message') }}</p>
            <x-ios-button type="button" onclick="window.location.reload()">
                {{ __('messages.try_again') }}
            </x-ios-button>
        </x-glass-card>
    </div>
</body>

</html>
