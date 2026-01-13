{{-- New Arrivals products --}}
<section id="new-arrivals" class="bg-[#F9F7F2]">
    <div class="max-w-7xl5 mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-10">

        {{-- New Arrivals Header Section --}}
        <div class="flex flex-col sm:flex-row items-baseline justify-between gap-4 mb-10 lg:mb-12">
            <div class="space-y-2">

                <h2 class="text-3xl sm:text-3xl font-bold text-black">
                    New Arrivals
                </h2>

                <p class="text-sm md:text-base text-black/40 max-w-md font-medium leading-relaxed">
                    Hand-picked treasures and our latest additions to the collection, curated for the discerning eye.
                </p>
            </div>

            {{-- Refined CTA Link --}}
            <a href="{{ route('shop.index') }}"
                class="group inline-flex items-center text-[11px] font-bold uppercase tracking-[0.2em] text-black hover:text-[#D4AF37] transition-all duration-300">
                <span class="border-b border-black/10 group-hover:border-[#D4AF37] pb-1">
                    Browse All Products
                </span>
                <div
                    class="ml-3 w-8 h-8 rounded-full border border-black/5 flex items-center justify-center group-hover:bg-black group-hover:text-white transition-all duration-500">
                    <svg xmlns="http://www.w3.org/2000/svg"
                        class="h-3 w-3 transform group-hover:translate-x-0.5 transition-transform" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12l-7.5 7.5M21 12H3" />
                    </svg>
                </div>
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
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="{{ $isFavorited ? '#D4AF37' : 'none' }}"
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
