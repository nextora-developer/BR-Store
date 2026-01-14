@extends('admin.layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Point Transactions</h1>
            <p class="text-sm text-gray-500 mt-1">View all points earned & redeemed across users</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm mb-6 transition-all">
        <form method="GET" class="flex flex-col md:flex-row gap-3">

            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                <input name="keyword" value="{{ request('keyword') }}" placeholder="Search user name/email, order no, note…"
                    class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 border-transparent rounded-xl text-sm
                           focus:bg-white focus:ring-2 focus:ring-[#D4AF37]/20 focus:border-[#D4AF37] transition-all">
            </div>

            <select name="type"
                class="min-w-[140px] py-2.5 bg-gray-50 border-transparent rounded-xl text-sm
                       focus:bg-white focus:ring-2 focus:ring-[#D4AF37]/20 focus:border-[#D4AF37] transition-all">
                <option value="">All Type</option>
                <option value="earn" @selected(request('type') === 'earn')>Earn</option>
                <option value="spend" @selected(request('type') === 'spend')>Spend</option>
            </select>

            <select name="source"
                class="min-w-[160px] py-2.5 bg-gray-50 border-transparent rounded-xl text-sm
                       focus:bg-white focus:ring-2 focus:ring-[#D4AF37]/20 focus:border-[#D4AF37] transition-all">
                <option value="">All Source</option>
                @foreach ($sources as $src)
                    <option value="{{ $src }}" @selected(request('source') === $src)>{{ $src }}</option>
                @endforeach
            </select>

            <div class="flex gap-2">
                <input type="date" name="from" value="{{ request('from') }}"
                    class="w-[150px] py-2.5 px-3 bg-gray-50 border-transparent rounded-xl text-sm
                           focus:bg-white focus:ring-2 focus:ring-[#D4AF37]/20 focus:border-[#D4AF37] transition-all">
                <input type="date" name="to" value="{{ request('to') }}"
                    class="w-[150px] py-2.5 px-3 bg-gray-50 border-transparent rounded-xl text-sm
                           focus:bg-white focus:ring-2 focus:ring-[#D4AF37]/20 focus:border-[#D4AF37] transition-all">
            </div>

            <button
                class="px-6 py-2.5 rounded-xl bg-[#D4AF37]/10 text-[#8f6a10]
                       border border-[#D4AF37]/20 hover:bg-[#D4AF37] hover:text-white
                       transition-all font-bold text-sm">
                Filter
            </button>

            <a href="{{ route('admin.points.index') }}"
                class="px-4 py-2.5 rounded-xl border border-gray-100 text-gray-500 hover:bg-gray-50
                       transition-all text-sm flex items-center justify-center">
                Reset
            </a>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-50">
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider w-[180px]">
                            Date
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider w-[280px]">
                            User
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider w-[150px]">
                            Source
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider w-[200px]">
                            Order
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider w-[240px]">
                            Referral By
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider w-[140px]">
                            Points
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider w-[140px]">

                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-50">
                    @forelse ($pointTransactions as $tx)
                        @php
                            $isSpend = ($tx->type ?? '') === 'spend' || ($tx->source ?? '') === 'redeem';
                            $sign = $isSpend ? '-' : '+';

                            $badgeClass = $isSpend
                                ? 'bg-rose-50 text-rose-600 border-rose-100'
                                : 'bg-emerald-50 text-emerald-600 border-emerald-100';

                            $sourceLabel = strtoupper(str_replace('_', ' ', $tx->source ?? '—'));
                        @endphp

                        <tr class="group hover:bg-[#D4AF37]/5 transition-colors">
                            {{-- Date --}}
                            <td class="px-6 py-4 text-gray-700 font-medium whitespace-nowrap">
                                <div class="font-semibold text-gray-900">
                                    {{ optional($tx->created_at)->format('d M Y') }}
                                </div>
                                <div class="text-xs text-gray-400">
                                    {{ optional($tx->created_at)->format('H:i') }}
                                </div>
                            </td>

                            {{-- User --}}
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 group-hover:text-[#8f6a10] transition-colors">
                                    {{ $tx->user->name ?? '—' }}
                                </div>
                                <div class="text-xs text-gray-400">
                                    {{ $tx->user->email ?? '' }}
                                </div>
                            </td>

                            {{-- Source --}}
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full
                                           text-[11px] font-bold bg-gray-50 text-gray-600
                                           border border-gray-100 uppercase tracking-wider">
                                    {{ $sourceLabel }}
                                </span>

                                {{-- <div class="mt-2">
                                    @if (($tx->type ?? '') === 'earn')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                                                   text-[10px] font-bold bg-emerald-50 text-emerald-600
                                                   border border-emerald-100 uppercase tracking-wider">
                                            Earn
                                        </span>
                                    @elseif (($tx->type ?? '') === 'spend')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                                                   text-[10px] font-bold bg-rose-50 text-rose-600
                                                   border border-rose-100 uppercase tracking-wider">
                                            Spend
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-300">—</span>
                                    @endif
                                </div> --}}
                            </td>

                            {{-- Order --}}
                            <td class="px-6 py-4">
                                @if ($tx->order)
                                    {{-- 改这里：如果你 orders show route 名不是 admin.orders.show --}}
                                    <a href="{{ route('admin.orders.show', $tx->order) }}"
                                        class="font-bold text-[#8f6a10] hover:underline whitespace-nowrap">
                                        {{ $tx->order->order_no }}
                                    </a>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>

                            {{-- Referral by --}}
                            <td class="px-6 py-4">
                                @if (($tx->source ?? '') === 'referral' && $tx->referralLog)
                                    <div class="font-bold text-gray-900 group-hover:text-[#8f6a10] transition-colors">
                                        {{ $tx->referralLog->referredUser->name ?? '—' }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ $tx->referralLog->referredUser->email ?? '' }}
                                    </div>
                                @else
                                    <span class="text-gray-300">—</span>
                                @endif
                            </td>


                            {{-- Points --}}
                            <td class="px-6 py-4 text-left whitespace-nowrap">
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full
                                           text-[11px] font-bold border uppercase tracking-wider {{ $badgeClass }}">
                                    {{ $sign }}{{ number_format(abs((int) ($tx->points ?? 0))) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">

                                    {{-- 👁 View --}}
                                    <a href="{{ route('admin.points.show', $tx) }}"
                                        class="p-2 rounded-lg text-gray-400 hover:text-[#8f6a10] hover:bg-[#D4AF37]/10 transition-all"
                                        title="View">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5
                                                    c4.478 0 8.268 2.943 9.542 7
                                                    -1.274 4.057-5.064 7-9.542 7
                                                    -4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>


                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center text-gray-400">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-gray-200" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 12v-2m9-4a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <p class="font-medium">No point transactions yet</p>
                                    <p class="text-xs mt-1 text-gray-300">Try adjusting your filters</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 bg-gray-50/50 border-t border-gray-100">
            {{ $pointTransactions->links() }}
        </div>
    </div>
@endsection
