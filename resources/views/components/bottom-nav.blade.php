@if (!isset($hideBottomNav) || !$hideBottomNav)
    <div class="fixed bottom-[calc(2rem+env(safe-area-inset-bottom))] left-1/2 -translate-x-1/2
            flex items-center gap-1.5 z-50
            transition-all duration-300 ease-out"
        dir="ltr" x-data
        :class="$store.nav.hidden ? 'opacity-0 translate-y-24 pointer-events-none' : 'opacity-100 translate-y-0'">

        {{-- ═══════════════════════════════ --}}
        {{-- LEFT PILL — Navigation Tabs     --}}
        {{-- ═══════════════════════════════ --}}
        <nav x-data="navLeftPill()" dir="rtl" @touchstart="touchStart($event)" @touchmove.prevent="touchMove($event)"
            @touchend="touchEnd($event)"
            class="flex items-center p-1 rounded-[2rem] liquid-glass transform-gpu touch-none relative">

            {{-- Glass gradient overlay --}}
            <div
                class="absolute inset-0 rounded-[2rem] pointer-events-none
                    bg-gradient-to-b from-white/70 via-white/20 to-white/5
                    dark:from-white/10 dark:via-white/5 dark:to-transparent">
            </div>

            {{-- Animated active pill --}}
            <div x-ref="pill"
                class="absolute top-1 bottom-1 right-0
                    bg-white/40 dark:bg-white/10
                    backdrop-blur-md rounded-full pointer-events-none"
                style="will-change: transform, width;"></div>

            {{-- Tab items --}}
            @foreach ($navItems as $item)
                @php
                    $routeBase = str_replace('.index', '', $item['route']);
                @endphp
                <a href="{{ route($item['route']) }}" id="tour-nav-{{ $item['route'] }}" @auth wire:navigate @endauth
                    data-route="{{ $item['route'] }}" data-base-route="{{ $routeBase }}"
                    class="flex flex-col items-center justify-center py-1.5 px-[0.65rem] relative z-10 liquid-transition whitespace-nowrap"
                    :class="isActive('{{ $item['route'] }}') ?
                        'text-banhafade-accent drop-shadow-[0_0_8px_rgba(1,101,225,0.4)]' :
                        'text-gray-500 dark:text-gray-400'">

                    <div class="relative">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            :stroke-width="isActive('{{ $item['route'] }}') ? '2' : '1.5'" stroke="currentColor"
                            class="w-[22px] h-[22px] liquid-transition"
                            :class="isActive('{{ $item['route'] }}') ? 'scale-110' : 'scale-100'">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                        </svg>

                        @if (($item['badge'] ?? false) && $offerCount > 0)
                            <span
                                class="absolute -top-1.5 -right-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-banhafade-accent text-[10px] font-bold text-white shadow-sm ring-2 ring-white/20 dark:ring-white/10 translate-x-1 animate-in zoom-in duration-300">
                                {{ $offerCount > 9 ? '9+' : $offerCount }}
                            </span>
                        @endif
                    </div>
                    <span class="text-[10px] font-semibold mt-0.5">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </nav>

        {{-- ═══════════════════════════════ --}}
        {{-- RIGHT PILL — Search Icon Only  --}}
        {{-- ═══════════════════════════════ --}}
        <div class="flex items-center p-1 rounded-[2rem] liquid-glass relative z-50 shrink-0">
            @auth
                <!-- Global Interactive Components: Notification Bell -->
                <a href="{{ route('notifications') }}" wire:navigate
                    class="p-1.5 rounded-full hover:bg-black/5 dark:hover:bg-white/10 relative liquid-button liquid-transition"
                    :class="$store.nav.currentRoute === 'notifications' ?
                        'text-banhafade-accent drop-shadow-[0_0_8px_rgba(1,101,225,0.4)]' :
                        'text-gray-500 dark:text-gray-400'">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                        :stroke-width="$store.nav.currentRoute === 'notifications' ? '2' : '1.5'" stroke="currentColor"
                        class="w-[22px] h-[22px] liquid-transition"
                        :class="$store.nav.currentRoute === 'notifications' ? 'scale-110' : 'scale-100'">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                    </svg>
                    @if ($unreadNotificationsCount > 0)
                        <span
                            class="absolute top-2 right-2.5 h-2 w-2 rounded-full bg-red-500 ring-2 ring-white/20 dark:ring-white/10 animate-pulse"></span>
                    @endif
                </a>

                <!-- Vertical Divider (Only for auth) -->
                <div class="mx-0.5 w-px h-5 bg-black/5 dark:bg-white/10 rounded-full"></div>
            @endauth

            <!-- Global Interactive Components: Search Button (Shown for everyone) -->
            <button type="button" @click="$dispatch('open-global-search')"
                class="p-1.5 rounded-full hover:bg-black/5 dark:hover:bg-white/10 liquid-button text-gray-500 dark:text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-[22px] h-[22px] liquid-transition scale-100">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 0z" />
                </svg>
            </button>
        </div>

    </div>
@endif

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
                        // Sync store route immediately
                        if ( Alpine.store('nav')) {
                            Alpine.store('nav').currentRoute = document.body.dataset.route || '';
                        }

                        this.$nextTick(() => {
                            const activeTab = this.tabs.find(t => this.isActive(t
                                .dataset.route)) || this.tabs[0];
                            this.snapTo(activeTab, true);
                        });
                    });

                    setTimeout(() => {
                        const activeTab = this.tabs.find(t => this.isActive(t.dataset.route)) ||
                            this.tabs[0];
                        this.snapTo(activeTab, true);
                    }, 50);

                    window.addEventListener('resize', () => {
                        const activeTab = this.tabs.find(t => this.isActive(t.dataset.route)) ||
                            this.tabs[0];
                        this.snapTo(activeTab, true);
                    });
                },

                isActive(route) {
                    const currentRoute = Alpine.store('nav').currentRoute;
                    if (!currentRoute) return false;

                    if(route === 'home') {
                        return currentRoute === 'home' || [
                            'whatsapp.connect'
                        ].includes(currentRoute)
                    }

                    // Internal Workshop Check: Dashboard Home should include management sub-pages
                    if (route === 'dashboard.home') {
                        return [
                            'dashboard.home',
                            'dashboard.reviews',
                            'dashboard.services',
                            'dashboard.categories',
                            'dashboard.barbers',
                            'dashboard.clients'
                        ].includes(currentRoute);
                    }

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
