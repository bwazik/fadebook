{{-- Bottom Navigation Bar --}}
<nav
    class="bottom-nav"
    x-data="{ hidden: false }"
    @hide-bottom-nav.window="hidden = true"
    @show-bottom-nav.window="hidden = false"
    x-show="!hidden"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="translate-y-full"
    x-transition:enter-end="translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="translate-y-0"
    x-transition:leave-end="translate-y-full"
>
    <div class="flex justify-around items-center h-16 max-w-lg mx-auto px-2">

        {{-- Home --}}
        <a href="{{ route('home') }}" class="flex flex-col items-center gap-1 px-4 py-2 group">
            <svg class="w-6 h-6 transition-colors {{ request()->routeIs('home') ? 'text-[--color-fadebook-accent]' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-white/60' }}" fill="{{ request()->routeIs('home') ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955a1.126 1.126 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
            </svg>
            <span class="text-[10px] font-medium {{ request()->routeIs('home') ? 'text-[--color-fadebook-accent]' : 'text-gray-400' }}">{{ __('messages.nav_home') }}</span>
        </a>

        {{-- Bookings --}}
        <a href="{{ route('bookings') }}" class="flex flex-col items-center gap-1 px-4 py-2 group">
            <svg class="w-6 h-6 transition-colors {{ request()->routeIs('bookings*') ? 'text-[--color-fadebook-accent]' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-white/60' }}" fill="{{ request()->routeIs('bookings*') ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
            </svg>
            <span class="text-[10px] font-medium {{ request()->routeIs('bookings*') ? 'text-[--color-fadebook-accent]' : 'text-gray-400' }}">{{ __('messages.nav_bookings') }}</span>
        </a>

        {{-- Search --}}
        <a href="{{ route('search') }}" class="flex flex-col items-center gap-1 px-4 py-2 group">
            <svg class="w-6 h-6 transition-colors {{ request()->routeIs('search*') ? 'text-[--color-fadebook-accent]' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-white/60' }}" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 0z"/>
            </svg>
            <span class="text-[10px] font-medium {{ request()->routeIs('search*') ? 'text-[--color-fadebook-accent]' : 'text-gray-400' }}">{{ __('messages.nav_search') }}</span>
        </a>

        {{-- Profile --}}
        <a href="{{ route('search') }}" class="flex flex-col items-center gap-1 px-4 py-2 group">
            <svg class="w-6 h-6 transition-colors {{ request()->routeIs('search*') ? 'text-[--color-fadebook-accent]' : 'text-gray-400 group-hover:text-gray-600 dark:group-hover:text-white/60' }}" fill="{{ request()->routeIs('search*') ? 'currentColor' : 'none' }}" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
            </svg>
            <span class="text-[10px] font-medium {{ request()->routeIs('search*') ? 'text-[--color-fadebook-accent]' : 'text-gray-400' }}">{{ __('messages.nav_profile') }}</span>
        </a>

    </div>
</nav>
