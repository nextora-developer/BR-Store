<nav x-data="{ open: false }" class="sticky top-0 z-50 border-b border-white/10 bg-black/95 backdrop-blur-md">

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
                            Store
                        </span>
                    </a>
                </div>

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

                    {{-- More Dropdown --}}
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
                                <a href="#"
                                    class="block px-4 py-2.5 text-sm text-gray-300 hover:bg-white/10 hover:text-[#D4AF37] rounded-xl transition">About
                                    Us</a>
                                <a href="#"
                                    class="block px-4 py-2.5 text-sm text-gray-300 hover:bg-white/10 hover:text-[#D4AF37] rounded-xl transition">How
                                    to Order</a>
                                <a href="#"
                                    class="block px-4 py-2.5 text-sm text-gray-300 hover:bg-white/10 hover:text-[#D4AF37] rounded-xl transition">FAQ</a>
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

                {{-- Cart --}}
                <a href="{{ route('cart.index') }}"
                    class="group relative p-2 text-gray-300 hover:text-white hover:bg-white/10 rounded-full transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                    <span data-cart-count
                        class="absolute top-0 right-0 h-5 w-5 bg-[#D4AF37] text-black text-[10px] font-bold flex items-center justify-center rounded-full ring-2 ring-black">
                        {{ auth()->user()?->cart?->items?->count() ?? 0 }}
                    </span>
                </a>

                @auth
                    <div class="hidden sm:block">
                        <div x-data="{ openUser: false }" class="relative">

                            <!-- Trigger -->
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

                                <!-- 小箭头（可选） -->
                                <svg class="h-4 w-4 text-gray-300 transition-transform duration-200"
                                    :class="{ 'rotate-180': openUser }" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>

                            <!-- Dropdown -->
                            <div x-cloak x-show="openUser" x-transition:enter="transition ease-out duration-120"
                                x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-90"
                                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                                class="absolute right-0 mt-2 w-56 z-50">
                                <div
                                    class="rounded-2xl overflow-hidden
                       bg-gradient-to-b from-[#0f0f0f] to-[#070707]
                       border border-white/10
                       shadow-2xl shadow-black/70
                       ring-1 ring-black/40">
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

                {{-- Mobile Hamburger --}}
                <button @click="open = !open" class="sm:hidden p-2 rounded-xl text-gray-300 hover:bg-white/10">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div x-show="open" x-cloak x-transition class="sm:hidden border-t border-white/10 bg-black/95 backdrop-blur">
        <div class="p-4 space-y-4">
            {{-- Mobile Search --}}
            <form method="GET" action="{{ route('shop.index') }}">
                <div class="relative">
                    <input type="text" name="q" placeholder="Search products..."
                        class="w-full bg-white/10 text-white placeholder-gray-400 border border-white/10 rounded-2xl px-5 py-3 text-sm focus:ring-2 focus:ring-[#D4AF37]/30 focus:bg-black transition-all">
                </div>
            </form>

            <div class="grid grid-cols-2 gap-2">
                <a href="{{ route('home') }}"
                    class="flex items-center justify-center p-3 rounded-2xl text-sm font-bold
                    {{ request()->routeIs('home') ? 'bg-[#D4AF37]/10 text-[#D4AF37]' : 'bg-white/5 text-gray-300 hover:bg-white/10 hover:text-white' }}">
                    Home
                </a>
                <a href="{{ route('shop.index') }}"
                    class="flex items-center justify-center p-3 rounded-2xl text-sm font-bold
                    {{ request()->routeIs('shop.*') ? 'bg-[#D4AF37]/10 text-[#D4AF37]' : 'bg-white/5 text-gray-300 hover:bg-white/10 hover:text-white' }}">
                    Shop
                </a>
            </div>

            @auth
                <div class="p-4 bg-white/5 border border-white/10 rounded-3xl">
                    <div class="flex items-center gap-3 mb-4">
                        <div
                            class="h-10 w-10 rounded-full bg-[#D4AF37] flex items-center justify-center text-black font-bold">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-white">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-400">{{ Auth::user()->email }}</p>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <a href="{{ route('account.index') }}"
                            class="block p-2 text-sm text-gray-300 font-medium hover:text-white hover:bg-white/10 rounded-xl transition">Account</a>
                        <a href="{{ route('account.orders.index') }}"
                            class="block p-2 text-sm text-gray-300 font-medium hover:text-white hover:bg-white/10 rounded-xl transition">My
                            Orders</a>
                        <a href="{{ route('account.favorites.index') }}"
                            class="block p-2 text-sm text-gray-300 font-medium hover:text-white hover:bg-white/10 rounded-xl transition">Wishlist</a>
                        <a href="{{ route('account.address.index') }}"
                            class="block p-2 text-sm text-gray-300 font-medium hover:text-white hover:bg-white/10 rounded-xl transition">Shipping
                            Address</a>
                        <a href="{{ route('account.profile.edit') }}"
                            class="block p-2 text-sm text-gray-300 font-medium hover:text-white hover:bg-white/10 rounded-xl transition">Profile
                            Settings</a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full text-left p-2 text-sm text-red-400 font-bold hover:bg-white/10 rounded-xl transition">
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="flex flex-col gap-2">
                    <a href="{{ route('login') }}"
                        class="w-full p-4 rounded-2xl bg-[#D4AF37] text-black text-center font-bold hover:bg-[#e5c55a] transition shadow-lg shadow-[#D4AF37]/20">
                        Login
                    </a>
                    <a href="{{ route('register') }}"
                        class="w-full p-4 rounded-2xl border border-white/15 text-white text-center font-bold hover:bg-white/10 transition">
                        Register
                    </a>
                </div>
            @endauth
        </div>
    </div>
</nav>
