<x-app-layout>
    <div class="bg-[#FAF9F6] min-h-screen">
        <div class="max-w-7xl5 mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">

            {{-- Header + 小标题 --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-semibold text-gray-900">Shop</h1>
                    <p class="text-sm text-gray-500">
                        Browse Shop products and find what you need.
                    </p>
                </div>

                {{-- 总数 --}}
                <div class="text-xs sm:text-sm text-gray-500">
                    Showing <span class="font-semibold text-gray-800">{{ $products->total() }}</span> items
                </div>
            </div>

            {{-- Filter Bar --}}
            <form method="GET" action="{{ route('shop.index') }}"
                class="mb-6 bg-white border border-[#D4AF37]/18 rounded-2xl px-4 py-3 sm:px-5 sm:py-4 shadow-[0_10px_30px_rgba(0,0,0,0.06)]">

                <div class="flex flex-col md:flex-row md:items-center gap-3 md:gap-4">

                    {{-- Search --}}
                    <div class="flex-1">
                        <label class="block text-[11px] uppercase tracking-wide text-gray-400 mb-1">
                            Search
                        </label>
                        <div class="relative">
                            <input type="text" name="q" value="{{ request('q') }}"
                                placeholder="Search products..."
                                class="w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2 text-sm text-gray-700
                                        focus:border-[#D4AF37] focus:ring-[#D4AF37]/30 focus:outline-none">
                            <span class="absolute right-3 top-2.5 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m21 21-4.35-4.35M10.5 18a7.5 7.5 0 1 1 0-15 7.5 7.5 0 0 1 0 15z" />
                                </svg>
                            </span>
                        </div>
                    </div>

                    {{-- Category --}}
                    <div class="w-full md:w-56">
                        <label class="block text-[11px] uppercase tracking-wide text-gray-400 mb-1">
                            Category
                        </label>
                        <select name="category"
                            class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700
                                   focus:border-[#D4AF37] focus:ring-[#D4AF37]/30">
                            <option value="">All categories</option>
                            @isset($categories)
                                @foreach ($categories as $category)
                                    <option value="{{ $category->slug }}" @selected(request('category') === $category->slug)>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                    </div>

                    {{-- Sort --}}
                    <div class="w-full md:w-48">
                        <label class="block text-[11px] uppercase tracking-wide text-gray-400 mb-1">
                            Sort by
                        </label>
                        <select name="sort"
                            class="w-full rounded-xl border border-gray-200 bg-white px-3 py-2 text-sm text-gray-700
                                   focus:border-[#D4AF37] focus:ring-[#D4AF37]/30">
                            <option value="">Default</option>
                            <option value="latest" @selected(request('sort') === 'latest')>Latest</option>
                            <option value="price_asc" @selected(request('sort') === 'price_asc')>Price: Low → High</option>
                            <option value="price_desc" @selected(request('sort') === 'price_desc')>Price: High → Low</option>
                        </select>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex items-end gap-2 mt-4">
                        <button type="submit"
                            class="px-4 py-2 rounded-xl bg-[#D4AF37]/90 text-white text-sm font-semibold hover:bg-[#b8942f] transition">
                            Apply
                        </button>

                        <a href="{{ route('shop.index') }}"
                            class="px-4 py-2 rounded-xl border border-gray-200 text-sm sm:text-sm text-gray-600 hover:bg-gray-50 transition">
                            Reset
                        </a>
                    </div>

                </div>
            </form>

            {{-- Product Grid --}}
            @if ($products->count())
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4 sm:gap-6">
                    @foreach ($products as $product)
                        <div
                            class="group relative flex flex-col bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl hover:border-[#D4AF37]/40 transition-all duration-500 overflow-hidden">

                            {{-- Image Wrapper --}}
                            <a href="{{ route('shop.show', $product->slug) }}"
                                class="relative aspect-square overflow-hidden bg-gray-50">
                                @if ($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                        loading="lazy"
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

                            {{-- ❤️ Favorite Button --}}
                            @auth
                                @php
                                    $isFavorited = auth()->user()->favorites->contains('product_id', $product->id);
                                @endphp
                                <form
                                    action="{{ $isFavorited ? route('account.favorites.destroy', $product) : route('account.favorites.store', $product) }}"
                                    method="POST" class="absolute top-3 right-3 z-10" onclick="event.stopPropagation();">
                                    @csrf
                                    @if ($isFavorited)
                                        @method('DELETE')
                                    @endif

                                    <button type="submit"
                                        onclick="event.preventDefault(); event.stopPropagation(); this.closest('form').submit();"
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
                                <a href="{{ route('shop.show', $product->slug) }}" class="block flex-1 group/title">
                                    <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#D4AF37] mb-1.5">
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
                                                    class="text-[10px] font-medium text-gray-400 uppercase align-middle mr-1">
                                                    From
                                                </span>
                                                RM {{ number_format($min, 2) }}
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

                <div class="mt-8">
                    {{ $products->withQueryString()->links() }}
                </div>
            @else
                {{-- Empty State (match featured style) --}}
                <div
                    class="mt-8 flex flex-col items-center justify-center border-2 border-dashed border-gray-200 rounded-3xl bg-white/50 py-16 px-4">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <p class="text-gray-500 font-medium text-center">
                        No products found. Try adjusting your filters.
                    </p>
                    <a href="{{ route('shop.index') }}"
                        class="mt-4 text-sm font-bold text-[#8f6a10] underline underline-offset-4">
                        Back to shop
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
