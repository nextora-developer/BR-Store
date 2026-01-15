<x-app-layout>
    <div class="bg-[#FAF9F6] min-h-screen py-10">
        <div class="max-w-7xl5 mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-xs font-medium uppercase tracking-widest text-gray-400 mb-8">
                <a href="{{ route('home') }}" class="hover:text-[#8f6a10] transition-colors">Home</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M9 5l7 7-7 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span class="text-gray-900">Reviews</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

                {{-- Left Sidebar --}}
                <aside class="hidden lg:block lg:col-span-1">
                    @include('account.partials.sidebar')
                </aside>

                {{-- Right Content --}}
                <main class="lg:col-span-3 space-y-6" x-data="reviewModal()">

                    {{-- Header --}}
                    <section class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-gray-50">
                            <h1 class="text-2xl font-black text-gray-900">Product Reviews</h1>
                            <p class="text-sm text-gray-500 mt-1">
                                Review products from completed orders to earn points.
                            </p>
                        </div>

                        <div class="p-6">
                            @if (session('status'))
                                <div
                                    class="mb-4 px-4 py-3 rounded-2xl bg-amber-50 text-amber-800 border border-amber-200 text-sm">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <div class="text-xs text-gray-400 uppercase tracking-widest font-bold">
                                Pending reviews: <span class="text-gray-900">{{ $items->total() }}</span>
                            </div>
                        </div>
                    </section>

                    {{-- Pending Review List --}}
                    <div class="space-y-4">
                        @forelse ($items as $item)
                            @php
                                $thumb =
                                    $item->product && $item->product->image
                                        ? asset('storage/' . $item->product->image)
                                        : null;
                            @endphp

                            <div
                                class="group block bg-white rounded-3xl border border-gray-100 p-5 hover:border-[#D4AF37]/40 hover:shadow-xl hover:shadow-orange-100/30 transition-all duration-500">

                                <div class="flex flex-col md:flex-row md:items-center gap-6">
                                    {{-- Thumb --}}
                                    <div class="relative flex-shrink-0">
                                        <div
                                            class="w-20 h-20 rounded-2xl overflow-hidden bg-gray-50 border border-gray-100 shadow-sm">
                                            @if ($thumb)
                                                <img src="{{ $thumb }}"
                                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                            @else
                                                <div
                                                    class="w-full h-full flex items-center justify-center text-gray-300">
                                                    <svg class="w-8 h-8" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path
                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Details --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-3 mb-1">
                                            <span
                                                class="text-sm font-black text-gray-900 group-hover:text-[#8f6a10] transition-colors">
                                                {{ $item->product->name ?? 'Product' }}
                                            </span>
                                            <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                            <span class="text-xs font-medium text-gray-400 uppercase tracking-tighter">
                                                Order #{{ $item->order->order_no ?? $item->order_id }}
                                            </span>
                                        </div>

                                        <p class="text-xs text-gray-500 line-clamp-1">
                                            Completed on {{ $item->order->created_at->format('d M Y') }}
                                        </p>

                                        {{-- Badge --}}
                                        <span
                                            class="inline-flex items-center mt-3 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest border bg-emerald-50 text-emerald-700 border-emerald-100">
                                            completed
                                        </span>
                                    </div>

                                    {{-- Action --}}
                                    <div
                                        class="flex md:flex-col items-start md:items-end justify-between gap-2 border-t md:border-t-0 pt-4 md:pt-0 border-gray-50 w-full md:w-auto">
                                        <div
                                            class="text-xs font-black text-[#8f6a10] bg-[#fcfaf6] border border-[#D4AF37]/20 px-3 py-1.5 rounded-full">
                                            +20 pts
                                        </div>

                                        <button type="button"
                                            @click="open({
                                                itemId: {{ $item->id }},
                                                productName: @js($item->product->name ?? 'Product'),
                                                orderNo: @js($item->order->order_no ?? $item->order_id),
                                                actionUrl: @js(route('account.reviews.store', $item)),
                                            })"
                                            class="mt-2 md:mt-3 inline-flex items-center justify-center px-5 py-2.5 rounded-2xl
                                                   bg-[#D4AF37] text-white text-xs font-black uppercase tracking-widest
                                                   hover:bg-[#EBCB5A] transition-colors shadow-lg shadow-orange-900/10 w-full md:w-auto">
                                            Write Review
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="bg-white rounded-3xl border border-dashed border-gray-200 p-12 text-center">
                                <div
                                    class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 text-gray-300 mb-4">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M9 11h6m-6 4h6m-7-9h8a2 2 0 012 2v12a2 2 0 01-2 2H8a2 2 0 01-2-2V8a2 2 0 012-2z"
                                            stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                                <h3 class="text-gray-900 font-bold">No pending reviews</h3>
                                <p class="text-sm text-gray-500 mt-1">Complete an order to review products.</p>
                            </div>
                        @endforelse

                        <div class="mt-8">
                            {{ $items->links() }}
                        </div>
                    </div>

                    {{-- Modal --}}
                    <div x-show="isOpen" x-transition.opacity
                        class="fixed inset-0 z-50 flex items-end sm:items-center justify-center" style="display:none">

                        {{-- Backdrop --}}
                        <div class="absolute inset-0 bg-black/40" @click="close()"></div>

                        {{-- Panel --}}
                        <div x-transition
                            class="relative w-full sm:max-w-lg bg-white rounded-t-3xl sm:rounded-3xl border border-gray-100 shadow-2xl overflow-hidden">
                            <div class="p-6 border-b border-gray-50">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="text-xs uppercase tracking-widest font-bold text-gray-400">
                                            Order <span class="text-gray-900">#<span x-text="orderNo"></span></span>
                                        </div>
                                        <h3 class="text-xl font-black text-gray-900 mt-1" x-text="productName"></h3>
                                        <p class="text-sm text-gray-500 mt-1">Rate this product and earn <span
                                                class="font-bold text-[#8f6a10]">+20 pts</span>.</p>
                                    </div>

                                    <button type="button" @click="close()"
                                        class="p-2 rounded-xl hover:bg-gray-50 text-gray-400 hover:text-gray-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path d="M6 18L18 6M6 6l12 12" stroke-width="2.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <form method="POST" :action="actionUrl" class="p-6 space-y-5">
                                @csrf

                                <input type="hidden" name="rating" :value="rating">

                                {{-- Stars --}}
                                <div>
                                    <div class="text-xs uppercase font-black tracking-widest text-gray-500 mb-2">Rating
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <template x-for="i in 5" :key="i">
                                            <button type="button" class="text-3xl leading-none transition"
                                                :class="rating >= i ? 'text-[#D4AF37]' : 'text-gray-300 hover:text-[#D4AF37]/60'"
                                                @click="rating = i" :aria-label="'Rate ' + i">
                                                ★
                                            </button>
                                        </template>
                                        <span class="ml-2 text-sm font-black text-gray-700"
                                            x-text="rating + '/5'"></span>
                                    </div>
                                    @error('rating')
                                        <div class="mt-2 text-xs text-red-600">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Comment --}}
                                <div>
                                    <div class="text-xs uppercase font-black tracking-widest text-gray-500 mb-2">
                                        Comment</div>
                                    <textarea name="comment" rows="4"
                                        class="w-full rounded-2xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm
                                               focus:bg-white focus:ring-2 focus:ring-[#D4AF37]/20 focus:border-[#D4AF37]/40 transition-all"
                                        placeholder="Share your experience..."></textarea>
                                    @error('comment')
                                        <div class="mt-2 text-xs text-red-600">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- Actions --}}
                                <div class="flex gap-2 pt-1">
                                    <button type="button" @click="close()"
                                        class="flex-1 rounded-2xl border border-gray-200 bg-white py-3 text-sm font-black text-gray-700 hover:bg-gray-50 transition">
                                        Cancel
                                    </button>

                                    <button type="submit"
                                        class="flex-1 rounded-2xl bg-[#D4AF37] text-white py-3 text-sm font-black
                                               hover:bg-[#EBCB5A] transition shadow-lg shadow-orange-900/10">
                                        Submit
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Alpine Component --}}
                    <script>
                        function reviewModal() {
                            return {
                                isOpen: false,
                                itemId: null,
                                productName: '',
                                orderNo: '',
                                actionUrl: '',
                                rating: 5,

                                open(payload) {
                                    this.itemId = payload.itemId;
                                    this.productName = payload.productName;
                                    this.orderNo = payload.orderNo;
                                    this.actionUrl = payload.actionUrl;
                                    this.rating = 5;
                                    this.isOpen = true;

                                    document.documentElement.classList.add('overflow-hidden');
                                },

                                close() {
                                    this.isOpen = false;
                                    document.documentElement.classList.remove('overflow-hidden');
                                }
                            }
                        }
                    </script>

                </main>
            </div>
        </div>
    </div>
</x-app-layout>
