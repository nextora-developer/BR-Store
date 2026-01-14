@extends('admin.layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Point Transaction</h1>
            <p class="text-sm text-gray-500 mt-1">
                Transaction ID: <span class="font-semibold text-gray-700">#{{ $tx->id }}</span>
            </p>
        </div>

        <a href="{{ route('admin.points.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-gray-200
                   text-sm font-semibold text-gray-600 hover:bg-gray-50 transition shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            <span>Back</span>
        </a>
    </div>

    @php
        $isSpend = ($tx->type ?? '') === 'spend' || ($tx->source ?? '') === 'redeem';
        $sign = $isSpend ? '-' : '+';

        $pillClass = $isSpend
            ? 'bg-rose-50 text-rose-600 border-rose-100'
            : 'bg-emerald-50 text-emerald-600 border-emerald-100';

        $sourceLabel = strtoupper(str_replace('_', ' ', $tx->source ?? '—'));
        $typeLabel = strtoupper($tx->type ?? '—');
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Main Details --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Overview Card --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900 tracking-tight">Overview</h2>
                        <p class="text-sm text-gray-500 mt-1">Points movement details</p>
                    </div>

                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full
                               text-[11px] font-bold border uppercase tracking-wider {{ $pillClass }}">
                        {{ $sign }}{{ number_format(abs((int) ($tx->points ?? 0))) }} pts
                    </span>
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                        <p class="text-[11px] uppercase tracking-widest text-gray-400 font-bold">Date</p>
                        <p class="text-sm font-bold text-gray-900 mt-1">
                            {{ optional($tx->created_at)->format('d M Y, H:i') }}
                        </p>
                    </div>

                    <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                        <p class="text-[11px] uppercase tracking-widest text-gray-400 font-bold">Type</p>
                        <p class="text-sm font-bold text-gray-900 mt-1">{{ $typeLabel }}</p>
                    </div>

                    <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                        <p class="text-[11px] uppercase tracking-widest text-gray-400 font-bold">Source</p>
                        <p class="text-sm font-bold text-gray-900 mt-1">{{ $sourceLabel }}</p>
                    </div>
                </div>

                <div class="mt-5 bg-gray-50 rounded-2xl p-4 border border-gray-100">
                    <p class="text-[11px] uppercase tracking-widest text-gray-400 font-bold">Note</p>
                    <p class="text-sm text-gray-900 font-semibold mt-1">
                        {{ $tx->note ?? '—' }}
                    </p>
                </div>
            </div>

            {{-- User Card --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 tracking-tight">User</h2>
                <p class="text-sm text-gray-500 mt-1">Owner of this points transaction</p>

                <div class="mt-5 flex items-start justify-between gap-4 bg-gray-50 rounded-2xl p-4 border border-gray-100">
                    <div>
                        <p class="text-base font-bold text-gray-900">{{ $tx->user->name ?? '—' }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ $tx->user->email ?? '' }}</p>
                    </div>

                    {{-- 如果你有 admin user show route，可打开 --}}
                    {{-- <a href="{{ route('admin.users.show', $tx->user) }}"
                        class="px-4 py-2 rounded-xl bg-white border border-gray-200 text-sm font-bold text-gray-700 hover:bg-gray-50">
                        View User
                    </a> --}}
                </div>
            </div>

            {{-- Referral Card (only if referral) --}}
            @if (($tx->source ?? '') === 'referral')
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h2 class="text-lg font-bold text-gray-900 tracking-tight">Referral</h2>
                    <p class="text-sm text-gray-500 mt-1">Referral log details linked to this transaction</p>

                    @if ($tx->referralLog)
                        <div class="mt-5 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                                <p class="text-[11px] uppercase tracking-widest text-gray-400 font-bold">Referrer (Earned)
                                </p>
                                <p class="text-sm font-bold text-gray-900 mt-1">
                                    {{ $tx->referralLog->referrer->name ?? '—' }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $tx->referralLog->referrer->email ?? '' }}
                                </p>
                            </div>

                            <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                                <p class="text-[11px] uppercase tracking-widest text-gray-400 font-bold">Referral By</p>
                                <p class="text-sm font-bold text-gray-900 mt-1">
                                    {{ $tx->referralLog->referredUser->name ?? '—' }}
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ $tx->referralLog->referredUser->email ?? '' }}
                                </p>
                            </div>

                            <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100">
                                <p class="text-[11px] uppercase tracking-widest text-gray-400 font-bold">Referral Log ID</p>
                                <p class="text-sm font-bold text-gray-900 mt-1">#{{ $tx->referral_log_id }}</p>
                            </div>
                        </div>
                    @else
                        <div class="mt-5 bg-amber-50 border border-amber-200 rounded-2xl p-4">
                            <p class="text-sm font-bold text-amber-800">Referral log not found</p>
                            <p class="text-xs text-amber-700 mt-1">This transaction is marked as referral but has no
                                referral_log_id.</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        {{-- Right: Order Card --}}
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden h-max">
                <div class="px-5 py-4 border-b border-gray-50 bg-[#F9F4E5]/30">
                    <h2 class="text-base font-bold text-gray-900 tracking-tight">Order</h2>
                </div>

                <div class="p-5">
                    @if ($tx->order)
                        <div class="space-y-3">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 font-medium">Order No</span>
                                <span class="font-bold text-[#8f6a10]">
                                    {{-- 改这里：如果你的 orders show route 名不同 --}}
                                    <a href="{{ route('admin.orders.show', $tx->order) }}" class="hover:underline">
                                        {{ $tx->order->order_no }}
                                    </a>
                                </span>
                            </div>

                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 font-medium">Status</span>
                                <span class="font-bold text-gray-900">{{ strtoupper($tx->order->status ?? '—') }}</span>
                            </div>

                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 font-medium">Total</span>
                                <span class="font-bold text-gray-900">
                                    RM {{ number_format((float) ($tx->order->total ?? 0), 2) }}
                                </span>
                            </div>

                            <div class="pt-4 mt-2 border-t border-gray-100">
                                <p class="text-xs text-gray-400">
                                    Linked via <span class="font-bold text-gray-600">order_id</span>.
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="py-10 text-center text-gray-400">
                            <div class="flex justify-center mb-3">
                                <svg class="w-10 h-10 text-gray-200" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <p class="font-medium">No linked order</p>
                            <p class="text-xs mt-1 text-gray-300">This transaction has no order_id.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
