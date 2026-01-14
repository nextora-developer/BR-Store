@extends('admin.layouts.app')

@section('content')
    {{-- Header Section --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-3xl font-semibold text-gray-900 tracking-tight">User Profile</h1>
            <p class="text-sm text-gray-500 mt-1">Detailed overview of account credentials and saved locations.</p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-gray-200
                       text-sm font-semibold text-gray-600 hover:bg-gray-50 transition shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                <span>Back</span>
            </a>

            <a href="{{ route('admin.users.edit', $user) }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#D4AF37]/15 border border-[#D4AF37]/30 
                       text-[#8f6a10] font-bold hover:bg-[#D4AF37]/20 transition shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                    stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                </svg>
                <span>Edit Profile</span>
            </a>
        </div>
    </div>

    <div class="space-y-6">
        {{-- Overview Card --}}
        <div class="bg-white border border-[#D4AF37]/18 rounded-2xl p-6 shadow-[0_18px_40px_rgba(0,0,0,0.06)]">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-gray-100 pb-6">
                <div class="flex items-center gap-3">
                    <div
                        class="h-12 w-12 rounded-full bg-[#D4AF37]/10 border border-[#D4AF37]/20 flex items-center justify-center text-[#8f6a10] font-bold text-xl uppercase">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 text-lg leading-tight">{{ $user->name }}</p>
                        @if ($user->is_admin ?? false)
                            <span
                                class="inline-flex mt-1 items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-gray-900 text-white uppercase tracking-wider">
                                Administrator
                            </span>
                        @else
                            <span
                                class="inline-flex mt-1 items-center px-2 py-0.5 rounded-md text-[10px] font-bold bg-[#D4AF37]/10 text-[#8f6a10] border border-[#D4AF37]/20 uppercase tracking-wider">
                                Customer Account
                            </span>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    {{-- Active Badge --}}
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-bold border
        {{ $user->is_active ? 'border-green-500 bg-green-50 text-green-700' : 'border-gray-300 bg-gray-50 text-gray-500' }}">
                        <span
                            class="w-1.5 h-1.5 rounded-full mr-2
            {{ $user->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                        {{ $user->is_active ? 'ACTIVE ACCOUNT' : 'INACTIVE' }}
                    </span>

                    {{-- Verified Badge --}}
                    @if ($user->is_verified)
                        <span
                            class="inline-flex items-center gap-1 px-3 py-1 rounded-full
                   text-[11px] font-bold uppercase tracking-wider
                   bg-emerald-100 text-emerald-700 border border-emerald-200">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Verified
                        </span>
                    @else
                        <span
                            class="inline-flex items-center gap-1 px-3 py-1 rounded-full
                   text-[11px] font-bold uppercase tracking-wider
                   bg-gray-100 text-gray-500 border border-gray-200">
                            Unverified
                        </span>
                    @endif
                </div>

            </div>

            {{-- Info Grid --}}
            <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                <div class="rounded-xl bg-gray-50/80 border border-gray-100 px-4 py-3">
                    <p class="text-xs uppercase font-bold tracking-widest text-gray-400 mb-1">Email Address</p>
                    <p class="text-gray-900 font-semibold truncate">{{ $user->email ?? '—' }}</p>
                </div>

                <div class="rounded-xl bg-gray-50/80 border border-gray-100 px-4 py-3">
                    <p class="text-xs uppercase font-bold tracking-widest text-gray-400 mb-1">Mobile Phone</p>
                    <p class="text-gray-900 font-semibold">{{ $user->phone ?? '—' }}</p>
                </div>

                <div class="rounded-xl bg-gray-50/80 border border-gray-100 px-4 py-3">
                    <p class="text-xs uppercase font-bold tracking-widest text-gray-400 mb-1">Created date</p>
                    <p class="text-gray-900 font-semibold">{{ $user->created_at?->format('d M Y') ?? '—' }}</p>
                </div>

                <div class="rounded-xl bg-gray-50/80 border border-gray-100 px-4 py-3">
                    <p class="text-xs uppercase font-bold tracking-widest text-gray-400 mb-1">Last Updated</p>
                    <p class="text-gray-900 font-semibold">{{ $user->updated_at?->format('d M Y') ?? '—' }}</p>
                </div>
            </div>
        </div>

        {{-- Identity Card --}}
        <div x-data="{
            open: false,
            img: '',
            openImg(src) {
                this.img = src;
                this.open = true;
            },
            close() {
                this.open = false;
                this.img = '';
            }
        }"
            class="mt-6 bg-white border border-[#D4AF37]/18 rounded-2xl p-6 shadow-[0_18px_40px_rgba(0,0,0,0.06)]">
            <div class="flex items-center justify-between mb-5">
                <h2 class="font-bold text-gray-900 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#D4AF37]" fill="none"
                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12h6m-6 4h6M7 4h10a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" />
                    </svg>
                    Identity Verification
                </h2>

                @if ($user->ic_image)
                    <a href="{{ asset('storage/' . $user->ic_image) }}" download
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#D4AF37]/15 border border-[#D4AF37]/30
                       text-[#8f6a10] font-bold hover:bg-[#D4AF37]/20 transition shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                            stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0 4-4m-4 4-4-4M6 21h12" />
                        </svg>
                        Download IC Image
                    </a>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                {{-- IC Number --}}
                <div class="rounded-xl bg-gray-50/80 border border-gray-100 px-4 py-3">
                    <p class="text-xs uppercase font-bold tracking-widest text-gray-400 mb-1">IC Number</p>
                    <p class="text-gray-900 font-semibold">
                        {{ $user->ic_number ?? '—' }}
                    </p>
                </div>

                {{-- Birth Date --}}
                <div class="rounded-xl bg-gray-50/80 border border-gray-100 px-4 py-3">
                    <p class="text-xs uppercase font-bold tracking-widest text-gray-400 mb-1">Birth Date</p>
                    <p class="text-gray-900 font-semibold">
                        {{ $user->birth_date ? $user->birth_date->format('d M Y') : '—' }}
                    </p>
                </div>

                {{-- IC Image --}}
                <div class="rounded-xl bg-gray-50/80 border border-gray-100 px-4 py-3">
                    <p class="text-xs uppercase font-bold tracking-widest text-gray-400 mb-2">IC Image</p>

                    @if ($user->ic_image)
                        @php
                            $icUrl = asset('storage/' . $user->ic_image);
                        @endphp

                        <button type="button" @click="openImg('{{ $icUrl }}')"
                            class="w-full flex items-center gap-3 p-3 rounded-xl bg-white border border-gray-200
                           hover:border-[#D4AF37]/60 hover:shadow-sm transition text-left">
                            <img src="{{ $icUrl }}" alt="IC Thumbnail"
                                class="w-12 h-12 rounded-lg object-cover border border-gray-100" />
                            <div class="flex-1">
                                <div class="text-sm font-bold text-gray-900">View IC Image</div>
                                <div class="text-xs text-gray-400">Click to preview in popup</div>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none"
                                viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M15 12h.01M12 12h.01M9 12h.01M20 12c0 4.418-4 8-8 8s-8-3.582-8-8 4-8 8-8 8 3.582 8 8Z" />
                            </svg>
                        </button>
                    @else
                        <div class="p-3 rounded-xl bg-white border border-dashed border-gray-200 text-xs text-gray-400">
                            No IC image uploaded.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Popup Modal --}}
            <div x-show="open" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center p-4"
                style="display:none;" @keydown.escape.window="close()">
                {{-- Backdrop --}}
                <div class="absolute inset-0 bg-black/60" @click="close()"></div>

                {{-- Modal Panel --}}
                <div
                    class="relative w-full max-w-3xl bg-white rounded-2xl overflow-hidden shadow-2xl border border-white/10">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                        <div class="font-bold text-gray-900">IC Image Preview</div>

                        <div class="flex items-center gap-2">
                            <a :href="img" download
                                class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-[#D4AF37]/15 border border-[#D4AF37]/30
                               text-[#8f6a10] font-bold hover:bg-[#D4AF37]/20 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 3v12m0 0 4-4m-4 4-4-4M6 21h12" />
                                </svg>
                                Download
                            </a>

                            <button type="button" @click="close()"
                                class="inline-flex items-center justify-center w-10 h-10 rounded-xl border border-gray-200
                               hover:bg-gray-50 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-gray-600" fill="none"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="p-4 bg-gray-50">
                        <img :src="img" alt="IC Full Preview"
                            class="w-full max-h-[75vh] object-contain rounded-xl bg-white border border-gray-100" />
                    </div>
                </div>
            </div>
        </div>

        {{-- Points + Referral Row --}}
        <div class="mt-6 bg-white border border-[#D4AF37]/18 rounded-2xl p-6 shadow-[0_18px_40px_rgba(0,0,0,0.06)]">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                {{-- LEFT: Points + Referral --}}
                <div class="space-y-6">

                    {{-- Points Balance + Adjust --}}
                    <div class="rounded-2xl border border-gray-100 bg-gray-50/40 p-5">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-gray-900 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#D4AF37]" fill="none"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 12v-2m9-4a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Points
                            </h3>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">
                                100 pts = RM 1
                            </span>
                        </div>

                        <p class="text-xs uppercase font-bold tracking-widest text-gray-400">Current Balance</p>
                        <p class="text-2xl font-black text-gray-900 tracking-tight mt-1">
                            {{ number_format($pointsBalance ?? 0) }} pts
                        </p>

                        {{-- Adjust Form --}}
                        <form method="POST" action="{{ route('admin.users.points.adjust', $user) }}" class="mt-4">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                <select name="action"
                                    class="py-2.5 bg-white border border-gray-200 rounded-xl text-sm font-semibold
                                   focus:ring-2 focus:ring-[#D4AF37]/20 focus:border-[#D4AF37]">
                                    <option value="add">Add</option>
                                    <option value="deduct">Deduct</option>
                                </select>

                                <input type="number" name="points" min="1" value="{{ old('points', 100) }}"
                                    class="py-2.5 px-3 bg-white border border-gray-200 rounded-xl text-sm
                                   focus:ring-2 focus:ring-[#D4AF37]/20 focus:border-[#D4AF37]"
                                    placeholder="Points">

                                <button
                                    class="py-2.5 rounded-xl bg-gray-900 text-white text-sm font-bold hover:bg-black transition-all">
                                    Apply
                                </button>
                            </div>

                            <input type="text" name="note" value="{{ old('note') }}"
                                class="mt-3 w-full py-2.5 px-3 bg-white border border-gray-200 rounded-xl text-sm
                               focus:ring-2 focus:ring-[#D4AF37]/20 focus:border-[#D4AF37]"
                                placeholder="Note (optional)">

                            @error('points')
                                <div class="mt-2 text-xs font-bold text-red-600">{{ $message }}</div>
                            @enderror
                        </form>
                    </div>

                    {{-- Referral Info (moved UNDER points) --}}
                    <div class="rounded-2xl border border-gray-100 bg-gray-50/40 p-5">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-gray-900 flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-[#D4AF37]" fill="none"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.999 8.999 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0Z" />
                                </svg>
                                Referral
                            </h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div class="bg-white border border-gray-100 rounded-xl p-4">
                                <p class="text-xs uppercase font-bold tracking-widest text-gray-400">Referral Code</p>
                                <p class="text-sm font-black text-gray-900 mt-1">{{ $user->referral_code ?? '—' }}</p>
                            </div>

                            <div class="bg-white border border-gray-100 rounded-xl p-4">
                                <p class="text-xs uppercase font-bold tracking-widest text-gray-400">Invited Users</p>
                                <p class="text-sm font-black text-gray-900 mt-1">{{ number_format($invitedCount ?? 0) }}
                                </p>
                            </div>

                            <div class="bg-white border border-gray-100 rounded-xl p-4">
                                <p class="text-xs uppercase font-bold tracking-widest text-gray-400">Referral Earned</p>
                                <p class="text-sm font-black text-gray-900 mt-1">
                                    {{ number_format($referralEarned ?? 0) }} pts
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 bg-white border border-gray-100 rounded-xl p-4">
                            <p class="text-xs uppercase font-bold tracking-widest text-gray-400">Referred By</p>
                            @if ($user->referrer)
                                <p class="text-sm font-bold text-gray-900 mt-1">{{ $user->referrer->name }}</p>
                                <p class="text-xs text-gray-400">{{ $user->referrer->email }}</p>
                            @else
                                <p class="text-sm font-semibold text-gray-400 mt-1">—</p>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- RIGHT: Recent Point Transactions --}}
                <div class="rounded-2xl border border-gray-100 bg-gray-50/40 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-gray-900">Recent Point Transactions</h3>
                    </div>

                    @if (!empty($recentPoints) && $recentPoints->count())
                        <div class="space-y-2">
                            @foreach ($recentPoints as $tx)
                                @php
                                    $isSpend = ($tx->type ?? '') === 'spend' || ($tx->source ?? '') === 'redeem';
                                    $sign = $isSpend ? '-' : '+';
                                    $pill = $isSpend
                                        ? 'bg-rose-50 text-rose-700 border-rose-200'
                                        : 'bg-emerald-50 text-emerald-700 border-emerald-200';
                                @endphp

                                <div
                                    class="flex items-center justify-between gap-3 bg-white border border-gray-100 rounded-xl px-3 py-2">
                                    <div class="min-w-0">
                                        <div class="text-sm font-bold text-gray-900 truncate">
                                            {{ strtoupper(str_replace('_', ' ', $tx->source ?? '—')) }}
                                            @if ($tx->order)
                                                <span class="text-xs text-gray-400">· {{ $tx->order->order_no }}</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-gray-400">
                                            {{ $tx->created_at?->format('d M Y, H:i') }} · {{ $tx->note ?? '—' }}
                                        </div>
                                    </div>

                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $pill }}">
                                        {{ $sign }}{{ number_format(abs((int) ($tx->points ?? 0))) }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="py-10 text-center text-gray-400 text-sm">
                            No point transactions yet.
                        </div>
                    @endif
                </div>
            </div>
        </div>



        {{-- Bottom Row: Addresses & Activity --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Addresses --}}
            <div class="bg-white border border-[#D4AF37]/18 rounded-2xl p-6 shadow-[0_18px_40px_rgba(0,0,0,0.06)]">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="font-bold text-gray-900 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-4 h-4 text-[#D4AF37]">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                        </svg>
                        Saved Addresses
                    </h2>
                </div>

                @if ($user->addresses->isEmpty())
                    <div class="py-10 text-center border-2 border-dashed border-gray-100 rounded-2xl">
                        <p class="text-sm text-gray-400 italic">No registered addresses found.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($user->addresses as $address)
                            <div
                                class="relative rounded-2xl border px-4 py-4 transition-all {{ $address->is_default ? 'bg-[#D4AF37]/5 border-[#D4AF37]/40 shadow-sm' : 'bg-white border-gray-100 hover:border-gray-300' }}">
                                <div class="flex items-start justify-between mb-2">
                                    <div>
                                        <div class="font-bold text-gray-900">{{ $address->recipient_name }}</div>
                                        <div class="text-xs text-gray-500 font-medium">{{ $address->phone }}</div>
                                    </div>
                                    @if ($address->is_default)
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-[9px] font-black bg-[#D4AF37] text-white uppercase tracking-tighter">
                                            Default
                                        </span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-700 leading-relaxed">
                                    {{ $address->address_line1 }}@if ($address->address_line2)
                                        , {{ $address->address_line2 }}
                                    @endif
                                    <br>
                                    <span class="font-medium text-gray-900">{{ $address->postcode }}
                                        {{ $address->city }}</span>, {{ $address->state }}
                                    <br>
                                    <span
                                        class="text-xs text-gray-400 uppercase tracking-widest font-bold">{{ $address->country }}</span>
                                </p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Recent activity --}}
            <div
                class="bg-white border border-[#D4AF37]/18 rounded-2xl p-6 shadow-[0_18px_40px_rgba(0,0,0,0.06)] flex flex-col">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="font-bold text-gray-900 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="w-4 h-4 text-[#D4AF37]">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                        Shopping Activity
                    </h2>
                    <span class="text-xs font-bold text-gray-300 uppercase tracking-widest">
                        @if ($recentOrders->count())
                            Last {{ $recentOrders->count() }} Orders
                        @else
                            No Orders Yet
                        @endif
                    </span>
                </div>

                @if ($recentOrders->count())
                    {{-- ✅ 有订单时：订单列表 --}}
                    <div class="flex-1 -mx-3">
                        <ul class="divide-y divide-gray-100">
                            @foreach ($recentOrders as $order)
                                <li class="px-3 py-3 flex items-center justify-between gap-4">
                                    <div>
                                        <div class="flex items-center gap-2 text-sm font-semibold text-gray-900">
                                            <span>Order #{{ $order->order_no }}</span>

                                            {{-- status badge --}}
                                            <span
                                                class="text-[10px] px-2 py-0.5 rounded-full
                                    @switch($order->status)
                                        @case('pending') bg-amber-100 text-amber-700 @break
                                        @case('paid') bg-emerald-100 text-emerald-700 @break
                                        @case('processing') bg-blue-100 text-blue-700 @break
                                        @case('shipped') bg-indigo-100 text-indigo-700 @break
                                        @case('completed') bg-emerald-100 text-emerald-700 @break
                                        @case('cancelled') bg-red-100 text-red-600 @break
                                        @default bg-gray-100 text-gray-500
                                    @endswitch
                                ">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </div>

                                        <p class="text-xs text-gray-400 mt-1">
                                            {{ $order->created_at->format('d M Y, H:i') }}
                                            @if ($order->items_count ?? false)
                                                · {{ $order->items_count }} item(s)
                                            @endif
                                        </p>
                                    </div>

                                    <div class="flex items-center gap-4">
                                        <div class="text-right">
                                            <div class="text-sm font-bold text-gray-900">
                                                RM {{ number_format($order->total, 2) }}
                                            </div>
                                            @if ($order->payment_method_name ?? null)
                                                <div class="text-[11px] text-gray-400">
                                                    {{ $order->payment_method_name }}
                                                </div>
                                            @endif
                                        </div>

                                        {{-- 管理员查看订单详情 --}}
                                        <a href="{{ route('admin.orders.show', $order) }}"
                                            class="inline-flex items-center px-3 py-1.5 rounded-full border border-gray-200
                                      text-xs font-semibold text-gray-700 hover:border-[#D4AF37]/60 hover:text-[#8f6a10] transition">
                                            View
                                        </a>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @else
                    {{-- 🚫 没有订单：用一个简单空状态（你也可以沿用之前那套 Locked 文案） --}}
                    <div
                        class="flex-1 flex flex-col items-center justify-center text-center space-y-3 py-10 border-2 border-dashed border-gray-50 rounded-2xl bg-gray-50/30">
                        <div class="p-3 bg-white rounded-full shadow-sm border border-gray-100 text-gray-300">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-500">No orders found</p>
                            <p class="text-xs text-gray-400 max-w-xs mx-auto mt-1">
                                This customer has not placed any orders yet.
                            </p>
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>
@endsection
