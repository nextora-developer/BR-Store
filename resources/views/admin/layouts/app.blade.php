<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Admin Panel | CompanyName</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        [x-cloak] {
            display: none !important;
        }

        body {
            font-family: 'Inter', sans-serif;
        }

        /* Custom scrollbar for a cleaner look */
        ::-webkit-scrollbar {
            width: 5px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #e5e7eb;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #D4AF37;
        }

        /* Trix Editor Polish */
        trix-toolbar .trix-button-group {
            border-color: #f3f4f6 !important;
        }

        trix-editor {
            border: 1px solid #e5e7eb !important;
            border-radius: 0.75rem !important;
            min-height: 150px !important;
        }
    </style>

    <link rel="stylesheet" href="https://unpkg.com/trix@1.3.1/dist/trix.css">
    <script src="https://unpkg.com/trix@1.3.1/dist/trix.js"></script>
</head>

<body class="bg-[#F8F9FA] text-[#1A1A1A] antialiased">
    <div x-data="{
        collapsed: (localStorage.getItem('admin_sidebar_collapsed') === '1'),
        toggle() {
            this.collapsed = !this.collapsed;
            localStorage.setItem('admin_sidebar_collapsed', this.collapsed ? '1' : '0');
        }
    }" class="min-h-screen flex">

        {{-- SIDEBAR --}}
        <aside
            class="fixed inset-y-0 left-0 h-screen z-30 bg-white border-r border-gray-100 flex flex-col transition-all duration-300 ease-in-out shadow-sm"
            :class="collapsed ? 'w-20' : 'w-64'">

            {{-- Brand Section --}}
            <div class="h-20 flex items-center px-6 border-b border-gray-50">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div
                        class="h-9 w-9 bg-[#D4AF37] rounded-lg flex shrink-0 items-center justify-center text-white shadow-lg shadow-[#D4AF37]/20">
                        <span class="font-bold text-xl">C</span>
                    </div>
                    <div x-show="!collapsed" x-transition.opacity class="whitespace-nowrap">
                        <span class="font-bold text-gray-800 tracking-tight">Company<span
                                class="text-[#D4AF37]">Name</span></span>
                    </div>
                </div>
            </div>

            @php
                $isCatalog =
                    request()->routeIs('admin.categories.*') ||
                    request()->routeIs('admin.products.*') ||
                    request()->routeIs('admin.vouchers.*');
                $isContent = request()->routeIs('admin.banners.*') || request()->routeIs('admin.popup-banners.*');
                $isSettings = request()->routeIs('admin.payment-methods.*') || request()->routeIs('admin.shipping.*');
            @endphp


            {{-- Nav --}}
            <nav class="flex-1 overflow-y-auto py-3 px-4 space-y-1">

                @php
                    $linkBase =
                        'group flex items-center gap-3 px-3 py-4 rounded-xl transition-all duration-200 relative';
                    $idle = 'text-gray-500 hover:bg-gray-50 hover:text-[#D4AF37]';
                    $active = 'bg-[#D4AF37]/5 text-[#B8860B] font-semibold';

                    $iconClass = 'h-5 w-5 shrink-0';
                    $chev = 'M8.25 9l3.75 3.75L15.75 9';
                @endphp

                {{-- Single: Dashboard --}}
                <a href="{{ route('admin.home') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('admin.home') ? $active : $idle }}">
                    @if (request()->routeIs('admin.home'))
                        <span class="absolute left-[-16px] h-6 w-1 bg-[#D4AF37] rounded-r-full"></span>
                    @endif
                    <svg class="{{ $iconClass }}" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>

                    <span x-show="!collapsed" class="text-sm tracking-tight">Dashboard</span>

                    <div x-show="collapsed"
                        class="fixed left-20 ml-2 px-3 py-1.5 bg-gray-900 text-white text-xs rounded shadow-xl opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-50">
                        Dashboard
                    </div>
                </a>

                {{-- Single: Orders --}}
                <a href="{{ route('admin.orders.index') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('admin.orders.*') ? $active : $idle }}">
                    @if (request()->routeIs('admin.orders.*'))
                        <span class="absolute left-[-16px] h-6 w-1 bg-[#D4AF37] rounded-r-full"></span>
                    @endif
                    <svg class="{{ $iconClass }}" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>

                    <span x-show="!collapsed" class="text-sm tracking-tight">Orders</span>

                    <div x-show="collapsed"
                        class="fixed left-20 ml-2 px-3 py-1.5 bg-gray-900 text-white text-xs rounded shadow-xl opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-50">
                        Orders
                    </div>
                </a>

                {{-- Group: Catalog --}}
                <div x-data="{ open: {{ $isCatalog ? 'true' : 'false' }} }" class="space-y-1">
                    <button type="button"
                        class="{{ $linkBase }} {{ $isCatalog ? $active : $idle }} w-full justify-between"
                        @click="if(!collapsed) open = !open">
                        <div class="flex items-center gap-3">
                            <svg class="{{ $iconClass }}" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 8.25l-9 5.25-9-5.25M12 13.5V21M3 8.25V18a1.5 1.5 0 00.75 1.3l7.5 4.33a1.5 1.5 0 001.5 0l7.5-4.33A1.5 1.5 0 0021 18V8.25M12 3l9 5.25-9 5.25L3 8.25 12 3z" />
                            </svg>
                            <span x-show="!collapsed" class="text-sm tracking-tight">Catalog</span>
                        </div>

                        <svg x-show="!collapsed" class="h-4 w-4 text-gray-400 transition-transform"
                            :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $chev }}" />
                        </svg>

                        <div x-show="collapsed"
                            class="fixed left-20 ml-2 px-3 py-1.5 bg-gray-900 text-white text-xs rounded shadow-xl opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-50">
                            Catalog
                        </div>
                    </button>

                    <div x-show="open && !collapsed" x-collapse class="pl-3 space-y-1">
                        @php
                            $subBase = 'flex items-center gap-3 px-3 py-3 rounded-xl text-sm transition';
                            $subIdle = 'text-gray-500 hover:bg-gray-50 hover:text-[#D4AF37]';
                            $subActive = 'bg-[#D4AF37]/10 text-[#8f6a10] font-semibold';
                        @endphp

                        <a href="{{ route('admin.categories.index') }}"
                            class="{{ $subBase }} {{ request()->routeIs('admin.categories.*') ? $subActive : $subIdle }}">
                            <span class="h-2 w-2 rounded-full bg-[#D4AF37]/60"></span>
                            Categories
                        </a>

                        <a href="{{ route('admin.products.index') }}"
                            class="{{ $subBase }} {{ request()->routeIs('admin.products.*') ? $subActive : $subIdle }}">
                            <span class="h-2 w-2 rounded-full bg-[#D4AF37]/60"></span>
                            Products
                        </a>

                        <a href="{{ route('admin.vouchers.index') }}"
                            class="{{ $subBase }} {{ request()->routeIs('admin.vouchers.*') ? $subActive : $subIdle }}">
                            <span class="h-2 w-2 rounded-full bg-[#D4AF37]/60"></span>
                            Vouchers
                        </a>
                    </div>
                </div>

                {{-- Group: Content --}}
                <div x-data="{ open: {{ $isContent ? 'true' : 'false' }} }" class="space-y-1">
                    <button type="button"
                        class="{{ $linkBase }} {{ $isContent ? $active : $idle }} w-full justify-between"
                        @click="if(!collapsed) open = !open">
                        <div class="flex items-center gap-3">
                            <svg class="{{ $iconClass }}" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Z" />
                            </svg>
                            <span x-show="!collapsed" class="text-sm tracking-tight">Content</span>
                        </div>

                        <svg x-show="!collapsed" class="h-4 w-4 text-gray-400 transition-transform"
                            :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $chev }}" />
                        </svg>

                        <div x-show="collapsed"
                            class="fixed left-20 ml-2 px-3 py-1.5 bg-gray-900 text-white text-xs rounded shadow-xl opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-50">
                            Content
                        </div>
                    </button>

                    <div x-show="open && !collapsed" x-collapse class="pl-3 space-y-1">
                        <a href="{{ route('admin.banners.index') }}"
                            class="{{ $subBase }} {{ request()->routeIs('admin.banners.*') ? $subActive : $subIdle }}">
                            <span class="h-2 w-2 rounded-full bg-[#D4AF37]/60"></span>
                            Banners
                        </a>

                        <a href="{{ route('admin.popup-banners.index') }}"
                            class="{{ $subBase }} {{ request()->routeIs('admin.popup-banners.*') ? $subActive : $subIdle }}">
                            <span class="h-2 w-2 rounded-full bg-[#D4AF37]/60"></span>
                            Popup Banner
                        </a>
                    </div>
                </div>

                {{-- Group: Settings --}}
                <div x-data="{ open: {{ $isSettings ? 'true' : 'false' }} }" class="space-y-1">
                    <button type="button"
                        class="{{ $linkBase }} {{ $isSettings ? $active : $idle }} w-full justify-between"
                        @click="if(!collapsed) open = !open">
                        <div class="flex items-center gap-3">
                            <svg class="{{ $iconClass }}" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            </svg>

                            <span x-show="!collapsed" class="text-sm tracking-tight">Settings</span>
                        </div>

                        <svg x-show="!collapsed" class="h-4 w-4 text-gray-400 transition-transform"
                            :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $chev }}" />
                        </svg>

                        <div x-show="collapsed"
                            class="fixed left-20 ml-2 px-3 py-1.5 bg-gray-900 text-white text-xs rounded shadow-xl opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-50">
                            Settings
                        </div>
                    </button>

                    <div x-show="open && !collapsed" x-collapse class="pl-3 space-y-1">
                        <a href="{{ route('admin.payment-methods.index') }}"
                            class="{{ $subBase }} {{ request()->routeIs('admin.payment-methods.*') ? $subActive : $subIdle }}">
                            <span class="h-2 w-2 rounded-full bg-[#D4AF37]/60"></span>
                            Payment Methods
                        </a>

                        <a href="{{ route('admin.shipping.index') }}"
                            class="{{ $subBase }} {{ request()->routeIs('admin.shipping.*') ? $subActive : $subIdle }}">
                            <span class="h-2 w-2 rounded-full bg-[#D4AF37]/60"></span>
                            Shipping Fee
                        </a>
                    </div>
                </div>

                {{-- Single: Users --}}
                <a href="{{ route('admin.users.index') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('admin.users.*') ? $active : $idle }}">
                    @if (request()->routeIs('admin.users.*'))
                        <span class="absolute left-[-16px] h-6 w-1 bg-[#D4AF37] rounded-r-full"></span>
                    @endif
                    <svg class="{{ $iconClass }}" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>
                    <span x-show="!collapsed" class="text-sm tracking-tight">Users</span>
                    <div x-show="collapsed"
                        class="fixed left-20 ml-2 px-3 py-1.5 bg-gray-900 text-white text-xs rounded shadow-xl opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-50">
                        Users
                    </div>
                </a>

                {{-- Single: Point Transaction --}}
                <a href="{{ route('admin.points.index') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('admin.points.*') ? $active : $idle }}">
                    @if (request()->routeIs('admin.points.*'))
                        <span class="absolute left-[-16px] h-6 w-1 bg-[#D4AF37] rounded-r-full"></span>
                    @endif
                    <svg class="{{ $iconClass }}" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M21 11.25v8.25a1.5 1.5 0 0 1-1.5 1.5H5.25a1.5 1.5 0 0 1-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 1 0 9.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1 1 14.625 7.5H12m0 0V21m-8.625-9.75h18c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125h-18c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                    </svg>

                    <span x-show="!collapsed" class="text-sm tracking-tight">Points</span>
                    <div x-show="collapsed"
                        class="fixed left-20 ml-2 px-3 py-1.5 bg-gray-900 text-white text-xs rounded shadow-xl opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-50">
                        Points
                    </div>
                </a>

                {{-- Single: Reports --}}
                <a href="{{ route('admin.reports.index') }}"
                    class="{{ $linkBase }} {{ request()->routeIs('admin.reports.*') ? $active : $idle }}">
                    <svg class="{{ $iconClass }}" fill="none" viewBox="0 0 24 24" stroke-width="1.8"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0 0 20.25 18V6A2.25 2.25 0 0 0 18 3.75H6A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25Z" />
                    </svg>
                    <span x-show="!collapsed" class="text-sm tracking-tight">Reports</span>
                    <div x-show="collapsed"
                        class="fixed left-20 ml-2 px-3 py-1.5 bg-gray-900 text-white text-xs rounded shadow-xl opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity z-50">
                        Reports
                    </div>
                </a>

            </nav>


            {{-- Logout Footer --}}
            <div class="p-4 border-t border-gray-50">
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button
                        class="w-full flex items-center justify-center gap-3 py-2.5 rounded-xl border border-gray-100 text-gray-600 hover:bg-red-50 hover:text-red-600 hover:border-red-100 transition-all duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                d="M8.25 9V5.25A2.25 2.25 0 0110.5 3h6A2.25 2.25 0 0118.75 5.25v13.5A2.25 2.25 0 0116.5 21h-6A2.25 2.25 0 018.25 18.75V15M12 12H3m0 0 3-3m-3 3 3 3" />
                        </svg>
                        <span x-show="!collapsed" class="text-sm font-medium uppercase tracking-wider">Sign Out</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- MAIN CONTENT --}}
        <main class="flex-1 flex flex-col min-h-screen transition-all duration-300 ease-in-out"
            :class="collapsed ? 'ml-20' : 'ml-64'">

            {{-- Top Navbar --}}
            <header
                class="h-20 bg-white/80 backdrop-blur-md sticky top-0 z-20 border-b border-gray-100 px-8 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <button @click="toggle()"
                        class="p-2 rounded-lg hover:bg-gray-100 text-gray-400 transition-colors">
                        <svg x-show="!collapsed" class="w-6 h-6" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h7" />
                        </svg>
                        <svg x-show="collapsed" class="w-6 h-6" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h2 class="text-gray-400 text-sm font-medium">
                        Welcome back, <span
                            class="text-gray-800 font-bold">{{ explode('@', auth()->user()->email)[0] }}</span>
                    </h2>
                </div>

                <div class="flex items-center gap-4">
                    <div
                        class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center border border-gray-200">
                        <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
            </header>

            {{-- Content Area --}}
            <div class="p-4">
                @if (session('success'))
                    <div
                        class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-700 flex items-center gap-3 animate-fade-in">
                        <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                @if ($errors->any())
                    <div
                        class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-100 text-rose-700 flex items-center gap-3">
                        <svg class="w-5 h-5 text-rose-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm font-medium">{{ $errors->first() }}</span>
                    </div>
                @endif

                <div class="p-2 ">
                    @yield('content')
                </div>
            </div>

            {{-- Footer --}}
            <footer class="mt-auto p-8 text-center text-gray-400 text-xs">
                &copy; {{ date('Y') }} CompanyName Admin Portal. All rights reserved.
            </footer>
        </main>
    </div>

    @stack('scripts')
</body>

</html>
