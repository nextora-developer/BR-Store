<x-app-layout>
    <div class="bg-[#FAF9F6] min-h-screen py-10">
        <div class="max-w-7xl5 mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-xs font-medium uppercase tracking-widest text-gray-400 mb-8">
                <a href="{{ route('home') }}" class="hover:text-[#8f6a10] transition-colors">Home</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M9 5l7 7-7 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span class="text-gray-900">Account Overview</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

                {{-- Left sidebar --}}
                <aside class="hidden lg:block lg:col-span-1">
                    @include('account.partials.sidebar')
                </aside>

                {{-- Right Content --}}
                <main class="lg:col-span-3 space-y-10">

                    {{-- 1. Header Row --}}
                    <div class="grid grid-cols-12 gap-6 items-stretch">
                        {{-- Left: Welcome Card --}}
                        <section
                            class="relative col-span-12 lg:col-span-8 overflow-hidden bg-white rounded-3xl border border-gray-100 shadow-sm p-8 flex flex-col justify-center transition-all">
                            <div
                                class="absolute top-0 right-0 -mt-10 -mr-10 w-48 h-48 bg-[#FAF9F6] rounded-full blur-3xl opacity-60">
                            </div>
                            <div class="relative">
                                <h1 class="text-4xl font-black text-gray-900 tracking-tight leading-tight">
                                    Welcome back, <br class="sm:hidden">
                                    <span class="text-[#8f6a10]">{{ explode(' ', $user->name)[0] }}</span>
                                </h1>
                                <p class="text-sm text-gray-500 mt-4 max-w-md leading-relaxed">
                                    Your personal hub for managing orders, tracking your rewards, and updating your
                                    preferences.
                                </p>

                                <div class="flex gap-8 mt-2 pt-4 border-t border-gray-50">
                                    <div>
                                        <span
                                            class="block text-[10px] font-black uppercase tracking-[0.15em] text-gray-400 mb-1">
                                            Total Orders
                                        </span>
                                        <span class="text-xl font-bold text-gray-900">
                                            {{ $stats['orders'] }}
                                        </span>
                                    </div>

                                    <div>
                                        <span
                                            class="block text-[10px] font-black uppercase tracking-[0.15em] text-gray-400 mb-1">
                                            Saved Items
                                        </span>
                                        <span class="text-xl font-bold text-gray-900">
                                            {{ $stats['favorites'] }}
                                        </span>
                                    </div>

                                    <div>
                                        <span
                                            class="block text-[10px] font-black uppercase tracking-[0.15em] text-gray-400 mb-1">
                                            Addresses
                                        </span>
                                        <span class="text-xl font-bold text-gray-900">
                                            {{ $stats['addresses'] }}
                                        </span>
                                    </div>
                                </div>

                            </div>
                        </section>

                        {{-- Right: Membership-style Reward Card --}}
                        <section
                            class="relative col-span-12 lg:col-span-4 overflow-hidden rounded-3xl p-8 bg-gradient-to-br from-gray-900 via-gray-800 to-black shadow-xl group">
                            <div
                                class="absolute top-0 right-0 w-32 h-32 bg-[#D4AF37] rounded-full blur-[80px] opacity-20 group-hover:opacity-30 transition-opacity">
                            </div>
                            <div class="relative h-full flex flex-col justify-between">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <div
                                            class="text-[10px] font-black uppercase tracking-[0.2em] text-[#D4AF37]/80">
                                            Reward Points</div>
                                        <div class="mt-4 flex items-baseline gap-1">
                                            <span
                                                class="text-5xl font-black text-white tracking-tighter">{{ number_format($stats['points'] ?? 0) }}</span>
                                            <span class="text-xs font-bold text-[#D4AF37]">PTS</span>
                                        </div>
                                    </div>
                                    <div
                                        class="bg-white/10 backdrop-blur-md border border-white/10 p-2.5 rounded-xl shadow-inner">
                                        <svg class="w-6 h-6 text-[#D4AF37]" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </div>
                                </div>
                                <div class="mt-8">
                                    <div class="flex items-center justify-between text-white/50 mb-3">
                                        <span class="text-[10px] font-black uppercase tracking-widest">Est. Cash
                                            Value</span>
                                        <span
                                            class="text-lg font-bold text-white">RM{{ number_format(($stats['points'] ?? 0) / 100, 2) }}</span>
                                    </div>
                                    <div class="w-full h-1.5 bg-white/10 rounded-full overflow-hidden">
                                        <div
                                            class="h-full bg-gradient-to-r from-[#8f6a10] to-[#D4AF37] w-full rounded-full">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>

                    {{-- 2. Points History Section --}}
                    <section>
                        <div class="flex items-center justify-between mb-5 px-2">
                            <h2 class="text-lg font-black text-gray-900 flex items-center gap-3">
                                <span class="w-1.5 h-6 bg-[#D4AF37] rounded-full"></span>
                                Points History
                            </h2>
                            <div
                                class="px-3 py-1 rounded-lg text-[10px] font-bold uppercase tracking-widest bg-gradient-to-r from-[#F9F4E5] to-white border border-[#D4AF37]/20 text-[#8f6a10]">
                                100 pts = RM 1
                            </div>

                        </div>

                        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                            <div class="overflow-x-auto whitespace-nowrap">
                                <table class="min-w-full text-sm">
                                    <thead class="bg-gray-50/70 border-b border-gray-100">
                                        <tr>
                                            <th
                                                class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-widest text-gray-400">
                                                Date</th>
                                            <th
                                                class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-widest text-gray-400">
                                                Source</th>
                                            <th
                                                class="px-6 py-4 text-right text-[11px] font-black uppercase tracking-widest text-gray-400">
                                                Points</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        @forelse ($pointTransactions as $tx)
                                            <tr class="hover:bg-[#FAF9F6] transition-colors group">
                                                <td class="px-6 py-5 text-gray-500 font-medium tracking-tight">
                                                    {{ $tx->created_at->format('d M Y') }}
                                                </td>
                                                <td class="px-6 py-5">
                                                    @php
                                                        $isSpend =
                                                            ($tx->type ?? '') === 'spend' ||
                                                            ($tx->source ?? '') === 'redeem';
                                                        $title = match ($tx->source) {
                                                            'purchase' => 'Cashback Reward',
                                                            'redeem' => 'Points Redeemed',
                                                            default => ucfirst(
                                                                str_replace('_', ' ', $tx->source ?? 'transaction'),
                                                            ),
                                                        };
                                                        $defaultNote =
                                                            $tx->source === 'redeem'
                                                                ? 'Redeem points on checkout'
                                                                : 'Purchase reward points earned';
                                                    @endphp

                                                    <div class="text-gray-900 font-bold">{{ $title }}</div>
                                                    <div class="text-[11px] text-gray-400 font-medium">
                                                        {{ $tx->note ?? $defaultNote }}
                                                    </div>
                                                </td>

                                                <td class="px-6 py-5 text-right">
                                                    @php
                                                        $points = (int) ($tx->points ?? 0);
                                                        $sign = $isSpend ? '-' : '+';
                                                        $badgeClass = $isSpend
                                                            ? 'text-rose-600 bg-rose-50'
                                                            : 'text-emerald-600 bg-emerald-50';
                                                    @endphp

                                                    <span
                                                        class="inline-flex items-center font-black {{ $badgeClass }} px-3 py-1 rounded-lg">
                                                        {{ $sign }}{{ number_format(abs($points)) }}
                                                    </span>
                                                </td>

                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="px-6 py-16 text-center">
                                                    <div class="text-gray-200 mb-3 flex justify-center">
                                                        <svg class="w-12 h-12" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                                                stroke-width="1.5" stroke-linecap="round"
                                                                stroke-linejoin="round" />
                                                        </svg>
                                                    </div>
                                                    <h3 class="text-gray-900 font-bold text-base">No transactions yet
                                                    </h3>
                                                    <p class="text-sm text-gray-400 mt-1">Start shopping to earn
                                                        cashback points.</p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="mt-4">
                            {{ $pointTransactions->links() }}
                        </div>

                    </section>

                    {{-- 3. Recent Orders Section --}}
                    <section>
                        <div class="flex items-center justify-between mb-5 px-2">
                            <h2 class="text-lg font-black text-gray-900 flex items-center gap-3">
                                <span class="w-1.5 h-6 bg-[#D4AF37] rounded-full"></span>
                                Recent Orders
                            </h2>
                            <a href="{{ route('account.orders.index') }}"
                                class="text-xs font-black uppercase tracking-widest text-[#8f6a10] hover:text-[#D4AF37] flex items-center gap-2 group transition-all">
                                View All
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path d="M13 7l5 5m0 0l-5 5m5-5H6" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </a>
                        </div>

                        <div class="grid gap-4">
                            @forelse ($latestOrders as $order)
                                <a href="{{ route('account.orders.show', $order) }}"
                                    class="group relative bg-white rounded-2xl border border-gray-100 p-5 hover:border-[#D4AF37]/30 hover:shadow-xl hover:shadow-[#D4AF37]/5 transition-all duration-300">

                                    <div class="flex flex-col md:flex-row md:items-center gap-5">
                                        {{-- Product Preview --}}
                                        <div
                                            class="w-16 h-16 rounded-xl overflow-hidden bg-gray-50 border border-gray-100 flex-shrink-0 shadow-inner">
                                            @php
                                                $firstItem = $order->items->first();
                                                $thumb =
                                                    $firstItem && $firstItem->product && $firstItem->product->image
                                                        ? asset('storage/' . $firstItem->product->image)
                                                        : null;
                                            @endphp
                                            @if ($thumb)
                                                <img src="{{ $thumb }}"
                                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                            @else
                                                <div
                                                    class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-300">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path
                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-3 mb-1">
                                                <span
                                                    class="text-sm font-black text-gray-900 tracking-tight">#{{ $order->order_no }}</span>
                                                <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                                <span
                                                    class="text-xs text-gray-400 font-bold uppercase tracking-tighter">{{ $order->created_at->format('d M Y') }}</span>
                                            </div>
                                            <p class="text-xs text-gray-500 font-medium truncate">
                                                {{ $order->items->count() }}
                                                item{{ $order->items->count() > 1 ? 's' : '' }} • Ship to
                                                {{ $order->shipping_city ?? 'Registered Address' }}
                                            </p>
                                        </div>

                                        <div
                                            class="flex items-center justify-between md:justify-end gap-6 border-t md:border-0 pt-4 md:pt-0">
                                            @php
                                                $statusClasses = [
                                                    'pending' => 'bg-amber-50 text-amber-700 border-amber-100',
                                                    'paid' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                                    'shipped' => 'bg-blue-50 text-blue-700 border-blue-100',
                                                    'completed' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
                                                    'cancelled' => 'bg-red-50 text-red-700 border-red-100',
                                                    'failed' => 'bg-rose-50 text-rose-700 border-rose-100',
                                                ];
                                            @endphp
                                            <span
                                                class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $statusClasses[$order->status] ?? 'bg-gray-50 text-gray-600 border-gray-200' }}">
                                                {{ $order->status }}
                                            </span>
                                            <div class="text-base font-black text-gray-900 tracking-tight">
                                                RM {{ number_format($order->total, 2) }}
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div
                                    class="bg-white rounded-3xl border border-dashed border-gray-200 p-12 text-center">
                                    <div
                                        class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 text-gray-300 mb-4">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke-width="1.5"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </div>
                                    <h3 class="text-gray-900 font-bold">Your closet is empty</h3>
                                    <p class="text-sm text-gray-500 mt-1">When you make your first purchase, it will
                                        appear here.</p>
                                    <a href="{{ route('shop.index') }}"
                                        class="mt-6 inline-flex items-center px-8 py-3 bg-black text-white text-xs font-black uppercase tracking-widest rounded-xl transition-all hover:bg-gray-800 hover:-translate-y-0.5">Start
                                        Shopping</a>
                                </div>
                            @endforelse
                        </div>
                    </section>
                </main>

            </div>
        </div>
    </div>
</x-app-layout>
