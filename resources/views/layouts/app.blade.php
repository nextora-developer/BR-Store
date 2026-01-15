<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Ecommerce') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen flex flex-col bg-[#FAF9F6]">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        @php
            $hasMobileTab = !request()->routeIs('login', 'register');
        @endphp

        <!-- Page Content -->
        <main class="flex-1">
            {{ $slot }}
        </main>
    </div>

    {{-- Global Footer --}}
    <footer
        class="relative overflow-hidden bg-black text-white border-t border-white/10
    {{ $hasMobileTab ? 'pb-24 lg:pb-0' : '' }}">
        {{-- Soft premium background glow --}}
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute -top-32 -left-40 w-[520px] h-[520px] rounded-full bg-[#D4AF37]/10 blur-3xl"></div>
            <div class="absolute -bottom-40 -right-40 w-[620px] h-[620px] rounded-full bg-white/5 blur-3xl"></div>
            <div class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-white/15 to-transparent">
            </div>
        </div>

        <div class="relative max-w-7xl5 mx-auto px-6 lg:px-12 py-16 lg:py-20">

            {{-- Top Grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-y-12 lg:gap-y-0 lg:gap-x-12">

                {{-- Brand --}}
                <div class="lg:col-span-4">
                    <div class="flex items-center gap-3 mb-6">
                        <img src="{{ asset('images/logo.png') }}" alt="BR Store"
                            class="h-10 w-10 rounded-2xl object-cover shadow-lg shadow-[#D4AF37]/20" />

                        <div class="flex flex-col leading-tight">
                            <span class="text-xl font-bold tracking-tight text-white">BRIF</span>
                            <span class="text-xs text-gray-400 tracking-wide">Premium Essentials</span>
                        </div>
                    </div>

                    <p class="text-sm text-gray-300 leading-relaxed max-w-sm">
                        Curating high-quality essentials for the modern Malaysian lifestyle.
                        Excellence in every detail, delivered with care.
                    </p>

                    {{-- Small brand badge --}}
                    <div class="mt-6">
                        <span
                            class="inline-flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest
                               text-[#D4AF37] bg-white/5 border border-white/10 px-3 py-1 rounded-full">
                            Malaysia • Crafted with care
                        </span>
                    </div>
                </div>

                {{-- Explore --}}
                <div class="lg:col-span-2 lg:col-start-5">
                    <h4 class="text-white font-extrabold text-sm uppercase tracking-wider">
                        Explore
                    </h4>
                    <div class="h-px w-10 bg-white/15 mt-4 mb-6"></div>

                    <ul class="space-y-4">
                        @foreach ([['label' => 'Official Site', 'route' => 'https://brif.cloud'], ['label' => 'How to Order', 'route' => route('how-to-order')], ['label' => 'Faq', 'route' => route('faq')], ['label' => 'Voucher', 'route' => route('vouchers.index')]] as $item)
                            <li>
                                <a href="{{ $item['route'] }}"
                                    class="text-sm text-gray-300 hover:text-white transition relative inline-block
                      after:content-[''] after:absolute after:left-0 after:-bottom-1
                      after:h-px after:w-0 after:bg-[#D4AF37] hover:after:w-full
                      after:transition-all after:duration-300">
                                    {{ $item['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>

                </div>

                {{-- Support --}}
                <div class="lg:col-span-2">
                    <h4 class="text-white font-extrabold text-sm uppercase tracking-wider">
                        Support
                    </h4>
                    <div class="h-px w-10 bg-white/15 mt-4 mb-6"></div>

                    <ul class="space-y-4">
                        @foreach ([['label' => 'Privacy Policy', 'route' => route('privacy')], ['label' => 'Shipping & Delivery', 'route' => route('shipping')], ['label' => 'Returns & Refunds', 'route' => route('returns')], ['label' => 'Terms of Service', 'route' => route('terms')]] as $item)
                            <li>
                                <a href="{{ $item['route'] }}"
                                    class="text-sm text-gray-300 hover:text-white transition relative inline-block
                          after:content-[''] after:absolute after:left-0 after:-bottom-1
                          after:h-px after:w-0 after:bg-[#D4AF37] hover:after:w-full
                          after:transition-all after:duration-300">
                                    {{ $item['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>


                {{-- Account --}}
                <div class="lg:col-span-2">
                    <h4 class="text-white font-extrabold text-sm uppercase tracking-wider">
                        Account
                    </h4>
                    <div class="h-px w-10 bg-white/15 mt-4 mb-6"></div>

                    <ul class="space-y-4">
                        <li>
                            <a href="{{ route('account.profile.edit') }}"
                                class="text-sm text-gray-300 hover:text-white transition relative inline-block
                                  after:content-[''] after:absolute after:left-0 after:-bottom-1
                                  after:h-px after:w-0 after:bg-[#D4AF37] hover:after:w-full
                                  after:transition-all after:duration-300">
                                Profile Details
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('account.orders.index') }}"
                                class="text-sm text-gray-300 hover:text-white transition relative inline-block
                                  after:content-[''] after:absolute after:left-0 after:-bottom-1
                                  after:h-px after:w-0 after:bg-[#D4AF37] hover:after:w-full
                                  after:transition-all after:duration-300">
                                Order History
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('account.favorites.index') }}"
                                class="text-sm text-gray-300 hover:text-white transition relative inline-block
                                  after:content-[''] after:absolute after:left-0 after:-bottom-1
                                  after:h-px after:w-0 after:bg-[#D4AF37] hover:after:w-full
                                  after:transition-all after:duration-300">
                                Wishlist
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('account.address.index') }}"
                                class="text-sm text-gray-300 hover:text-white transition relative inline-block
                                  after:content-[''] after:absolute after:left-0 after:-bottom-1
                                  after:h-px after:w-0 after:bg-[#D4AF37] hover:after:w-full
                                  after:transition-all after:duration-300">
                                Addresses
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Social Media --}}
                <div class="lg:col-span-2">
                    <h4 class="text-white font-extrabold text-sm uppercase tracking-wider">
                        Social Media
                    </h4>
                    <div class="h-px w-10 bg-white/15 mt-4 mb-6"></div>

                    <ul class="space-y-4">
                        <li>
                            <a href="https://www.instagram.com/brinnovatefuture?igsh=eHgzM2prcHN2MWF2"
                                class="text-sm text-gray-300 hover:text-white transition relative inline-block
                                  after:content-[''] after:absolute after:left-0 after:-bottom-1
                                  after:h-px after:w-0 after:bg-[#D4AF37] hover:after:w-full
                                  after:transition-all after:duration-300">
                                Instagram
                            </a>
                        </li>
                        <li>
                            <a href="https://www.facebook.com/share/17CB8CgQRN/?mibextid=wwXIfr"
                                class="text-sm text-gray-300 hover:text-white transition relative inline-block
                                  after:content-[''] after:absolute after:left-0 after:-bottom-1
                                  after:h-px after:w-0 after:bg-[#D4AF37] hover:after:w-full
                                  after:transition-all after:duration-300">
                                Facebook
                            </a>
                        </li>
                        <li>
                            <a href="https://wa.me/60123011610"
                                class="text-sm text-gray-300 hover:text-white transition relative inline-block
                                  after:content-[''] after:absolute after:left-0 after:-bottom-1
                                  after:h-px after:w-0 after:bg-[#D4AF37] hover:after:w-full
                                  after:transition-all after:duration-300">
                                WhatsApp
                            </a>
                        </li>

                        <li class="text-sm text-gray-400">
                            <a href="mailto:cs@brinnovatefuture.com" class="hover:text-white transition">
                                cs@brinnovatefuture.com
                            </a>
                        </li>
                    </ul>
                </div>

            </div>

            {{-- Bottom Bar --}}
            <div
                class="mt-14 pt-8 border-t border-white/10 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-sm text-gray-400 font-medium">
                    © {{ date('Y') }} Shop. Built with pride in Malaysia.
                </p>

                <div class="flex items-center gap-4">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                        Secure Payments
                    </span>

                    <div class="flex items-center gap-4">
                        <img src="/images/payments/fpx.png" alt="FPX"
                            class="h-5 opacity-70 grayscale hover:opacity-100 hover:grayscale-0 transition" />

                        <img src="/images/payments/visa.png" alt="Visa"
                            class="h-5 opacity-70 grayscale hover:opacity-100 hover:grayscale-0 transition" />

                        <img src="/images/payments/mastercard.png" alt="Mastercard"
                            class="h-5 opacity-70 grayscale hover:opacity-100 hover:grayscale-0 transition" />
                    </div>
                </div>
            </div>

        </div>
    </footer>

    {{-- Back to Top Button --}}
    <button id="backToTopBtn"
        class="hidden fixed right-4 bottom-24 z-50
           w-12 h-12 rounded-full
           bg-[#D4AF37] text-white
           flex items-center justify-center
           shadow-lg shadow-[#D4AF37]/40
           hover:bg-[#c49a2f] transition-all duration-300">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
            stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    </button>

    <a href="https://wa.me/60123011610" target="_blank"
        class="hidden lg:flex
          fixed right-4 bottom-4 z-50
          w-12 h-12 rounded-full
          bg-[#25D366] text-white
          items-center justify-center
          shadow-lg shadow-[#25D366]/40
          hover:bg-[#1DA851] transition-all duration-300 animate-bounce">

        <svg class="w-7 h-7" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
        </svg>

    </a>

    <script>
        function refreshCartCount() {
            console.log('Refreshing cart count…');
            const badge = document.querySelector('[data-cart-count]');
            if (!badge) return;

            fetch("{{ route('cart.count') }}", {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (typeof data.count !== 'undefined') {
                        badge.textContent = data.count;
                    }
                })
                .catch(() => {});
        }

        document.addEventListener('DOMContentLoaded', refreshCartCount);

        window.addEventListener('pageshow', function(event) {
            const navEntries = performance.getEntriesByType('navigation');
            const navType = navEntries[0] ? navEntries[0].type : null;

            if (event.persisted || navType === 'back_forward') {
                refreshCartCount();
            }
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const btn = document.getElementById("backToTopBtn");

            // 显示 / 隐藏按钮
            window.addEventListener("scroll", () => {
                if (window.scrollY > 300) {
                    btn.classList.remove("hidden");
                } else {
                    btn.classList.add("hidden");
                }
            });

            // 回到顶部（平滑滚动）
            btn.addEventListener("click", () => {
                window.scrollTo({
                    top: 0,
                    behavior: "smooth"
                });
            });
        });
    </script>

    @if (session('success'))
        <script>
            (function() {
                const isMobile = window.matchMedia('(max-width: 640px)').matches;
                const message = @json(session('success'));

                if (!isMobile) {
                    // 💻 Desktop：原本 SweetAlert toast
                    Swal.fire({
                        icon: 'success',
                        title: message,
                        toast: true,
                        position: 'bottom-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                    });
                } else {
                    // 📱 Mobile：Bottom Snackbar
                    const bar = document.createElement('div');
                    bar.innerHTML = `
                    <div class="fixed left-1/2 bottom-24 -translate-x-1/2 z-[9999]
                                bg-black text-white
                                px-5 py-3 rounded-full
                                text-sm font-semibold
                                shadow-xl
                                flex items-center gap-2
                                animate-[fadeUp_.25s_ease-out]">
                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-400"></span>
                        ${message}
                    </div>
                `;

                    document.body.appendChild(bar);

                    setTimeout(() => {
                        bar.remove();
                    }, 2200);
                }
            })();
        </script>

        <style>
            @keyframes fadeUp {
                from {
                    opacity: 0;
                    transform: translate(-50%, 12px);
                }

                to {
                    opacity: 1;
                    transform: translate(-50%, 0);
                }
            }
        </style>
    @endif



    @if (session('cart_added'))
        <div id="cartAddedOverlay" class="fixed inset-0 z-[9999] flex items-center justify-center">

            {{-- 背景遮罩 --}}
            <div class="absolute inset-0 bg-black/40"></div>

            {{-- 中间提示卡 --}}
            <div
                class="relative bg-white rounded-3xl shadow-2xl px-8 py-6 w-[90%] max-w-sm text-center
                   animate-[fadeInUp_.25s_ease-out]">

                <div
                    class="mx-auto mb-4 w-14 h-14 rounded-full
                        bg-[#D4AF37]/15 flex items-center justify-center">
                    <svg class="w-7 h-7 text-[#8f6a10]" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                <h3 class="text-lg font-bold text-gray-900 mb-1">
                    Added to cart
                </h3>

                <p class="text-sm text-gray-500 mb-6">
                    Item has been added successfully
                </p>

                <div class="flex gap-3">
                    <a href="{{ route('cart.index') }}"
                        class="flex-1 py-3 rounded-full bg-[#D4AF37] text-white font-bold">
                        View Cart
                    </a>

                    <button onclick="closeCartAdded()"
                        class="flex-1 py-3 rounded-full border border-gray-200 text-gray-600 font-bold">
                        Continue
                    </button>
                </div>
            </div>
        </div>

        <script>
            function closeCartAdded() {
                document.getElementById('cartAddedOverlay')?.remove();
            }

            // 自动关闭（可调）
            setTimeout(closeCartAdded, 3000);
        </script>
    @endif

    @if (session('review_success'))
        @php
            $rev = session('review_success');
            $revTitle = $rev['title'] ?? 'Review submitted';
            $revText = $rev['text'] ?? 'Thanks for your feedback!';
            $revPts = (int) ($rev['points'] ?? 0);
        @endphp

        <div id="reviewSuccessOverlay" class="fixed inset-0 z-[9999] flex items-center justify-center">
            {{-- 背景遮罩 --}}
            <div class="absolute inset-0 bg-black/40" onclick="closeReviewSuccess()"></div>

            {{-- 中间提示卡 --}}
            <div
                class="relative bg-white rounded-3xl shadow-2xl px-8 py-6 w-[90%] max-w-sm text-center
                   animate-[fadeInUp_.25s_ease-out]">

                <div
                    class="mx-auto mb-4 w-14 h-14 rounded-full
                       bg-[#D4AF37]/15 flex items-center justify-center">
                    <svg class="w-7 h-7 text-[#8f6a10]" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                <h3 class="text-lg font-bold text-gray-900 mb-1">
                    {{ $revTitle }}
                </h3>

                <p class="text-sm text-gray-500 mb-3">
                    {{ $revText }}
                </p>

                {{-- Points pill --}}
                <div
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#D4AF37]/10 border border-[#D4AF37]/20 mb-6">
                    <svg class="w-4 h-4 text-[#8f6a10]" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" />
                    </svg>
                    <span class="text-sm font-black text-[#8f6a10]">
                        +{{ $revPts }} pts
                    </span>
                </div>

                <div class="flex gap-3">
                    {{-- 你可以换成你自己的 route --}}
                    <a href="{{ route('account.reviews.index') }}"
                        class="flex-1 py-3 rounded-full bg-[#D4AF37] text-white font-bold">
                        View Reviews
                    </a>

                    <button type="button" onclick="closeReviewSuccess()"
                        class="flex-1 py-3 rounded-full border border-gray-200 text-gray-600 font-bold">
                        Continue
                    </button>
                </div>

                {{-- 右上角关闭 --}}
                <button type="button" onclick="closeReviewSuccess()"
                    class="absolute top-3 right-3 w-10 h-10 rounded-full hover:bg-gray-50 flex items-center justify-center text-gray-400 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <script>
            function closeReviewSuccess() {
                document.getElementById('reviewSuccessOverlay')?.remove();
            }

            // 自动关闭（可调）
            setTimeout(closeReviewSuccess, 3000);
        </script>

        <style>
            @keyframes fadeInUp_ {
                from {
                    opacity: 0;
                    transform: translateY(12px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        </style>
    @endif


    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: '{{ session('error') }}',
            });
        </script>
    @endif

    @if ($popupBanner)
        <div id="popupBannerWrap" class="fixed inset-0 z-[9999] hidden items-center justify-center px-4">
            <div class="absolute inset-0 bg-black/60 pointer-events-none"></div>

            <div id="popupBannerCard"
                class="relative w-full max-w-[520px] rounded-2xl overflow-hidden shadow-2xl border border-white/20
                opacity-0 translate-y-3 scale-[0.98] transition-all duration-300">
                <button id="popupBannerClose"
                    class="absolute top-3 right-3 z-10 w-9 h-9 rounded-full bg-white grid place-items-center shadow">
                    ✕
                </button>

                <a href="{{ $popupBanner->link ?? '#' }}" class="block">
                    <img src="{{ asset('storage/' . $popupBanner->image) }}" class="w-full h-auto block"
                        alt="Popup Banner">
                </a>
            </div>
        </div>

        <script>
            (function() {
                const bannerId = "{{ $popupBanner->id }}";
                const days = {{ (int) $popupBanner->cooldown_days }};
                const key = "popup_banner_next_" + bannerId;

                const wrap = document.getElementById("popupBannerWrap");
                const card = document.getElementById("popupBannerCard");
                const btn = document.getElementById("popupBannerClose");
                if (!wrap || !card || !btn) return;

                const now = Date.now();
                const next = parseInt(localStorage.getItem(key) || "0", 10);

                function openPopup() {
                    wrap.classList.remove("hidden");
                    wrap.classList.add("flex");
                    requestAnimationFrame(() => {
                        card.classList.remove("opacity-0", "translate-y-3", "scale-[0.98]");
                        card.classList.add("opacity-100", "translate-y-0", "scale-100");
                    });
                }

                function closePopup() {
                    card.classList.remove("opacity-100", "translate-y-0", "scale-100");
                    card.classList.add("opacity-0", "translate-y-3", "scale-[0.98]");

                    const cooldown = now + (days * 24 * 60 * 60 * 1000);
                    localStorage.setItem(key, String(cooldown));

                    setTimeout(() => {
                        wrap.remove();
                    }, 200);
                }

                if (!next || now >= next) openPopup();

                btn.addEventListener("click", closePopup);
                wrap.addEventListener("click", (e) => {
                    if (e.target === wrap) closePopup();
                });
                document.addEventListener("keydown", (e) => {
                    if (e.key === "Escape") closePopup();
                });
            })();
        </script>
    @endif


    @stack('scripts')
</body>

</html>
