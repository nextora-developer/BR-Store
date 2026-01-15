<x-app-layout>
    <div class="bg-[#FAF9F6] min-h-screen py-10">
        <div class="max-w-7xl5 mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-xs font-medium uppercase tracking-widest text-gray-400 mb-8">
                <a href="{{ route('home') }}" class="hover:text-[#8f6a10] transition-colors">Home</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M9 5l7 7-7 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <a href="{{ route('account.index') }}" class="hover:text-[#8f6a10] transition-colors">Account</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M9 5l7 7-7 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span class="text-gray-900">Referral</span>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

                {{-- Left sidebar --}}
                <aside class="hidden lg:block lg:col-span-1">
                    @include('account.partials.sidebar')
                </aside>

                {{-- Right Content --}}
                <main class="lg:col-span-3 space-y-8">

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        {{-- Left: Invite friends + stats --}}
                        <section
                            class="relative overflow-hidden bg-white rounded-3xl border border-gray-100 shadow-sm p-8 flex flex-col justify-between">
                            <div
                                class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-[#F9F4E5] rounded-full opacity-50 blur-3xl">
                            </div>

                            <div class="relative">
                                <h1 class="text-3xl font-black text-gray-900 leading-tight tracking-tight">
                                    Invite friends
                                </h1>
                                <p class="text-sm text-gray-500 mt-2 max-w-sm leading-relaxed">
                                    Share your referral code with friends. When they register, they will be linked to
                                    your account.
                                </p>
                            </div>

                            {{-- Stats --}}
                            <div class="relative grid grid-cols-2 gap-4 mt-8">
                                {{-- Total Referrals --}}
                                <div
                                    class="group p-6 rounded-2xl bg-gray-50 border border-gray-100 transition-all hover:bg-white hover:shadow-md">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-white flex items-center justify-center shadow-sm mb-3 group-hover:scale-110 transition-transform">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                    </div>
                                    <div class="text-[10px] font-bold uppercase tracking-widest text-gray-400 mb-1">
                                        Friends</div>
                                    <div class="text-3xl font-black text-gray-900">{{ $stats['total'] ?? 0 }}</div>
                                </div>

                                {{-- Reward Points --}}
                                <div
                                    class="group p-6 rounded-2xl bg-[#fcfaf6] border border-[#D4AF37]/20 transition-all hover:bg-white hover:shadow-md hover:border-[#D4AF37]/40">
                                    <div
                                        class="w-8 h-8 rounded-lg bg-white flex items-center justify-center shadow-sm mb-3 group-hover:scale-110 transition-transform text-[#D4AF37]">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </div>
                                    <div class="text-[10px] font-bold uppercase tracking-widest text-[#8f6a10] mb-1">
                                        Points</div>
                                    <div class="text-3xl font-black text-[#8f6a10]">
                                        {{ number_format($stats['points'] ?? 0) }}</div>
                                </div>
                            </div>

                        </section>

                        {{-- Right: Referral Code --}}
                        <section
                            class="relative bg-white rounded-3xl border border-gray-100 shadow-sm p-8 flex flex-col">
                            <div class="mb-auto">
                                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-[#8f6a10] mb-4">
                                    Your Unique Referral Code
                                </p>

                                <div onclick="copyReferralCode()"
                                    class="group cursor-pointer relative flex items-center justify-between px-6 py-5 rounded-2xl bg-[#fcfaf6] border-2 border-dashed border-[#D4AF37]/30 hover:border-[#D4AF37] transition-all">
                                    <span class="font-mono text-2xl font-black tracking-[0.2em] text-gray-900">
                                        {{ $user->referral_code }}
                                    </span>

                                    <div
                                        class="flex items-center gap-2 text-[#8f6a10] font-bold text-xs uppercase tracking-wider opacity-60 group-hover:opacity-100 transition-opacity">
                                        <span>Click to Copy</span>
                                    </div>
                                </div>

                                <p class="text-xs text-gray-400 mt-4 leading-relaxed">
                                    Friends enter this code during registration to grant you rewards.
                                </p>
                            </div>

                            <div class="mt-8">
                                <button type="button" onclick="copyReferralCode()"
                                    class="w-full inline-flex items-center justify-center gap-3 px-8 py-4 rounded-2xl bg-black text-white text-sm font-bold hover:bg-gray-800 active:scale-[0.98] transition-all shadow-lg shadow-black/5">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5 0H15.75a1.125 1.125 0 011.125 1.125v12.375c0 .621-.504 1.125-1.125 1.125H9.75a1.125 1.125 0 01-1.125-1.125V17.25" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8.25 6.75h12.75a1.125 1.125 0 011.125 1.125v12.375a1.125 1.125 0 01-1.125 1.125H15.75" />
                                    </svg>
                                    Copy Referral Code
                                </button>

                                <div class="h-6 mt-2"> {{-- Fixed height container to prevent layout shift --}}
                                    <p id="copyToast" class="text-center text-xs text-emerald-600 font-bold hidden">
                                        ✨ Copied to clipboard!
                                    </p>
                                    <p id="copyFail" class="text-center text-xs text-red-600 font-bold hidden">
                                        Copy failed. Please copy manually.
                                    </p>
                                </div>
                            </div>

                            <input id="refCodeInput" type="text" value="{{ $user->referral_code }}"
                                class="absolute -left-[9999px] top-0 opacity-0 pointer-events-none" readonly>
                        </section>
                    </div>

                    {{-- Referral List --}}
                    <section>
                        <div class="flex items-center justify-between mb-5 px-2">
                            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                <span class="w-1.5 h-6 bg-[#D4AF37] rounded-full"></span>
                                Your Referrals
                            </h2>
                        </div>

                        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead class="bg-gray-50/70 border-b border-gray-100">
                                        <tr>
                                            <th
                                                class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-wider text-gray-400">
                                                Name
                                            </th>
                                            <th
                                                class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-wider text-gray-400">
                                                Email
                                            </th>
                                            <th
                                                class="px-6 py-4 text-right text-[11px] font-black uppercase tracking-wider text-gray-400">
                                                Registered
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody class="divide-y divide-gray-50">
                                        @forelse ($referrals as $r)
                                            @php
                                                $ru = $r->referredUser;
                                            @endphp
                                            <tr class="hover:bg-[#FAF9F6] transition-colors">
                                                <td class="px-6 py-4">
                                                    <div class="flex items-center gap-3">
                                                        <div
                                                            class="w-9 h-9 rounded-xl bg-gray-100 text-gray-700 flex items-center justify-center font-bold">
                                                            {{ strtoupper(substr($ru->name ?? 'U', 0, 1)) }}
                                                        </div>
                                                        <span class="font-semibold text-gray-900">
                                                            {{ $ru->name ?? 'User' }}
                                                        </span>
                                                    </div>
                                                </td>

                                                <td class="px-6 py-4 text-gray-600">
                                                    {{ $ru->email ?? '-' }}
                                                </td>

                                                <td class="px-6 py-4 text-right text-gray-500">
                                                    {{ $r->created_at->format('d M Y') }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="px-6 py-12 text-center">
                                                    <div
                                                        class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-gray-50 text-gray-300 mb-4">
                                                        <svg class="w-7 h-7" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path
                                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                                                stroke-width="1.5" stroke-linecap="round"
                                                                stroke-linejoin="round" />
                                                        </svg>
                                                    </div>
                                                    <h3 class="text-gray-900 font-bold">No referrals yet</h3>
                                                    <p class="text-sm text-gray-500 mt-1">
                                                        Share your referral code to start building your network.
                                                    </p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if ($referrals->hasPages())
                                <div class="px-6 py-4 border-t border-gray-100">
                                    {{ $referrals->links() }}
                                </div>
                            @endif
                        </div>
                    </section>

                    {{-- Points Transactions --}}
                    <section>
                        <div class="flex items-center justify-between mb-5 px-2 mt-10">
                            <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                <span class="w-1.5 h-6 bg-[#D4AF37] rounded-full"></span>
                                Referral Points History
                            </h2>
                        </div>

                        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full text-sm">
                                    <thead class="bg-gray-50/70 border-b border-gray-100">
                                        <tr>
                                            <th
                                                class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-wider text-gray-400">
                                                Date
                                            </th>
                                            <th
                                                class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-wider text-gray-400">
                                                Source
                                            </th>
                                            <th
                                                class="px-6 py-4 text-right text-[11px] font-black uppercase tracking-wider text-gray-400">
                                                Points
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody class="divide-y divide-gray-50">
                                        @forelse ($pointTransactions as $tx)
                                            <tr class="hover:bg-[#FAF9F6] transition-colors">
                                                <td class="px-6 py-4 text-gray-500">
                                                    {{ $tx->created_at->format('d M Y') }}
                                                </td>

                                                @php
                                                    $refUser = $tx->referralLog?->referredUser;
                                                @endphp

                                                <td class="px-6 py-4">
                                                    <div class="text-gray-900 font-semibold">
                                                        Referral Order
                                                        @if ($refUser)
                                                            <span class="text-gray-500 font-normal">
                                                                • {{ $refUser->name }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ $tx->note ?? 'Referral reward' }}
                                                    </div>
                                                </td>




                                                <td class="px-6 py-4 text-right">
                                                    <span
                                                        class="inline-flex items-center font-black text-emerald-600 bg-emerald-50 px-3 py-1 rounded-lg">
                                                        +{{ number_format($tx->points) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="px-6 py-12 text-center">
                                                    <div
                                                        class="inline-flex items-center justify-center w-14 h-14 rounded-full bg-gray-50 text-gray-300 mb-4">
                                                        <svg class="w-7 h-7" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                                                stroke-width="1.5" stroke-linecap="round"
                                                                stroke-linejoin="round" />
                                                        </svg>
                                                    </div>
                                                    <h3 class="text-gray-900 font-bold">No points yet</h3>
                                                    <p class="text-sm text-gray-500 mt-1">
                                                        Points will appear here after your referrals complete an order.
                                                    </p>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if ($pointTransactions->hasPages())
                                <div class="px-6 py-4 border-t border-gray-100">
                                    {{ $pointTransactions->links() }}
                                </div>
                            @endif
                        </div>
                    </section>

                </main>
            </div>
        </div>
    </div>

    <script>
        function showToast(id) {
            const el = document.getElementById(id);
            el.classList.remove('hidden');
            setTimeout(() => el.classList.add('hidden'), 1400);
        }

        function fallbackCopy(text) {
            const input = document.getElementById('refCodeInput');
            input.value = text;

            input.focus();
            input.select();
            input.setSelectionRange(0, input.value.length);

            try {
                const ok = document.execCommand('copy');
                if (ok) showToast('copyToast');
                else showToast('copyFail');
            } catch (e) {
                showToast('copyFail');
            }
        }

        function copyReferralCode() {
            const code = document.getElementById('refCodeInput').value;

            // Try modern clipboard first
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(code)
                    .then(() => showToast('copyToast'))
                    .catch(() => fallbackCopy(code));
            } else {
                // HTTP / non-secure context -> fallback
                fallbackCopy(code);
            }
        }
    </script>
</x-app-layout>
