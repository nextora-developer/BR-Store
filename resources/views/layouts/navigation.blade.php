<nav class="sticky top-0 z-50 border-b border-white/10 bg-black/95 backdrop-blur-md">
    <div class="max-w-7xl5 mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20">

            {{-- Left side: Logo + Desktop Links --}}
            <div class="flex items-center">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="group flex items-center gap-3">
                        <img src="{{ asset('images/logo.png') }}" alt="BR Store"
                            class="h-10 w-10 rounded-2xl object-cover shadow-lg shadow-[#D4AF37]/20 group-hover:scale-105 transition-transform" />

                        <span
                            class="text-xl font-bold tracking-tight text-white group-hover:text-[#D4AF37] transition-colors">
                            BRIF
                        </span>
                    </a>
                </div>

                {{-- Desktop nav --}}
                <div class="hidden lg:flex items-center ms-10 space-x-1">
                    @php
                        $baseClass = 'px-4 py-2 text-base font-semibold transition-all duration-200 rounded-xl';
                        $activeClass = 'text-[#D4AF37] bg-[#D4AF37]/10';
                        $inactiveClass = 'text-gray-300 hover:text-white hover:bg-white/10';
                    @endphp

                    <a href="{{ route('home') }}"
                        class="{{ $baseClass }} {{ request()->routeIs('home') ? $activeClass : $inactiveClass }}">
                        Home
                    </a>

                    <a href="{{ route('shop.index') }}"
                        class="{{ $baseClass }} {{ request()->routeIs('shop.*') ? $activeClass : $inactiveClass }}">
                        Shop
                    </a>

                    <a href="https://brif.cloud/" target="_blank" rel="noopener noreferrer"
                        class="{{ $baseClass }} {{ $inactiveClass }} hover:text-[#D4AF37]">
                        Official Site
                    </a>

                    {{-- More Dropdown (Desktop) --}}
                    <div x-data="{ openMore: false }" class="relative">
                        <button @click="openMore = !openMore" @click.outside="openMore = false"
                            class="{{ $baseClass }} {{ $inactiveClass }} flex items-center gap-1">
                            <span>More</span>
                            <svg class="h-4 w-4 transition-transform" :class="{ 'rotate-180': openMore }" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>

                        <div x-cloak x-show="openMore" x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95"
                            class="absolute left-0 mt-2 w-48 rounded-2xl border border-white/10 bg-black/95 backdrop-blur shadow-xl ring-1 ring-black/40 z-50 overflow-hidden">
                            <div class="p-1.5">
                                <a href="{{ route('how-to-order') }}"
                                    class="block px-4 py-2.5 text-sm text-gray-300 hover:bg-white/10 hover:text-[#D4AF37] rounded-xl transition">
                                    How to Order
                                </a>
                                <a href="{{ route('faq') }}"
                                    class="block px-4 py-2.5 text-sm text-gray-300 hover:bg-white/10 hover:text-[#D4AF37] rounded-xl transition">
                                    FAQ
                                </a>
                                <a href="{{ route('vouchers.index') }}"
                                    class="block px-4 py-2.5 text-sm text-gray-300 hover:bg-white/10 hover:text-[#D4AF37] rounded-xl transition">
                                    Voucher
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Center: Search Bar (Desktop) --}}
            <div class="hidden md:flex flex-1 items-center justify-center px-8 lg:px-20">
                <form method="GET" action="{{ route('shop.index') }}" class="w-full max-w-lg">
                    <div class="relative group">
                        <input type="text" name="q" value="{{ request('q') }}"
                            placeholder="Search products..."
                            class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/10 rounded-full px-6 py-2.5 text-sm focus:ring-2 focus:ring-[#D4AF37]/30 focus:bg-black transition-all">
                        <button type="submit"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 group-hover:text-[#D4AF37] transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m21 21-4.35-4.35M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Right: Actions --}}
            <div class="flex items-center gap-2 sm:gap-4">

                {{-- 🔍 Mobile Search Button --}}
                <button type="button" onclick="toggleMobileSearch(true)"
                    class="md:hidden group relative p-2 text-gray-300 hover:text-white hover:bg-white/10 rounded-full transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m21 21-4.35-4.35M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z" />
                    </svg>
                </button>

                {{-- Cart --}}
                <a href="{{ route('cart.index') }}"
                    class="group relative p-2 text-gray-300 hover:text-white hover:bg-white/10 rounded-full transition">
                    {{-- <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg> --}}
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24"
                        stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
                    </svg>

                    <span data-cart-count
                        class="absolute top-0 right-0 h-5 w-5 bg-[#D4AF37] text-black text-[10px] font-bold flex items-center justify-center rounded-full ring-2 ring-black">
                        {{ auth()->user()?->cart?->items?->count() ?? 0 }}
                    </span>
                </a>

                {{-- Desktop User --}}
                @auth
                    <div class="hidden sm:block">
                        <div x-data="{ openUser: false }" class="relative">
                            <button type="button" @click="openUser = !openUser" @click.outside="openUser = false"
                                @keydown.escape.window="openUser = false"
                                class="flex items-center gap-2 p-1 pr-3 rounded-full border border-white/10 hover:bg-white/10 transition">
                                <div
                                    class="h-8 w-8 rounded-full bg-gradient-to-br from-[#D4AF37] to-[#8f6a10]
                                    flex items-center justify-center text-[11px] font-bold text-black uppercase">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>

                                <span class="text-base font-semibold text-gray-200 max-w-[100px] truncate">
                                    {{ Auth::user()->name }}
                                </span>

                                <svg class="h-4 w-4 text-gray-300 transition-transform duration-200"
                                    :class="{ 'rotate-180': openUser }" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>

                            <div x-cloak x-show="openUser" x-transition:enter="transition ease-out duration-120"
                                x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-90"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                                class="absolute right-0 mt-2 w-56 z-50">
                                <div
                                    class="rounded-2xl overflow-hidden bg-gradient-to-b from-[#0f0f0f] to-[#070707]
                                    border border-white/10 shadow-2xl shadow-black/70 ring-1 ring-black/40">
                                    <div class="p-2 space-y-1">

                                        <a href="{{ route('account.index') }}"
                                            class="block px-4 py-2.5 text-sm font-medium rounded-xl transition
                                            text-gray-200 hover:bg-white/10 hover:text-[#D4AF37]
                                            {{ request()->routeIs('account.index') ? 'bg-white/10 text-[#D4AF37]' : '' }}">
                                            Account
                                        </a>

                                        <a href="{{ route('account.orders.index') }}"
                                            class="block px-4 py-2.5 text-sm font-medium rounded-xl transition
                                            text-gray-200 hover:bg-white/10 hover:text-[#D4AF37]
                                            {{ request()->routeIs('account.orders.*') ? 'bg-white/10 text-[#D4AF37]' : '' }}">
                                            My Orders
                                        </a>

                                        <a href="{{ route('account.favorites.index') }}"
                                            class="block px-4 py-2.5 text-sm font-medium rounded-xl transition
                                            text-gray-200 hover:bg-white/10 hover:text-[#D4AF37]
                                            {{ request()->routeIs('account.favorites.*') ? 'bg-white/10 text-[#D4AF37]' : '' }}">
                                            Wishlist
                                        </a>

                                        <div class="my-1 border-t border-white/10"></div>

                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit"
                                                class="w-full text-left block px-4 py-2.5 text-sm font-medium rounded-xl transition
                                                text-red-400 hover:bg-white/10 hover:text-red-300">
                                                Log Out
                                            </button>
                                        </form>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                        class="hidden sm:inline-flex px-6 py-2.5 rounded-full bg-[#D4AF37] text-black text-sm font-bold hover:bg-[#e5c55a] transition shadow-lg shadow-[#D4AF37]/20">
                        Login
                    </a>
                @endauth

            </div>
        </div>
    </div>
</nav>

{{-- ✅ Mobile Search Sheet (App-like) --}}
<div id="mobileSearchSheet" class="md:hidden fixed inset-0 z-[80] hidden">
    <div class="absolute inset-0 bg-black/60" onclick="toggleMobileSearch(false)"></div>

    <div class="absolute inset-x-0 top-0 bg-black/95 backdrop-blur border-b border-white/10 p-4">
        <div class="flex items-center gap-3">

            <form method="GET" action="{{ route('shop.index') }}" class="flex-1">
                <div class="relative group">
                    <input id="mobileSearchInput" type="text" name="q" value="{{ request('q') }}"
                        placeholder="Search products..."
                        class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/10
                               rounded-2xl px-4 py-3 text-sm
                               focus:ring-2 focus:ring-[#D4AF37]/30 focus:bg-black transition-all">

                    <button type="submit"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-300 group-hover:text-[#D4AF37] transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m21 21-4.35-4.35M11 18a7 7 0 1 1 0-14 7 7 0 0 1 0 14z" />
                        </svg>
                    </button>
                </div>
            </form>

            <button type="button" class="w-10 h-10 rounded-full bg-white/10 text-white grid place-items-center"
                onclick="toggleMobileSearch(false)">
                ✕
            </button>

        </div>
    </div>
</div>

{{-- ✅ Mobile Bottom Tab Bar (App-like) --}}
<div class="lg:hidden fixed inset-x-0 bottom-0 z-[60] bg-black/95 backdrop-blur border-t border-white/10">
    <div class="max-w-7xl5 mx-auto px-4">
        <div class="grid grid-cols-4 py-2">

            {{-- Home --}}
            <a href="{{ route('home') }}"
                class="flex flex-col items-center justify-center py-2 rounded-xl
                {{ request()->routeIs('home') ? 'text-[#D4AF37]' : 'text-gray-300' }}">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>

                <span class="text-[11px] font-bold mt-1">Home</span>
            </a>

            {{-- Shop --}}
            <a href="{{ route('shop.index') }}"
                class="flex flex-col items-center justify-center py-2 rounded-xl
                {{ request()->routeIs('shop.*') ? 'text-[#D4AF37]' : 'text-gray-300' }}">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>

                <span class="text-[11px] font-bold mt-1">Shop</span>
            </a>

            {{-- Chat (WhatsApp) --}}
            <a href="https://wa.me/60123011610" target="_blank" rel="noopener noreferrer"
                class="flex flex-col items-center justify-center py-2 rounded-xl text-gray-300 hover:text-white">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
                </svg>

                <span class="text-[11px] font-bold mt-1">Chat</span>
            </a>

            {{-- More (包含 Login / Dashboard) --}}
            <button type="button" onclick="toggleMobileMore(true)"
                class="flex flex-col items-center justify-center py-2 rounded-xl text-gray-300 hover:text-white">
                <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>

                <span class="text-[11px] font-bold mt-1">More</span>
            </button>

        </div>
    </div>
</div>

@php
    $noTabRoutes = [
        'login',
        'register',
        'password.request',
        'password.email',
        'password.reset',
        'password.update',
        'verification.notice',
        'verification.verify',
    ];
@endphp

@if (!request()->routeIs(...$noTabRoutes))
    {{-- ✅ Mobile More Bottom Sheet --}}
    <div id="mobileMoreSheet"
        class="lg:hidden fixed inset-0 z-[70] invisible opacity-0 transition-all duration-300 overflow-hidden"
        aria-modal="true" role="dialog">

        {{-- Backdrop with soft blur --}}
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" onclick="toggleMobileMore(false)"></div>

        {{-- Sheet Container --}}
        <div id="mobileMoreContent"
            class="absolute inset-x-0 bottom-0 bg-[#0A0A0A] border-t border-white/10 rounded-t-[2.5rem] p-6 pb-10 transition-transform duration-300 translate-y-full shadow-[0_-10px_40px_rgba(0,0,0,0.5)]">

            {{-- Handlebar for visual affordance --}}
            <div class="w-12 h-1.5 bg-white/20 rounded-full mx-auto mb-6" onclick="toggleMobileMore(false)"></div>

            <div class="flex items-center justify-between mb-8">
                <div class="w-full">
                    @auth
                        <div
                            class="flex items-center gap-4 w-full px-5 py-4 rounded-2xl
                       bg-white/[0.04] border border-white/10
                       shadow-[0_10px_30px_rgba(0,0,0,0.4)]
                       hover:border-[#D4AF37]/30 transition-colors">

                            {{-- Avatar --}}
                            <div
                                class="relative shrink-0 w-12 h-12 rounded-full
                           bg-gradient-to-br from-[#D4AF37]/30 to-[#8f6a10]/30
                           flex items-center justify-center
                           text-[#D4AF37] font-black uppercase">
                                {{ strtoupper(substr(auth()->user()->name ?? auth()->user()->email, 0, 1)) }}
                            </div>

                            {{-- Name + Email --}}
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-black uppercase tracking-wide text-white truncate">
                                    {{ auth()->user()->name ?? 'User' }}
                                </div>
                                <div class="text-sm text-gray-400 truncate">
                                    {{ auth()->user()->email }}
                                </div>
                            </div>

                            {{-- Online Status --}}
                            <div class="relative flex h-2.5 w-2.5 shrink-0">
                                <span
                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-40"></span>
                                <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                            </div>
                        </div>
                    @else
                        <div
                            class="flex items-center gap-4 w-full px-5 py-4 rounded-2xl
                       bg-white/[0.04] border border-white/10">

                            <div
                                class="w-12 h-12 rounded-full bg-white/10 flex items-center justify-center text-gray-400 font-black">
                                ?
                            </div>

                            <div class="flex-1">
                                <div class="text-sm font-black uppercase tracking-wide text-gray-300">
                                    Guest
                                </div>
                                <div class="text-sm text-gray-500">
                                    Not signed in
                                </div>
                            </div>
                        </div>
                    @endauth
                </div>
            </div>


            {{-- Core Grid --}}
            <div class="grid grid-cols-4 gap-4 mb-8">
                @php
                    $menuItems = [
                        [
                            'route' => 'cart.index',
                            'label' => 'Cart',
                            'icon' =>
                                'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
                        ],
                        [
                            'route' => 'vouchers.index',
                            'label' => 'Vouchers',
                            'icon' =>
                                'M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z',
                        ],
                        [
                            'route' => 'how-to-order',
                            'label' => 'Help',
                            'icon' => 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                        ],
                        [
                            'route' => 'faq',
                            'label' => 'FAQ',
                            'icon' =>
                                'M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                        ],
                    ];
                @endphp

                @foreach ($menuItems as $item)
                    <a href="{{ route($item['route']) }}" class="flex flex-col items-center gap-2 group">
                        <div
                            class="w-14 h-14 rounded-2xl bg-white/5 flex items-center justify-center text-gray-400 group-hover:bg-[#D4AF37]/20 group-hover:text-[#D4AF37] transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="{{ $item['icon'] }}" />
                            </svg>
                        </div>
                        <span
                            class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </div>

            {{-- Highlighted Link --}}
            <a href="https://brif.cloud/" target="_blank"
                class="flex items-center justify-between p-4 rounded-2xl bg-gradient-to-r from-[#D4AF37]/20 to-transparent border border-[#D4AF37]/20 text-[#D4AF37] mb-6 transition-all active:scale-95">
                <span class="font-black tracking-tight">Visit Official Website</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </a>

            {{-- ================= Account (App-style Sidebar) ================= --}}
            @auth
                <div class="space-y-3 mb-8">

                    <div class="rounded-3xl bg-white/[0.04] border border-white/10 overflow-hidden">

                        {{-- Dashboard --}}
                        <a href="{{ route('account.index') }}"
                            class="flex items-center justify-between px-5 py-4 text-sm font-bold text-white
                  border-b border-white/10 active:bg-white/5 transition">
                            <div class="flex items-center gap-3">
            
                                <svg class="w-5 h-5 text-[#D4AF37]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" >
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" />
                                </svg>

                                <span>Dashboard</span>
                            </div>
                            <span class="w-2 h-2 border-r-2 border-b-2 border-white/40 rotate-[-45deg]"></span>
                        </a>

                        {{-- Orders --}}
                        <a href="{{ route('account.orders.index') }}"
                            class="flex items-center justify-between px-5 py-4 text-sm font-bold text-white
                  border-b border-white/10 active:bg-white/5 transition">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-[#D4AF37]" fill="none" stroke="currentColor"
                                    stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                                <span>My Orders</span>
                            </div>
                            <span class="w-2 h-2 border-r-2 border-b-2 border-white/40 rotate-[-45deg]"></span>
                        </a>

                        {{-- Wishlist --}}
                        <a href="{{ route('account.favorites.index') }}"
                            class="flex items-center justify-between px-5 py-4 text-sm font-bold text-white
                  border-b border-white/10 active:bg-white/5 transition">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-[#D4AF37]" fill="none" stroke="currentColor"
                                    stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M4.318 6.318a5.5 5.5 0 017.778 0L12 6.586l-.096-.097a5.5 5.5 0 117.778 7.778L12 21.192l-7.682-7.682a5.5 5.5 0 010-7.192z" />
                                </svg>
                                <span>My Wishlist</span>
                            </div>
                            <span class="w-2 h-2 border-r-2 border-b-2 border-white/40 rotate-[-45deg]"></span>
                        </a>

                        {{-- Addresses --}}
                        <a href="{{ route('account.address.index') }}"
                            class="flex items-center justify-between px-5 py-4 text-sm font-bold text-white
                  border-b border-white/10 active:bg-white/5 transition">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-[#D4AF37]" fill="none" stroke="currentColor"
                                    stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>Shipping Addresses</span>
                            </div>
                            <span class="w-2 h-2 border-r-2 border-b-2 border-white/40 rotate-[-45deg]"></span>
                        </a>

                        {{-- Profile --}}
                        <a href="{{ route('account.profile.edit') }}"
                            class="flex items-center justify-between px-5 py-4 text-sm font-bold text-white
                  active:bg-white/5 transition">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-[#D4AF37]" fill="none" stroke="currentColor"
                                    stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0
                                                       a1.724 1.724 0 002.573 1.066
                                                       c1.543-.94 3.31.826 2.37 2.37
                                                       a1.724 1.724 0 001.065 2.572
                                                       c1.756.426 1.756 2.924 0 3.35
                                                       a1.724 1.724 0 00-1.066 2.573
                                                       c.94 1.543-.826 3.31-2.37 2.37
                                                       a1.724 1.724 0 00-2.572 1.065
                                                       c-.426 1.756-2.924 1.756-3.35 0
                                                       a1.724 1.724 0 00-2.573-1.066
                                                       c-1.543.94-3.31-.826-2.37-2.37
                                                       a1.724 1.724 0 00-1.065-2.572
                                                       c-1.756-.426-1.756-2.924 0-3.35
                                                       a1.724 1.724 0 001.066-2.573
                                                       c-.94-1.543.826-3.31 2.37-2.37
                                                       a1.724 1.724 0 002.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>Profile Settings</span>
                            </div>
                            <span class="w-2 h-2 border-r-2 border-b-2 border-white/40 rotate-[-45deg]"></span>
                        </a>

                    </div>

                </div>
            @endauth


            {{-- ================= Auth Section ================= --}}
            <div class="space-y-3">

                @auth
                    {{-- Sign Out --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center justify-center gap-3
                       p-4 rounded-2xl
                       text-red-400 font-bold text-sm
                       bg-white/[0.02] border border-white/10
                       active:bg-red-500/10 active:scale-[0.97]
                       transition">

                            {{-- Icon --}}
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1m0-10V5" />
                            </svg>

                            <span>Sign Out</span>
                        </button>
                    </form>
                @else
                    {{-- Login | Register side by side --}}
                    <div class="grid grid-cols-2 gap-3">

                        {{-- Login --}}
                        <a href="{{ route('login') }}"
                            class="flex items-center justify-center gap-2
                       p-4 rounded-2xl
                       bg-[#D4AF37] text-black font-black text-center
                       shadow-lg shadow-[#D4AF37]/20
                       active:scale-[0.98] transition-transform">

                            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8.25 9V5.25A2.25 2.25 0 0 1 10.5 3h6a2.25 2.25 0 0 1 2.25 2.25v13.5A2.25 2.25 0 0 1 16.5 21h-6a2.25 2.25 0 0 1-2.25-2.25V15M12 9l3 3m0 0-3 3m3-3H2.25" />
                            </svg>


                            <span>Login</span>
                        </a>

                        {{-- Register --}}
                        <a href="{{ route('register') }}"
                            class="flex items-center justify-center gap-2
                       p-4 rounded-2xl
                       border border-white/10 text-white font-bold text-center
                       active:scale-[0.98] transition-transform">

                            <span>Register</span>
                        </a>

                    </div>
                @endauth

            </div>


        </div>
    </div>
@endif

<script>
    function toggleMobileSearch(show) {
        const el = document.getElementById('mobileSearchSheet');
        if (!el) return;

        el.classList.toggle('hidden', !show);

        if (show) {
            // focus input
            setTimeout(() => {
                const input = document.getElementById('mobileSearchInput');
                if (input) input.focus();
            }, 60);
        }
    }
</script>

<script>
    function toggleMobileMore(show) {
        const sheet = document.getElementById('mobileMoreSheet');
        const content = document.getElementById('mobileMoreContent');

        if (show) {
            sheet.classList.remove('invisible', 'opacity-0');
            sheet.classList.add('opacity-100');
            setTimeout(() => content.classList.remove('translate-y-full'), 10);
            document.body.style.overflow = 'hidden'; // Prevent scroll
        } else {
            content.classList.add('translate-y-full');
            sheet.classList.remove('opacity-100');
            setTimeout(() => {
                sheet.classList.add('invisible', 'opacity-0');
                document.body.style.overflow = '';
            }, 300);
        }
    }
</script>
