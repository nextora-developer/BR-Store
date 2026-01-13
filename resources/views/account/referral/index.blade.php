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

                    {{-- Header --}}
                    <section class="relative overflow-hidden bg-white rounded-3xl border border-gray-100 shadow-sm p-8">
                        <div
                            class="absolute top-0 right-0 -mt-4 -mr-4 w-32 h-32 bg-[#F9F4E5] rounded-full opacity-50 blur-3xl">
                        </div>

                        <div class="relative flex flex-col md:flex-row md:items-end justify-between gap-6">
                            <div>
                                <h1 class="text-3xl font-black text-gray-900 leading-tight">
                                    Invite friends
                                </h1>
                                <p class="text-sm text-gray-500 mt-2 max-w-xl">
                                    Share your referral code with friends. When they register, they will be under your
                                    account.
                                    Reward points will be available soon.
                                </p>
                            </div>

                            {{-- Stats --}}
                            <div class="flex items-center gap-3">
                                {{-- Total Referrals --}}
                                <div
                                    class="px-5 py-3 rounded-2xl bg-gray-50 border border-gray-100 text-center min-w-[120px]">
                                    <div class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-1">
                                        Total Referrals
                                    </div>
                                    <div class="text-2xl font-black text-gray-900">
                                        {{ $stats['total'] ?? 0 }}
                                    </div>
                                </div>

                                {{-- Reward Points --}}
                                <div
                                    class="px-5 py-3 rounded-2xl bg-[#fcfaf6] border border-[#D4AF37]/30 text-center min-w-[120px]">
                                    <div class="text-[10px] font-bold uppercase tracking-wider text-[#8f6a10] mb-1">
                                        Reward Points
                                    </div>
                                    <div class="text-2xl font-black text-[#8f6a10]">
                                        {{ $stats['points'] ?? 0 }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>


                    {{-- Referral Code --}}
                    <section class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                            <div>
                                <p class="text-[10px] font-black uppercase tracking-[0.18em] text-[#8f6a10] mb-2">
                                    Your Referral Code
                                </p>

                                <div
                                    class="inline-flex items-center gap-3 px-5 py-3 rounded-2xl bg-[#fcfaf6] border border-[#D4AF37]/20">
                                    <span class="font-mono text-base font-black tracking-widest text-gray-900">
                                        {{ $user->referral_code }}
                                    </span>
                                </div>

                                <p class="text-xs text-gray-500 mt-2">
                                    Share this code with your friends during registration.
                                </p>
                            </div>

                            <div class="flex items-center gap-3">
                                <button type="button" onclick="copyReferralCode()"
                                    class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-black text-white text-sm font-bold hover:opacity-90 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8 16h8M8 12h8m-6 8h6a2 2 0 002-2V6a2 2 0 00-2-2H10l-4 4v10a2 2 0 002 2z" />
                                    </svg>
                                    Copy Code
                                </button>
                            </div>
                        </div>

                        {{-- hidden input for fallback copy --}}
                        <input id="refCodeInput" type="text" value="{{ $user->referral_code }}"
                            class="absolute -left-[9999px] top-0 opacity-0 pointer-events-none" readonly>

                        <p id="copyToast" class="mt-3 text-xs text-emerald-600 font-semibold hidden">
                            Referral code copied!
                        </p>

                        <p id="copyFail" class="mt-3 text-xs text-red-600 font-semibold hidden">
                            Copy failed. Please select and copy manually.
                        </p>
                    </section>

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
                                                class="px-6 py-4 text-left text-[11px] font-black uppercase tracking-wider text-gray-400">
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

                                                <td class="px-6 py-4 text-gray-500">
                                                    {{ $r->created_at->format('M d, Y') }}
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
