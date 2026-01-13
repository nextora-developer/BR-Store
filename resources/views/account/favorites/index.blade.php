<x-app-layout>
    <div class="bg-[#FAF9F6] min-h-screen py-10">
        <div class="max-w-7xl5 mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-xs font-medium uppercase tracking-widest text-gray-400 mb-8">
                <a href="{{ route('home') }}" class="hover:text-[#8f6a10] transition-colors">Home</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M9 5l7 7-7 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span class="text-gray-900">Wishlist</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

                {{-- Left Sidebar --}}
                <aside class="hidden lg:block lg:col-span-1">
                    @include('account.partials.sidebar')
                </aside>

                {{-- Right Content --}}
                <main class="lg:col-span-3 space-y-6">

                    {{-- Header Section --}}
                    <section class="bg-white rounded-3xl border border-gray-100 shadow-sm p-8">
                        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                            <div>
                                <h1 class="text-3xl font-black text-gray-900 tracking-tight">Wishlist</h1>
                                <p class="text-gray-500 mt-2 max-w-md">
                                    A curated collection of items you love. Save them here and come back when you're
                                    ready.
                                </p>
                            </div>
                            @if ($favorites->total() > 0)
                                <div
                                    class="inline-flex items-center px-4 py-2 bg-gray-50 rounded-2xl border border-gray-100">
                                    <span class="text-sm font-bold text-gray-900">{{ $favorites->total() }}</span>
                                    <span
                                        class="ml-1.5 text-xs font-medium text-gray-500 uppercase tracking-wider">Items</span>
                                </div>
                            @endif
                        </div>
                    </section>

                    {{-- Favorites Grid --}}
                    @if ($favorites->isEmpty())
                        <section class="bg-white rounded-3xl border border-gray-100 shadow-sm p-12 text-center">
                            <div class="max-w-xs mx-auto">
                                <div
                                    class="w-16 h-16 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <svg class="w-8 h-8 " fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
                                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <h3 class="font-bold text-gray-900">Your wishlist is empty</h3>
                                <p class="text-gray-500 text-sm mt-1">Start exploring our collection and save your
                                    favorite
                                    pieces.</p>
                                <a href="{{ route('shop.index') }}"
                                    class="mt-6 inline-flex items-center px-6 py-2.5
                                                bg-black text-white text-sm font-bold rounded-xl
                                                transition-all duration-300 ease-out
                                                hover:bg-black hover:text-white
                                                hover:-translate-y-0.5 hover:scale-[1.03]
                                                hover:shadow-xl hover:shadow-gray/30">
                                    Start Shopping
                                </a>
                            </div>
                        </section>
                    @else
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
                            @foreach ($favorites as $favorite)
                                @php $product = $favorite->product; @endphp
                                @if ($product)
                                    <div
                                        class="group relative flex flex-col bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-xl hover:border-[#D4AF37]/40 transition-all duration-500 overflow-hidden">

                                        {{-- Image Wrapper --}}
                                        <a href="{{ route('shop.show', $product->slug) }}"
                                            class="relative aspect-square overflow-hidden bg-gray-50">
                                            @if ($product->image)
                                                <img src="{{ asset('storage/' . $product->image) }}"
                                                    alt="{{ $product->name }}" loading="lazy"
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

                                        {{-- ❤️ Favorite remove --}}
                                        <form action="{{ route('account.favorites.destroy', $product) }}" method="POST"
                                            class="absolute top-3 right-3 z-10" onclick="event.stopPropagation();">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                onclick="event.preventDefault(); event.stopPropagation(); this.closest('form').submit();"
                                                class="w-9 h-9 flex items-center justify-center rounded-full bg-white/90 backdrop-blur-sm text-[#8f6a10] shadow-sm hover:bg-white hover:scale-110 transition-all active:scale-95">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="#D4AF37" stroke="#D4AF37"
                                                    stroke-width="1.8" viewBox="0 0 24 24" class="h-5 w-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                                                </svg>
                                            </button>
                                        </form>

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
                                @endif
                            @endforeach
                        </div>


                        {{-- Pagination --}}
                        <div class="mt-12">
                            {{ $favorites->links() }}
                        </div>
                    @endif

                </main>
            </div>
        </div>
    </div>
</x-app-layout>
