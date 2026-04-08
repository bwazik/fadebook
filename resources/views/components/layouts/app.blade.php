<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>{{ $title ?? config('app.name', 'FadeBook') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-stone-950 text-stone-100 antialiased">
    <div class="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(249,115,22,0.22),_transparent_30%),linear-gradient(180deg,_#0c0a09_0%,_#1c1917_100%)] px-4 pb-[calc(env(safe-area-inset-bottom)+1.5rem)] pt-[calc(env(safe-area-inset-top)+1rem)]">
        <div class="mx-auto flex min-h-screen w-full max-w-6xl flex-col gap-6">
            <header class="rounded-3xl border border-white/10 bg-white/8 px-5 py-4 shadow-2xl shadow-black/20 backdrop-blur">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <a href="{{ route('marketplace') }}" class="text-lg font-semibold tracking-tight text-white">FadeBook</a>
                        <p class="text-sm text-stone-300">منصة حجز وحضور للصالونات الرجالي</p>
                    </div>

                    <nav class="flex flex-wrap items-center gap-2 text-sm">
                        <a href="{{ route('marketplace') }}" class="rounded-full border border-white/10 px-4 py-2 text-stone-200 transition hover:border-orange-400/60 hover:text-white">الرئيسية</a>

                        @auth
                            <a href="{{ route(auth()->user()->homeRouteName()) }}" class="rounded-full border border-white/10 px-4 py-2 text-stone-200 transition hover:border-orange-400/60 hover:text-white">لوحتي</a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="rounded-full bg-orange-500 px-4 py-2 font-medium text-stone-950 transition hover:bg-orange-400">تسجيل خروج</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="rounded-full border border-white/10 px-4 py-2 text-stone-200 transition hover:border-orange-400/60 hover:text-white">دخول</a>
                            <a href="{{ route('register') }}" class="rounded-full bg-orange-500 px-4 py-2 font-medium text-stone-950 transition hover:bg-orange-400">حساب جديد</a>
                        @endauth
                    </nav>
                </div>
            </header>

            @if (session('toast'))
                <div class="rounded-2xl border border-amber-400/30 bg-amber-400/10 px-4 py-3 text-sm text-amber-100">
                    {{ session('toast') }}
                </div>
            @endif

            @if (session('status'))
                <div class="rounded-2xl border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-100">
                    {{ session('status') }}
                </div>
            @endif

            <main class="flex-1">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>
