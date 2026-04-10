{{-- iOS 26 Split Bottom Navigation — Two Separate Pills --}}

@php
    $navItems = [
        [
            'route' => 'home',
            'label' => 'الرئيسية',
            'icon' =>
                'M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25',
        ],
        [
            'route' => 'bookings',
            'label' => 'حجوزاتي',
            'icon' =>
                'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5',
        ],
        [
            'route' => 'offers',
            'label' => 'العروض',
            'icon' =>
                'M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 1 0 9.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1 1 14.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z',
        ],
        [
            'route' => 'profile.index',
            'label' => 'حسابي',
            'icon' =>
                'M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z',
        ],
    ];
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
                $isActive = request()->routeIs($item['route']) || request()->routeIs($routeBase . '.*'); 
            @endphp
            <a href="{{ route($item['route']) }}" id="tour-nav-{{ $item['route'] }}" @auth wire:navigate @endauth
                data-route="{{ $item['route'] }}" data-active="{{ $isActive ? 'true' : 'false' }}"
                class="flex flex-col items-center justify-center py-2 px-[1rem] relative z-10
                      liquid-transition whitespace-nowrap
                      {{ $isActive ? 'text-fadebook-accent drop-shadow-[0_0_8px_rgba(1,101,225,0.4)]' : 'text-gray-500 dark:text-gray-400' }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="{{ $isActive ? '2' : '1.5' }}" stroke="currentColor"
                    class="w-[22px] h-[22px] liquid-transition {{ $isActive ? 'scale-110' : 'scale-100' }}">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                </svg>
                <span class="text-[10px] font-semibold mt-0.5">{{ $item['label'] }}</span>
            </a>
        @endforeach
    </nav>

    {{-- ═══════════════════════════════ --}}
    {{-- RIGHT PILL — Search Icon Only  --}}
    {{-- ═══════════════════════════════ --}}
    @php $searchActive = request()->routeIs('search') || request()->routeIs('search.*'); @endphp
    <a href="{{ route('search') }}" id="tour-nav-search" @auth wire:navigate @endauth
        class="flex items-center justify-center p-3 rounded-[2rem] liquid-glass liquid-button
              {{ $searchActive
                  ? 'text-fadebook-accent ring-2 ring-fadebook-accent/20 drop-shadow-[0_0_8px_rgba(1,101,225,0.4)]'
                  : 'text-gray-500 dark:text-gray-400' }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
            stroke-width="{{ $searchActive ? '2' : '1.5' }}" stroke="currentColor"
            class="w-[22px] h-[22px] liquid-transition {{ $searchActive ? 'scale-110' : 'scale-100' }}">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803a7.5 7.5 0 0010.607 0z" />
        </svg>
    </a>

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

                    setTimeout(() => {
                        const activeTab = this.tabs.find(t => t.dataset.active === 'true') ||
                            this.tabs[0];
                        this.snapTo(activeTab, true);
                    }, 50);

                    window.addEventListener('resize', () => {
                        const activeTab = this.tabs.find(t => t.dataset.active === 'true') ||
                            this.tabs[0];
                        this.snapTo(activeTab, true);
                    });

                    this.tabs.forEach(tab => {
                        tab.addEventListener('click', () => this.snapTo(tab, false));
                    });
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

                    if (closestTab.dataset.active !== 'true') {
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
