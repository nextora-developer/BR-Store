<x-app-layout>
    <div class="bg-white">
        {{-- Banner：可滑动轮播，图片来自数据库 --}}
        <section class="w-full relative z-0 bg-[#FAF9F6]" data-banner-slider>
            <div class="max-w-7xl5 mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-10 pt-5">
                <div class="relative rounded-3xl overflow-hidden shadow-[0_18px_40px_rgba(0,0,0,0.25)]">

                    @if (isset($banners) && $banners->count())
                        {{-- 用固定比例，避免不同 breakpoint 高度不一样导致裁切不同 --}}
                        <div class="relative w-full aspect-[21/10] sm:aspect-[21/9] lg:aspect-auto lg:h-[420px]">
                            {{-- 轨道 --}}
                            <div class="absolute inset-0 flex h-full transition-transform duration-700 ease-out"
                                data-banner-track>
                                @foreach ($banners as $banner)
                                    @php
                                        $url = $banner->link_url ?: route('shop.index');
                                    @endphp

                                    <a href="{{ $url }}" class="relative w-full h-full shrink-0 block group">
                                        <img src="{{ asset('storage/' . $banner->image_path) }}" alt="Banner"
                                            class="w-full h-full object-cover object-center block">
                                    </a>
                                @endforeach
                            </div>

                            {{-- 左右箭头 --}}
                            @if ($banners->count() > 1)
                                <button type="button"
                                    class="hidden sm:flex absolute left-4 top-1/2 -translate-y-1/2
                                   w-9 h-9 rounded-full bg-black/45 hover:bg-black/70
                                   text-white items-center justify-center text-sm"
                                    data-banner-prev>
                                    ‹
                                </button>

                                <button type="button"
                                    class="hidden sm:flex absolute right-4 top-1/2 -translate-y-1/2
                                   w-9 h-9 rounded-full bg-black/45 hover:bg-black/70
                                   text-white items-center justify-center text-sm"
                                    data-banner-next>
                                    ›
                                </button>

                                {{-- 小点点 --}}
                                <div class="absolute bottom-4 left-0 right-0 flex justify-center gap-2"
                                    data-banner-dots>
                                    @foreach ($banners as $index => $banner)
                                        <button type="button"
                                            class="w-2.5 h-2.5 rounded-full bg-white/40 hover:bg-white/80 transition"
                                            data-banner-dot="{{ $index }}"></button>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @else
                        {{-- 没有 banner 的时候显示一个占位背景 --}}
                        <div
                            class="w-full aspect-[21/10] sm:aspect-[21/9] lg:aspect-auto lg:h-[420px] bg-[#F5F5F7] flex items-center justify-center rounded-3xl">
                            <p class="text-gray-400 text-sm">Shop Banner coming soon</p>
                        </div>
                    @endif

                </div>
            </div>
        </section>


        {{-- Category 区块 --}}
        <section id="categories" class="bg-[#FAF9F6]">
            <div class="max-w-7xl5 mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-10">
                {{-- <div class="flex items-center justify-between mb-4 sm:mb-6">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-semibold text-gray-900">
                            Shop by category
                        </h2>
                        <p class="mt-1 text-sm text-gray-500">
                            Browse Shop by product category.
                        </p>
                    </div>
                </div> --}}

                @if (isset($categories) && $categories->count())
                    {{-- 横向滑动 + 隐藏 scrollbar + 鼠标拖动 --}}
                    <div class="overflow-x-auto scrollbar-hide cursor-grab select-none" data-scroll-x>
                        <div class="flex gap-4 md:gap-5 min-w-max py-1">
                            @foreach ($categories as $category)
                                <a href="{{ route('shop.index', ['category' => $category->slug]) }}"
                                    class="group shrink-0 w-[130px] md:w-[150px]
                                            rounded-2xl overflow-hidden
                                            bg-white border border-gray-100
                                            shadow-sm hover:shadow-md
                                            hover:border-[#D4AF37]/60 transition">

                                    {{-- 图片区域：直接吃满，没有灰色 --}}
                                    <div class="aspect-square relative bg-white">
                                        @if ($category->icon)
                                            <img src="{{ asset('storage/' . $category->icon) }}"
                                                alt="{{ $category->name }}"
                                                class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                        @else
                                            <div
                                                class="absolute inset-0 flex items-center justify-center text-xs text-gray-400">
                                                No image
                                            </div>
                                        @endif
                                    </div>

                                    {{-- 底部白色标题区 --}}
                                    <div class="px-3 py-2 border-t border-gray-100 bg-white">
                                        <div class="text-sm font-semibold text-gray-900 text-center line-clamp-2">
                                            {{ $category->name }}
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div
                        class="flex flex-col items-center justify-center border border-dashed border-gray-300 rounded-2xl bg-gray-50 py-10">
                        <p class="text-sm text-gray-500">
                            No categories yet. Add categories in admin to show them here.
                        </p>
                    </div>
                @endif

            </div>
        </section>


        {{-- New Arrivals products --}}
        <section id="featured" class="bg-[#FAF9F6]">
            <div class="max-w-7xl5 mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">

                {{-- Header Section --}}
                <div class="flex items-end justify-between mb-8">
                    <div class="space-y-1">
                        <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900">
                            New Arrivals
                        </h2>
                        <p class="text-sm text-gray-500 max-w-md">
                            Hand-picked treasures and our latest additions to the collection.
                        </p>
                    </div>

                    <a href="{{ route('shop.index') }}"
                        class="hidden sm:inline-flex items-center text-sm font-semibold text-[#8f6a10] hover:text-[#D4AF37] transition-colors group">
                        Browse all products
                        <svg xmlns="http://www.w3.org/2000/svg"
                            class="ml-1.5 h-4 w-4 transform group-hover:translate-x-1 transition-transform"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12l-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>

                @if ($featured->count())
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 sm:gap-6">
                        @foreach ($featured as $product)
                            <div
                                class="group relative flex flex-col bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl hover:border-[#D4AF37]/40 transition-all duration-500 overflow-hidden">

                                {{-- Image Wrapper --}}
                                <a href="{{ route('shop.show', $product->slug) }}"
                                    class="relative aspect-square overflow-hidden bg-gray-50">
                                    @if ($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                            class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700 ease-out">
                                    @else
                                        <div
                                            class="w-full h-full flex items-center justify-center bg-gray-50 text-[10px] uppercase tracking-widest text-gray-400">
                                            No Image Available
                                        </div>
                                    @endif

                                    {{-- Subtle Overlay --}}
                                    <div
                                        class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition-colors duration-500">
                                    </div>
                                </a>

                                {{-- ❤️ Favorite Button (Logic remains same, styling updated) --}}
                                @auth
                                    @php
                                        $isFavorited = auth()->user()->favorites->contains('product_id', $product->id);
                                    @endphp
                                    <form
                                        action="{{ $isFavorited ? route('account.favorites.destroy', $product) : route('account.favorites.store', $product) }}"
                                        method="POST" class="absolute top-3 right-3 z-10">
                                        @csrf
                                        @if ($isFavorited)
                                            @method('DELETE')
                                        @endif
                                        <button type="submit"
                                            class="w-9 h-9 flex items-center justify-center rounded-full bg-white/90 backdrop-blur-sm text-[#8f6a10] shadow-sm hover:bg-white hover:scale-110 transition-all active:scale-95">
                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                fill="{{ $isFavorited ? '#D4AF37' : 'none' }}"
                                                stroke="{{ $isFavorited ? '#D4AF37' : 'currentColor' }}" stroke-width="1.8"
                                                viewBox="0 0 24 24" class="h-5 w-5">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                            </svg>
                                        </button>
                                    </form>
                                @endauth

                                {{-- Product Content --}}
                                <div class="flex-1 flex flex-col p-4">
                                    <a href="{{ route('shop.show', $product->slug) }}"
                                        class="block flex-1 group/title">
                                        <p
                                            class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#D4AF37] mb-1.5">
                                            {{ $product->category->name ?? 'General' }}
                                        </p>
                                        <h3
                                            class="text-sm font-semibold text-gray-900 line-clamp-2 group-hover/title:text-[#8f6a10] transition-colors leading-snug">
                                            {{ $product->name }}
                                        </h3>
                                    </a>

                                    <div class="mt-4 flex flex-col gap-3">
                                        {{-- Price Logic --}}
                                        <p class="text-base font-bold text-gray-900">
                                            @if ($product->has_variants && $product->variants->count())
                                                @php
                                                    $prices = $product->variants->pluck('price')->filter();
                                                    $min = $prices->min();
                                                    $max = $prices->max();
                                                @endphp
                                                @if ($min == $max)
                                                    RM {{ number_format($min, 2) }}
                                                @else
                                                    <span
                                                        class="text-[10px] font-medium text-gray-400 uppercase align-middle mr-1">From</span>RM
                                                    {{ number_format($min, 2) }}
                                                @endif
                                            @else
                                                RM {{ number_format($product->price ?? 0, 2) }}
                                            @endif
                                        </p>

                                        {{-- Action Button --}}
                                        <a href="{{ route('shop.show', $product->slug) }}"
                                            class="w-full inline-flex items-center justify-center rounded-xl bg-gray-50 border border-gray-200 py-2.5 text-xs font-bold text-gray-700 hover:bg-[#D4AF37] hover:text-white hover:border-[#D4AF37] transition-all duration-300">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    {{-- Enhanced Empty State --}}
                    <div
                        class="flex flex-col items-center justify-center border-2 border-dashed border-gray-200 rounded-3xl bg-white/50 py-16 px-4">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <p class="text-gray-500 font-medium text-center">Our newest treasures are still being polished.
                        </p>
                        <a href="{{ route('shop.index') }}"
                            class="mt-4 text-sm font-bold text-[#8f6a10] underline underline-offset-4">Check back
                            soon</a>
                    </div>
                @endif
            </div>
        </section>

        {{-- Trust & Value Section --}}
        <section class="relative overflow-hidden bg-[#F5F0E8] border-y border-black/10">
            {{-- Decorative glows (premium, subtle) --}}
            <div class="absolute inset-0 pointer-events-none">
                <div class="absolute -top-32 -left-40 w-[520px] h-[520px] rounded-full bg-[#D4AF37]/15 blur-3xl"></div>
                <div class="absolute -bottom-40 -right-40 w-[620px] h-[620px] rounded-full bg-black/5 blur-3xl"></div>
                <div
                    class="absolute inset-x-0 top-0 h-px bg-gradient-to-r from-transparent via-black/10 to-transparent">
                </div>
                <div
                    class="absolute inset-x-0 bottom-0 h-px bg-gradient-to-r from-transparent via-black/10 to-transparent">
                </div>
            </div>

            <div class="relative max-w-7xl5 mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">

                {{-- Section heading (adds hierarchy so it feels designed, not just 4 blocks) --}}
                <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-8 lg:mb-10">
                    <div>
                        <h3 class="mt-2 text-2xl lg:text-3xl font-black tracking-tight text-black">
                            Built for confidence, every order
                        </h3>
                    </div>

                </div>

                {{-- Cards --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">

                    {{-- Card 1 --}}
                    <div
                        class="group rounded-3xl bg-white/55 backdrop-blur-sm border border-black/5 p-6
                       hover:bg-white/70 hover:border-black/10 hover:-translate-y-0.5
                       transition-all duration-300">
                        <div class="flex items-start gap-4">
                            <div
                                class="flex-shrink-0 w-12 h-12 rounded-2xl flex items-center justify-center
                               bg-[#D4AF37]/15 text-[#8f6a10]
                               group-hover:bg-[#D4AF37] group-hover:text-black
                               transition-all duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.129-1.125V11.25c0-4.446-3.51-8.11-8.048-8.11h-.852a4.482 4.482 0 00-4.488 4.488v2.602M19.5 14.25l-2.25-6.75m-10.5 6.75h12.75" />
                                </svg>
                            </div>

                            <div class="min-w-0">
                                <h4 class="text-sm font-extrabold text-black tracking-tight">
                                    Fast Delivery
                                </h4>
                                <p class="mt-1 text-sm text-black/60 leading-relaxed">
                                    Shipped within <span class="font-semibold text-black/80">1–3 working days</span>.
                                </p>

                                <div class="mt-4">
                                    <span
                                        class="inline-flex items-center gap-2 text-[11px] font-bold text-[#8f6a10]
                                       bg-[#D4AF37]/10 border border-[#D4AF37]/25 px-3 py-1 rounded-full">
                                        Dispatch guarantee
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card 2 --}}
                    <div
                        class="group rounded-3xl bg-white/55 backdrop-blur-sm border border-black/5 p-6
                       hover:bg-white/70 hover:border-black/10 hover:-translate-y-0.5
                       transition-all duration-300">
                        <div class="flex items-start gap-4">
                            <div
                                class="flex-shrink-0 w-12 h-12 rounded-2xl flex items-center justify-center
                               bg-[#D4AF37]/15 text-[#8f6a10]
                               group-hover:bg-[#D4AF37] group-hover:text-black
                               transition-all duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.744c0 5.052 3.823 9.213 8.712 9.637.222.019.447.029.672.029.224 0 .449-.01.671-.029 4.89-.423 8.713-4.585 8.713-9.637 0-1.305-.209-2.56-.598-3.744A11.959 11.959 0 0112 2.714z" />
                                </svg>
                            </div>

                            <div class="min-w-0">
                                <h4 class="text-sm font-extrabold text-black tracking-tight">
                                    Secure Checkout
                                </h4>
                                <p class="mt-1 text-sm text-black/60 leading-relaxed">
                                    Protected by <span class="font-semibold text-black/80">secure encryption</span>.
                                </p>

                                <div class="mt-4">
                                    <span
                                        class="inline-flex items-center gap-2 text-[11px] font-bold text-[#8f6a10]
                                       bg-[#D4AF37]/10 border border-[#D4AF37]/25 px-3 py-1 rounded-full">
                                        Trusted payment
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card 3 --}}
                    <div
                        class="group rounded-3xl bg-white/55 backdrop-blur-sm border border-black/5 p-6
                       hover:bg-white/70 hover:border-black/10 hover:-translate-y-0.5
                       transition-all duration-300">
                        <div class="flex items-start gap-4">
                            <div
                                class="flex-shrink-0 w-12 h-12 rounded-2xl flex items-center justify-center
                               bg-[#D4AF37]/15 text-[#8f6a10]
                               group-hover:bg-[#D4AF37] group-hover:text-black
                               transition-all duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                                </svg>
                            </div>

                            <div class="min-w-0">
                                <h4 class="text-sm font-extrabold text-black tracking-tight">
                                    Easy Returns
                                </h4>
                                <p class="mt-1 text-sm text-black/60 leading-relaxed">
                                    Hassle-free returns on <span class="font-semibold text-black/80">eligible
                                        items</span>.
                                </p>

                                <div class="mt-4">
                                    <span
                                        class="inline-flex items-center gap-2 text-[11px] font-bold text-[#8f6a10]
                                       bg-[#D4AF37]/10 border border-[#D4AF37]/25 px-3 py-1 rounded-full">
                                        Simple process
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card 4 --}}
                    <div
                        class="group rounded-3xl bg-white/55 backdrop-blur-sm border border-black/5 p-6
                       hover:bg-white/70 hover:border-black/10 hover:-translate-y-0.5
                       transition-all duration-300">
                        <div class="flex items-start gap-4">
                            <div
                                class="flex-shrink-0 w-12 h-12 rounded-2xl flex items-center justify-center
                               bg-[#D4AF37]/15 text-[#8f6a10]
                               group-hover:bg-[#D4AF37] group-hover:text-black
                               transition-all duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                </svg>
                            </div>

                            <div class="min-w-0">
                                <h4 class="text-sm font-extrabold text-black tracking-tight">
                                    Malaysian Seller
                                </h4>
                                <p class="mt-1 text-sm text-black/60 leading-relaxed">
                                    Proudly supporting <span class="font-semibold text-black/80">local
                                        community</span>.
                                </p>

                                <div class="mt-4">
                                    <span
                                        class="inline-flex items-center gap-2 text-[11px] font-bold text-[#8f6a10]
                                       bg-[#D4AF37]/10 border border-[#D4AF37]/25 px-3 py-1 rounded-full">
                                        Local & reliable
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>



        {{-- Bottom CTA Section --}}
        <section class="relative bg-[#FAF9F6] border-t border-black/5 overflow-hidden antialiased">
            {{-- Decorative Elements --}}
            <div
                class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-96 h-96 bg-[#D4AF37]/10 rounded-full blur-[100px]">
            </div>
            <div
                class="absolute bottom-0 left-0 translate-y-1/2 -translate-x-1/4 w-72 h-72 bg-[#D4AF37]/5 rounded-full blur-[80px]">
            </div>

            <div class="relative max-w-7xl5 mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24">
                <div
                    class="flex flex-col lg:flex-row items-center justify-between gap-10
                    bg-white/40 backdrop-blur-md
                    rounded-[2.5rem] p-10 md:p-16
                    border border-white/20 shadow-sm
                    hover:border-black/10 transition-colors duration-500">

                    <div class="text-center lg:text-left max-w-2xl">
                        <h3 class="text-2xl md:text-3xl lg:text-3xl font-bold text-black tracking-tight leading-tight">
                            Ready to elevate <br class="hidden sm:block lg:hidden" /> your lifestyle?
                        </h3>
                        <p class="mt-4 text-base md:text-base text-black/50 leading-relaxed font-medium">
                            Discover our full collection of premium essentials.
                            Quality, security, and elegance — delivered straight to your door.
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 shrink-0 w-full sm:w-auto">
                        {{-- Primary Button --}}
                        <a href="{{ route('shop.index') }}"
                            class="group inline-flex items-center justify-center px-10 py-4 rounded-full
                           text-sm font-black uppercase tracking-widest
                           bg-[#D4AF37] text-black
                           shadow-xl shadow-[#D4AF37]/20
                           hover:bg-black hover:text-white hover:-translate-y-1
                           transition-all duration-300">
                            Start Shopping
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="ml-2 h-4 w-4 transform transition-transform group-hover:translate-x-1"
                                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M14.25 7.75L18.5 12m0 0l-4.25 4.25M18.5 12H5.5" />
                            </svg>
                        </a>

                        {{-- Secondary Button --}}
                        <a href="#categories"
                            class="inline-flex items-center justify-center px-10 py-4 rounded-full
                           text-sm font-black uppercase tracking-widest
                           bg-white text-black
                           border border-black/5
                           hover:bg-black hover:text-white hover:border-black
                           transition-all duration-300">
                            View Categories
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slider = document.querySelector('[data-scroll-x]');
            if (!slider) return;

            let isDown = false;
            let startX = 0;
            let moved = false;

            // 鼠标按下
            slider.addEventListener('mousedown', function(e) {
                isDown = true;
                moved = false;
                slider.classList.add('cursor-grabbing');

                e.preventDefault();
                startX = e.clientX;
            });

            // 鼠标抬起 / 离开
            const stopDrag = () => {
                isDown = false;
                slider.classList.remove('cursor-grabbing');
            };

            slider.addEventListener('mouseup', stopDrag);
            slider.addEventListener('mouseleave', stopDrag);

            // 鼠标移动：增量拖动（每次用上一次的位置当参考，会比较顺）
            slider.addEventListener('mousemove', function(e) {
                if (!isDown) return;

                e.preventDefault();
                const x = e.clientX;
                const delta = x - startX;

                // 灵敏度：1.2 可以自己调（1.0 更稳，1.5 更敏感）
                slider.scrollLeft -= delta * 1.2;

                startX = x; // 更新起点，下一次从这里算
                if (Math.abs(delta) > 3) moved = true;
            });

            // 拖动时不要触发里面 a 的点击
            slider.addEventListener('click', function(e) {
                if (moved) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            }, true);

            // 滚轮 -> 横向滚动，稍微顺一点
            // slider.addEventListener('wheel', function(e) {
            //     if (Math.abs(e.deltaY) > Math.abs(e.deltaX)) {
            //         e.preventDefault();
            //         slider.scrollLeft += e.deltaY * 0.7;
            //     }
            // }, {
            //     passive: false
            // });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slider = document.querySelector('[data-banner-slider]');
            if (!slider) return;

            const track = slider.querySelector('[data-banner-track]');
            const slides = Array.from(track.children);
            const prevBtn = slider.querySelector('[data-banner-prev]');
            const nextBtn = slider.querySelector('[data-banner-next]');
            const dotsWrap = slider.querySelector('[data-banner-dots]');
            const dots = dotsWrap ? Array.from(dotsWrap.querySelectorAll('[data-banner-dot]')) : [];

            let index = 0;
            let autoTimer = null;

            function goTo(i) {
                if (!slides.length) return;
                index = (i + slides.length) % slides.length;
                track.style.transform = `translateX(-${index * 100}%)`;

                // 更新底部点
                dots.forEach((dot, idx) => {
                    if (idx === index) {
                        dot.classList.add('bg-white');
                        dot.classList.remove('bg-white/40');
                    } else {
                        dot.classList.remove('bg-white');
                        dot.classList.add('bg-white/40');
                    }
                });
            }

            function next() {
                goTo(index + 1);
            }

            function prev() {
                goTo(index - 1);
            }

            // 初始
            goTo(0);

            // 按钮
            if (prevBtn) prevBtn.addEventListener('click', () => {
                prev();
                restartAuto();
            });

            if (nextBtn) nextBtn.addEventListener('click', () => {
                next();
                restartAuto();
            });

            // 点点点击
            dots.forEach((dot, idx) => {
                dot.addEventListener('click', () => {
                    goTo(idx);
                    restartAuto();
                });
            });

            // Auto slide
            function startAuto() {
                if (autoTimer) clearInterval(autoTimer);
                autoTimer = setInterval(() => {
                    next();
                }, 5000); // 5 秒一张
            }

            function restartAuto() {
                startAuto();
            }

            startAuto();

            // Touch swipe 支持（手机左右划）
            let startX = null;
            let isTouchMoving = false;

            slider.addEventListener('touchstart', (e) => {
                if (!e.touches[0]) return;
                startX = e.touches[0].clientX;
                isTouchMoving = true;
            });

            slider.addEventListener('touchmove', (e) => {
                if (!isTouchMoving || startX === null) return;
                const currentX = e.touches[0].clientX;
                const diff = currentX - startX;

                // 不做实时拖动，只是记录 swipe 方向
                // 如要实时拖动可以改这里
            });

            slider.addEventListener('touchend', (e) => {
                if (!isTouchMoving || startX === null) return;
                const endX = e.changedTouches[0].clientX;
                const diff = endX - startX;

                if (Math.abs(diff) > 50) {
                    if (diff < 0) {
                        next();
                    } else {
                        prev();
                    }
                    restartAuto();
                }

                startX = null;
                isTouchMoving = false;
            });
        });
    </script>



</x-app-layout>
