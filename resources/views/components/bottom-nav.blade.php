{{-- iOS 26 Split Bottom Navigation — Two Separate Pills --}}

@php
    $user = auth()->user();
    $isOwner = $user && $user->role->value === \App\Enums\UserRole::BarberOwner->value;
    $isDashboard = request()->is('dashboard*');

    if ($isOwner && $isDashboard) {
        // Pure Owner Dashboard Nav
        $navItems = [
            [
                'route' => 'dashboard.home',
                'label' => 'الرئيسية',
                'icon' => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z',
            ],
            [
                'route' => 'dashboard.reservations',
                'label' => 'الحجوزات',
                'icon' => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5',
            ],
            [
                'route' => 'dashboard.settings',
                'label' => 'المحل',
                'icon' => 'M13.5 21v-7.5a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349M3.75 21V9.349m0 0a3.001 3.001 0 0 0 3.75-.615A2.993 2.993 0 0 0 9.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 0 0 2.25 1.016c.896 0 1.7-.393 2.25-1.015a3.001 3.001 0 0 0 3.75.614m-16.5 0a3.004 3.004 0 0 1-.621-4.72l1.189-1.19A1.5 1.5 0 0 1 5.378 3h13.243a1.5 1.5 0 0 1 1.06.44l1.19 1.189a3 3 0 0 1-.621 4.72M6.75 18h3.75a.75.75 0 0 0 .75-.75V13.5a.75.75 0 0 0-.75-.75H6.75a.75.75 0 0 0-.75.75v3.75c0 .414.336.75.75.75Z', // Example icon for settings
            ],
            [
                'route' => 'dashboard.financials',
                'label' => 'المالية',
                'icon' => 'M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
            ],
        ];
    } else {
        // Pure Client Nav
        $navItems = [
            [
                'route' => 'home',
                'label' => 'الرئيسية',
                'icon' => 'm7.848 8.25 1.536.887M7.848 8.25a3 3 0 1 1-5.196-3 3 3 0 0 1 5.196 3Zm1.536.887a2.165 2.165 0 0 1 1.083 1.839c.005.351.054.695.14 1.024M9.384 9.137l2.077 1.199M7.848 15.75l1.536-.887m-1.536.887a3 3 0 1 1-5.196 3 3 3 0 0 1 5.196-3Zm1.536-.887a2.165 2.165 0 0 0 1.083-1.838c.005-.352.054-.695.14-1.025m-1.223 2.863 2.077-1.199m0-3.328a4.323 4.323 0 0 1 2.068-1.379l5.325-1.628a4.5 4.5 0 0 1 2.48-.044l.803.215-7.794 4.5m-2.882-1.664A4.33 4.33 0 0 0 10.607 12m3.736 0 7.794 4.5-.802.215a4.5 4.5 0 0 1-2.48-.043l-5.326-1.629a4.324 4.324 0 0 1-2.068-1.379M14.343 12l-2.882 1.664',
            ],
            [
                'route' => 'bookings.index',
                'label' => 'حجوزاتي',
                'icon' => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5',
            ],
            [
                'route' => 'offers',
                'label' => 'العروض',
                'icon' => 'M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 1 0 9.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1 1 14.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z',
            ],
            [
                'route' => 'profile.index',
                'label' => 'حسابي',
                'icon' => 'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z',
            ],
        ];
    }
@endphp

{{-- Wrapper: dir=ltr so RTL doesn't reverse pill order --}}
<div class="fixed bottom-[calc(2rem+env(safe-area-inset-bottom))] left-1/2 -translate-x-1/2
            flex items-center gap-2.5 z-50
            transition-all duration-300 ease-out"
    dir="ltr" x-data
    :class="$store.nav.hidden ? 'opacity-0 translate-y-24 pointer-events-none' : 'opacity-100 translate-y-0'">

    {{-- ═══════════════════════════════ --}}
    {{-- LEFT PILL — 3 Navigation Tabs  --}}
    {{-- ═══════════════════════════════ --}}
    <nav x-data="navLeftPill()" dir="rtl" @touchstart="touchStart($event)" @touchmove.prevent="touchMove($event)"
        @touchend="touchEnd($event)"
        class="flex items-center p-1.5 rounded-[2rem] liquid-glass transform-gpu touch-none relative">

        {{-- Glass gradient overlay --}}
        <div
            class="absolute inset-0 rounded-[2rem] pointer-events-none
                    bg-gradient-to-b from-white/70 via-white/20 to-white/5
                    dark:from-white/10 dark:via-white/5 dark:to-transparent">
        </div>

        {{-- Animated active pill --}}
        <div x-ref="pill"
            class="absolute top-1.5 bottom-1.5 right-0
                    bg-white/40 dark:bg-white/10
                    backdrop-blur-md rounded-full pointer-events-none"
            style="will-change: transform, width;"></div>

        {{-- Tab items --}}
        @foreach ($navItems as $item)
            @php
                $routeBase = str_replace('.index', '', $item['route']);
            @endphp
            <a href="{{ route($item['route']) }}"
               id="tour-nav-{{ $item['route'] }}"
               @auth wire:navigate @endauth
               data-route="{{ $item['route'] }}"
               data-base-route="{{ $routeBase }}"
               class="flex flex-col items-center justify-center py-2 px-[1.1rem] relative z-10 liquid-transition whitespace-nowrap"
               :class="isActive('{{ $item['route'] }}') ? 'text-fadebook-accent drop-shadow-[0_0_8px_rgba(1,101,225,0.4)]' : 'text-gray-500 dark:text-gray-400'">

                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    :stroke-width="isActive('{{ $item['route'] }}') ? '2' : '1.5'" stroke="currentColor"
                    class="w-[22px] h-[22px] liquid-transition"
                    :class="isActive('{{ $item['route'] }}') ? 'scale-110' : 'scale-100'">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                </svg>
                <span class="text-[10px] font-semibold mt-0.5">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    {{-- ═══════════════════════════════ --}}
    {{-- RIGHT PILL — Search Icon Only  --}}
    {{-- ═══════════════════════════════ --}}
    <button type="button"
       class="flex items-center justify-center p-3 rounded-[2rem] liquid-glass liquid-button text-gray-500 dark:text-gray-400">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
            stroke-width="1.5" stroke="currentColor"
            class="w-[22px] h-[22px] liquid-transition scale-100">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 0z" />
        </svg>
    </button>

</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('navLeftPill', () => ({
                tabs: [],
                pill: null,
                isDragging: false,
                startX: 0,
                currentTranslateX: 0,
                initialTranslateX: 0,

                init() {
                    this.tabs = Array.from(this.$el.querySelectorAll('a[data-route]'));
                    this.pill = this.$refs.pill;

                    // Re-calculate on Livewire navigation
                    document.addEventListener('livewire:navigated', () => {
                        this.$nextTick(() => {
                            const activeTab = this.tabs.find(t => this.isActive(t.dataset.route)) || this.tabs[
                                0];
                            this.snapTo(activeTab, true);
                        });
                    });

                    setTimeout(() => {
                        const activeTab = this.tabs.find(t => this.isActive(t.dataset.route)) || this.tabs[0];
                        this.snapTo(activeTab, true);
                    }, 50);

                    window.addEventListener('resize', () => {
                        const activeTab = this.tabs.find(t => this.isActive(t.dataset.route)) || this.tabs[0];
                        this.snapTo(activeTab, true);
                    });
                },

                isActive(route) {
                    const currentRoute = Alpine.store('nav').currentRoute;
                    if (!currentRoute) return false;

                    // Hardcoded check for Bookings tab
                    if (route === 'bookings.index') {
                        return [
                            'bookings.index',
                            'booking.show',
                            'booking.create'
                        ].includes(currentRoute);
                    }

                    // Hardcoded check for Profile tab
                    if (route === 'profile.index') {
                        return [
                            'profile.index',
                            'profile.edit',
                            'profile.settings'
                        ].includes(currentRoute);
                    }

                    // Generic case: exact match or sub-route
                    const base = route.replace('.index', '');
                    return currentRoute === route || currentRoute.startsWith(base + '.');
                },

                snapTo(targetEl, instant = false) {
                    if (!targetEl || !this.pill) return;
                    const parentRect = this.$el.getBoundingClientRect();
                    const elRect = targetEl.getBoundingClientRect();

                    this.pill.style.transition = instant ?
                        'none' :
                        'transform 0.4s cubic-bezier(0.32,0.72,0,1), width 0.4s cubic-bezier(0.32,0.72,0,1)';

                    this.pill.style.width = `${elRect.width}px`;
                    const offset = (parentRect.right - elRect.right);
                    this.currentTranslateX = -offset;
                    this.initialTranslateX = -offset;
                    this.pill.style.transform = `translateX(${this.currentTranslateX}px)`;
                },

                touchStart(e) {
                    this.isDragging = true;
                    this.startX = e.touches[0].clientX;
                    if (this.pill) this.pill.style.transition = 'none';
                },

                touchMove(e) {
                    if (!this.isDragging || !this.pill) return;
                    const deltaX = e.touches[0].clientX - this.startX;
                    this.currentTranslateX = this.initialTranslateX + deltaX;

                    const padding = 6;
                    const maxRight = -padding;
                    const maxLeft = -(this.$el.getBoundingClientRect().width -
                        this.tabs[this.tabs.length - 1].getBoundingClientRect().width -
                        padding);
                    this.currentTranslateX = Math.min(maxRight, Math.max(maxLeft, this
                        .currentTranslateX));
                    this.pill.style.transform = `translateX(${this.currentTranslateX}px)`;
                },

                touchEnd(e) {
                    if (!this.isDragging) return;
                    this.isDragging = false;

                    const pillRect = this.pill.getBoundingClientRect();
                    const pillCenter = pillRect.left + (pillRect.width / 2);
                    let closestTab = this.tabs[0];
                    let minDistance = Infinity;

                    this.tabs.forEach(tab => {
                        const tabRect = tab.getBoundingClientRect();
                        const tabCenter = tabRect.left + (tabRect.width / 2);
                        const distance = Math.abs(tabCenter - pillCenter);
                        if (distance < minDistance) {
                            minDistance = distance;
                            closestTab = tab;
                        }
                    });

                    this.snapTo(closestTab, false);

                    if (!this.isActive(closestTab.dataset.route)) {
                        setTimeout(() => {
                                const href = closestTab.getAttribute('href');
                                @auth
                                window.Livewire.navigate(href);
                            @else
                                window.location.href = href;
                            @endauth
                        }, 150);
                }
            }
        }));
    });
</script>
