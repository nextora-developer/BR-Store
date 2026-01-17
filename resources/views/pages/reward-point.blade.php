<x-app-layout>
    <section class="bg-[#FAF9F6] min-h-screen pb-24">

        {{-- Header Section --}}
        <div class="relative bg-white border-b border-gray-100 overflow-hidden">
            {{-- Decorative Element --}}
            <div class="absolute top-0 right-0 -mt-20 -mr-20 w-64 h-64 bg-[#D4AF37]/5 rounded-full blur-3xl"></div>

            <div class="max-w-7xl mx-auto px-6 py-20 text-center relative z-10">
                <span
                    class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-[#D4AF37]/10 border border-[#D4AF37]/20 text-[#8f6a10] text-[10px] font-bold uppercase tracking-[0.2em] mb-6">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                        </path>
                    </svg>
                    Loyalty Program
                </span>

                <h1 class="text-4xl sm:text-5xl font-black text-gray-900 tracking-tight mb-6">
                    Reward Points Program
                </h1>

                <p class="text-gray-500 max-w-2xl mx-auto text-lg leading-relaxed">
                    Our way of saying thank you. Earn points with every purchase and
                    unlock exclusive discounts on your future orders.
                </p>
            </div>
        </div>

        <div class="max-w-5xl mx-auto px-6 mt-16">

            {{-- Three Column Highlights --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
                {{-- Step 1 --}}
                <div
                    class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Shop & Earn</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">Automatically earn points for every eligible
                        purchase you make on our platform.</p>
                </div>

                {{-- Step 2 --}}
                <div
                    class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Order Completion</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">Points are credited directly to your account as
                        soon as your order is marked as completed.</p>
                </div>

                {{-- Step 3 --}}
                <div
                    class="bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-[#D4AF37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-2">Instant Savings</h3>
                    <p class="text-sm text-gray-500 leading-relaxed">Apply your points at checkout to reduce your total
                        price instantly.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-y-10 lg:gap-x-12">

                {{-- Left Side: Details --}}
                <div class="lg:col-span-3 space-y-10 lg:pr-2">
                    {{-- Conversion Rate Card --}}
                    <div class="bg-[#8f6a10] rounded-[2.5rem] p-10 text-white relative overflow-hidden group">
                        <div
                            class="absolute right-0 bottom-0 opacity-10 translate-x-1/4 translate-y-1/4 group-hover:scale-110 transition-transform duration-700">
                            <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM5.884 6.607a1 1 0 01-.226 1.396l-.867.65a1 1 0 11-1.17-1.622l.867-.65a1 1 0 011.396.226zM17.303 12.66c.456.447.47 1.18.023 1.636l-.65.667a1 1 0 11-1.432-1.394l.65-.667a1 1 0 011.41-.042zM4.134 15.586a1 1 0 011.396-.226l.867.65a1 1 0 11-1.17 1.622l-.867-.65a1 1 0 01-.226-1.396zM13 17a1 1 0 10-2 0v1a1 1 0 102 0v-1z">
                                </path>
                            </svg>
                        </div>
                        <h4 class="text-[#D4AF37] font-bold uppercase tracking-widest text-xs mb-2">Redemption Value
                        </h4>
                        <div class="flex items-baseline gap-4">
                            <span class="text-5xl font-black">100</span>
                            <span class="text-xl font-medium opacity-80">Points =</span>
                            <span class="text-5xl font-black text-[#D4AF37]">RM1</span>
                        </div>
                        <p class="mt-6 text-white/70 text-sm max-w-sm leading-relaxed">
                            Points can be redeemed in blocks of 100. There is no minimum point requirement to start
                            saving!
                        </p>
                    </div>

                    {{-- More Ways to Earn --}}
                    <div class="bg-white rounded-[2rem] border border-gray-100 p-8">
                        <h3 class="font-bold text-gray-900 mb-6 flex items-center gap-2">
                            <span class="w-1.5 h-6 bg-[#D4AF37] rounded-full"></span>
                            Bonus Opportunities
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex gap-4">
                                <div
                                    class="w-8 h-8 rounded-full bg-green-50 text-green-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.921-.755 1.688-1.54 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.784.57-1.838-.197-1.539-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">Review Items</h4>
                                    <p class="text-xs text-gray-500 mt-1">Get bonus points for every product review you
                                        submit.</p>
                                </div>
                            </div>
                            <div class="flex gap-4">
                                <div
                                    class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">Refer a Friend</h4>
                                    <p class="text-xs text-gray-500 mt-1">Share your link and earn when they make their
                                        first purchase.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Side: T&C --}}
                <div class="lg:col-span-2 lg:pl-2">
                    <div class="bg-white rounded-[2rem] border border-gray-100 p-8 sticky top-8">
                        <h3 class="font-bold text-gray-900 mb-6 uppercase text-xs tracking-widest">Program Policy</h3>
                        <ul class="space-y-4">
                            @foreach (['Points are issued at the company’s discretion.', 'Only completed and eligible orders earn points.', 'Points are non-transferable and non-refundable.', 'Cancelled orders will result in point deduction.', 'Company reserves the right to amend the program.'] as $index => $tc)
                                <li class="flex gap-4 items-start group">
                                    <span
                                        class="flex-shrink-0 w-6 h-6 rounded-lg bg-gray-50 text-gray-400 text-[10px] font-bold flex items-center justify-center group-hover:bg-[#D4AF37]/10 group-hover:text-[#8f6a10] transition-colors">
                                        0{{ $index + 1 }}
                                    </span>
                                    <p class="text-sm text-gray-600 leading-relaxed">{{ $tc }}</p>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            {{-- CTA: Start Earning Points --}}
            <div class="mt-12">
                <div class="relative overflow-hidden rounded-[2rem] bg-[#8f6a10] text-white px-8 py-10 shadow-xl">

                    {{-- Simplified Glow --}}
                    <div class="absolute top-0 right-0 w-64 h-64 bg-[#D4AF37]/20 rounded-full blur-[60px]"></div>

                    <div class="relative z-10 flex flex-col lg:flex-row items-center justify-between gap-8">

                        {{-- Text Content --}}
                        <div class="text-center lg:text-left max-w-lg">
                            <div
                                class="inline-flex items-center gap-2 mb-3 px-3 py-1 rounded-full bg-white/10 border border-white/10">
                                <span class="text-[9px] uppercase tracking-widest font-black">Member Benefits</span>
                            </div>

                            <h2 class="text-2xl sm:text-3xl font-black tracking-tight mb-2">
                                Turn Purchases into <span class="text-[#D4AF37]">Rewards</span>
                            </h2>

                            <p class="text-white/70 text-sm leading-relaxed">
                                Earn points with every order and enjoy instant savings on your next checkout.
                            </p>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex flex-wrap justify-center gap-3">
                            <a href="{{ route('shop.index') }}"
                                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white text-[#8f6a10] font-bold text-sm hover:bg-[#FAF9F6] transition-all shadow-lg active:scale-95">
                                Start Shopping
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>

                            <a href="{{ route('account.index') }}"
                                class="inline-flex items-center px-6 py-3 rounded-xl border border-white/20 text-white text-sm font-bold hover:bg-white/10 transition-all">
                                View My Points
                            </a>
                        </div>

                    </div>
                </div>
            </div>

        </div>


    </section>
</x-app-layout>
