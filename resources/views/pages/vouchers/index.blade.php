<x-app-layout>
    <div class="bg-[#F9F7F2] min-h-screen">
        <div class="max-w-7xl5 mx-auto px-6 py-8 lg:py-10">

            {{-- Page Header --}}
            <div class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
                <div>
                    <h1 class="text-sm uppercase tracking-[0.3em] text-[#D4AF37] font-bold mb-3">Exclusive Access</h1>
                    <p class="text-4xl font-light text-black tracking-tight">
                        Voucher <span class="font-serif italic">Privileges</span>
                    </p>
                    <p class="mt-2 text-sm text-black/45 max-w-xl">
                        Copy a code and apply it during checkout. Limited releases rotate frequently.
                    </p>
                </div>

                {{-- Search --}}
                <form method="GET" action="{{ route('vouchers.index') }}" class="w-full md:w-[360px]">
                    <label class="block text-[11px] uppercase tracking-wide text-black/40 mb-2 font-bold">Search</label>
                    <div class="relative">
                        <input name="q" value="{{ request('q') }}" placeholder="Search by code or name..."
                            class="w-full rounded-2xl border border-black/5 bg-white/80 backdrop-blur
                                   px-4 py-3 text-sm text-black/70
                                   focus:border-[#D4AF37]/60 focus:ring-[#D4AF37]/20 focus:outline-none">
                        <button type="submit"
                            class="absolute right-2 top-1/2 -translate-y-1/2 px-3 py-1.5 rounded-xl bg-black text-white text-xs font-bold">
                            Go
                        </button>

                    </div>
                </form>
            </div>

            {{-- Section Wrapper (参考你那段) --}}
            <section class="relative bg-[#F9F7F2] py-6 overflow-hidden rounded-[2.5rem] border border-black/[0.03]">
                {{-- High-End Background Composition --}}
                <div class="pointer-events-none absolute inset-0 z-0">
                    {{-- Primary Golden Glow (Centered Top) --}}
                    <div class="absolute left-1/2 top-0 -translate-x-1/2 -translate-y-1/2">
                        <div class="w-[1000px] h-[600px] rounded-full bg-[#D4AF37]/10 blur-[100px]"></div>
                    </div>

                    {{-- Secondary Soft Dark Glow (Side) --}}
                    <div class="absolute right-[-10%] top-[20%]">
                        <div class="w-[600px] h-[600px] rounded-full bg-black/[0.02] blur-[80px]"></div>
                    </div>

                    {{-- Structural Dot Grid --}}
                    <div class="absolute inset-0 opacity-[0.35]"
                        style="background-image: radial-gradient(circle at 1px 1px, rgba(15,23,42,.08) 1px, transparent 1px);
                               background-size: 32px 32px;">
                    </div>

                    {{-- Subtle Vertical Typography --}}
                    <div class="absolute left-10 top-1/2 -translate-y-1/2 hidden xl:block">
                        <span class="text-[10rem] font-black text-black/[0.01] uppercase tracking-tighter select-none"
                            style="writing-mode: vertical-rl;">
                            Privilege
                        </span>
                    </div>
                </div>

                <div class="relative z-10 px-6 py-8">
                    {{-- small divider --}}
                    <div class="h-px bg-gradient-to-r from-transparent via-black/10 to-transparent mb-8"></div>

                    @if ($vouchers->count())
                        <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                            @foreach ($vouchers as $v)
                                <div
                                    class="group relative flex flex-col sm:flex-row bg-white rounded-2xl
                                           shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)]
                                           hover:shadow-[0_20px_40px_-10px_rgba(0,0,0,0.08)]
                                           transition-all duration-500 overflow-hidden border border-black/[0.03]">

                                    {{-- Left Side --}}
                                    <div class="flex-1 p-8 sm:p-10 relative">
                                        <div class="flex flex-col h-full justify-between">
                                            <div>
                                                <div class="flex items-center gap-3 mb-6">
                                                    <span class="w-8 h-[1px] bg-[#D4AF37]"></span>
                                                    <span
                                                        class="text-[10px] uppercase tracking-[0.2em] font-bold text-black/40">
                                                        Limited Release
                                                    </span>
                                                </div>

                                                <h3
                                                    class="text-3xl font-bold text-black tracking-tighter mb-4 group-hover:text-[#D4AF37] transition-colors duration-300">
                                                    {{ $v->name ?? 'Tier Rewards' }}
                                                </h3>

                                                <div class="space-y-2">
                                                    @if (!is_null($v->min_spend))
                                                        <p class="text-sm text-black/50 font-medium tracking-tight">
                                                            Minimum investment of
                                                            <span class="text-black font-bold text-base ml-1">
                                                                RM {{ number_format($v->min_spend, 0) }}
                                                            </span>
                                                        </p>
                                                    @endif

                                                    @if (!empty($v->expires_at))
                                                        <p
                                                            class="text-[11px] text-black/30 uppercase tracking-widest font-bold">
                                                            Valid Thru
                                                            {{ \Carbon\Carbon::parse($v->expires_at)->format('F d, Y') }}
                                                        </p>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="mt-8">
                                                <a href="{{ route('shop.index') }}"
                                                    class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-widest group/link">
                                                    <span>Explore Collection</span>
                                                    <svg class="w-4 h-4 transform group-hover/link:translate-x-1 transition-transform"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Perforated Divider --}}
                                    <div
                                        class="relative w-full sm:w-px h-px sm:h-auto border-t sm:border-t-0 sm:border-l border-dashed border-black/10">
                                        <div
                                            class="absolute -top-3 -left-3 sm:-left-3 w-6 h-6 rounded-full bg-[#F9F7F2] border border-black/[0.03] z-10">
                                        </div>
                                        <div
                                            class="absolute -bottom-3 -left-3 sm:-left-3 w-6 h-6 rounded-full bg-[#F9F7F2] border border-black/[0.03] z-10">
                                        </div>
                                    </div>

                                    {{-- Right Side Stub --}}
                                    <div
                                        class="w-full sm:w-[240px] bg-black/[0.01] p-8 flex flex-col items-center justify-center relative overflow-hidden">
                                        <div class="absolute inset-0 opacity-10 pointer-events-none"
                                            style="background-image: radial-gradient(#D4AF37 0.5px, transparent 0.5px); background-size: 10px 10px;">
                                        </div>

                                        <div class="relative w-full">
                                            <p
                                                class="text-center text-[10px] uppercase tracking-[0.2em] font-bold text-black/30 mb-4">
                                                Click to Copy Code
                                            </p>

                                            <button type="button" data-copy-voucher="{{ $v->code }}"
                                                class="voucher-button group/btn w-full bg-white border border-black/5 p-4 rounded-xl shadow-sm
                                                       hover:border-[#D4AF37]/50 active:scale-95 transition-all duration-300 overflow-hidden relative">

                                                <span
                                                    class="block text-lg font-mono font-bold tracking-[0.2em] text-black group-hover/btn:text-[#8f6a10]">
                                                    {{ $v->code }}
                                                </span>

                                                <div
                                                    class="copy-indicator absolute inset-0 bg-[#D4AF37] flex items-center justify-center translate-y-full transition-transform duration-300">
                                                    <span
                                                        class="text-white text-[10px] font-bold uppercase tracking-widest">Code
                                                        Copied</span>
                                                </div>
                                            </button>

                                            <button type="button"
                                                class="mt-4 w-full text-center text-[10px] font-bold text-[#8f6a10] hover:text-[#D4AF37]
                                                        uppercase tracking-widest underline underline-offset-4 transition"
                                                data-open-terms="1" data-terms-title="{{ $v->name ?? 'Voucher' }}"
                                                data-terms-code="{{ $v->code }}"
                                                data-terms-min="{{ !is_null($v->min_spend) ? number_format($v->min_spend, 2) : '' }}"
                                                data-terms-start="{{ !empty($v->starts_at) ? \Carbon\Carbon::parse($v->starts_at)->format('d M Y') : '' }}"
                                                data-terms-end="{{ !empty($v->expires_at) ? \Carbon\Carbon::parse($v->expires_at)->format('d M Y') : '' }}">
                                                Terms & Conditions
                                            </button>

                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-10">
                            {{ $vouchers->links() }}
                        </div>
                    @else
                        <div class="rounded-3xl bg-white/60 border border-black/5 p-10 text-center">
                            <p class="text-black/60 font-medium">No vouchers available right now.</p>
                            <a href="{{ route('shop.index') }}"
                                class="mt-4 inline-flex text-sm font-bold text-[#8f6a10] underline underline-offset-4">
                                Browse products
                            </a>
                        </div>
                    @endif
                </div>
            </section>
        </div>

        {{-- Copy script (same logic as yours) --}}
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('[data-copy-voucher]').forEach(btn => {
                    btn.addEventListener('click', async (e) => {
                        e.preventDefault();
                        e.stopPropagation();

                        const code = btn.getAttribute('data-copy-voucher');
                        const indicator = btn.querySelector('.copy-indicator');

                        const showCopied = () => {
                            if (!indicator) return;
                            indicator.classList.remove('translate-y-full');
                            btn.style.borderColor = '#D4AF37';
                            setTimeout(() => {
                                indicator.classList.add('translate-y-full');
                                btn.style.borderColor = '';
                            }, 1500);
                        };

                        try {
                            if (navigator.clipboard && window.isSecureContext) {
                                await navigator.clipboard.writeText(code);
                                showCopied();
                                return;
                            }

                            const ta = document.createElement('textarea');
                            ta.value = code;
                            ta.setAttribute('readonly', '');
                            ta.style.position = 'fixed';
                            ta.style.left = '-9999px';
                            document.body.appendChild(ta);
                            ta.select();
                            const ok = document.execCommand('copy');
                            document.body.removeChild(ta);

                            if (ok) showCopied();
                            else alert('Copy failed. Please copy manually: ' + code);
                        } catch (err) {
                            console.error(err);
                            alert('Copy failed. Please copy manually: ' + code);
                        }
                    }, true);
                });
            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const modal = document.getElementById('termsModal');
                const titleEl = document.getElementById('termsTitle');
                const codeEl = document.getElementById('termsCode');
                const listEl = document.getElementById('termsList');

                const openModal = () => {
                    modal.classList.remove('hidden');
                    document.body.classList.add('overflow-hidden');
                };

                const closeModal = () => {
                    modal.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                };

                // open
                document.querySelectorAll('[data-open-terms]').forEach(btn => {
                    btn.addEventListener('click', (e) => {
                        e.preventDefault();
                        e.stopPropagation();

                        const tTitle = btn.getAttribute('data-terms-title') || 'Voucher';
                        const tCode = btn.getAttribute('data-terms-code') || '';
                        const tMin = btn.getAttribute('data-terms-min') || '';
                        const tStart = btn.getAttribute('data-terms-start') || '';
                        const tEnd = btn.getAttribute('data-terms-end') || '';

                        titleEl.textContent = tTitle;
                        codeEl.textContent = tCode;

                        // build terms list like your screenshot
                        const terms = [];

                        if (tMin) terms.push(`Minimum purchase required: RM ${tMin}`);
                        terms.push('Cannot be combined with other vouchers');

                        if (tStart && tEnd) terms.push(`Valid from ${tStart} to ${tEnd}`);
                        else if (tEnd) terms.push(`Valid until ${tEnd}`);

                        listEl.innerHTML = terms.map(line => `
                <li class="flex items-start gap-3">
                    <span class="mt-2 w-1.5 h-1.5 rounded-full bg-[#D4AF37]"></span>
                    <span>${line}</span>
                </li>
            `).join('');

                        openModal();
                    }, true);
                });

                // close (overlay + buttons)
                modal.querySelectorAll('[data-close-terms]').forEach(el => {
                    el.addEventListener('click', closeModal);
                });

                // ESC to close
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
                });
            });
        </script>

    </div>

    {{-- ✅ Terms Modal (single instance) --}}
    <div id="termsModal" class="fixed inset-0 z-[999] hidden">
        {{-- overlay --}}
        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" data-close-terms></div>

        {{-- panel --}}
        <div class="relative min-h-screen flex items-center justify-center p-4">
            <div class="w-full max-w-xl rounded-3xl bg-white shadow-2xl border border-black/10 overflow-hidden">
                {{-- header --}}
                <div class="px-6 py-5 border-b border-black/10 flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-[11px] uppercase tracking-[0.28em] font-bold text-[#D4AF37]">
                            Voucher Details
                        </p>
                        <h3 id="termsTitle" class="mt-1 text-xl font-bold text-black truncate">
                            Terms & Conditions
                        </h3>
                        <p class="mt-1 text-xs text-black/50">
                            Code: <span id="termsCode" class="font-mono font-bold text-black/70"></span>
                        </p>
                    </div>

                    <button type="button"
                        class="shrink-0 w-10 h-10 rounded-full bg-black/5 hover:bg-black/10 transition flex items-center justify-center"
                        aria-label="Close" data-close-terms>
                        <svg class="w-5 h-5 text-black/60" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- body --}}
                <div class="px-6 py-6 space-y-6">
                    {{-- Terms & Conditions --}}
                    <div>
                        <h4 class="text-sm font-extrabold text-black mb-3">Terms & Conditions</h4>
                        <ul id="termsList" class="space-y-2 text-sm text-black/60">
                            {{-- injected by JS --}}
                        </ul>
                    </div>

                    <div class="h-px bg-black/10"></div>

                    {{-- How to Use --}}
                    <div>
                        <h4 class="text-sm font-extrabold text-black mb-3">How to Use</h4>
                        <ol class="space-y-2 text-sm text-black/60 list-decimal list-inside">
                            <li>Copy the voucher code by clicking the button.</li>
                            <li>Add items to your cart and proceed to checkout.</li>
                            <li>Paste the code in the <span class="font-semibold text-black/70">"Voucher"</span>
                                field.</li>
                            <li>Click <span class="font-semibold text-black/70">"Apply"</span> to enjoy your discount.
                            </li>
                        </ol>
                    </div>
                </div>

                {{-- footer --}}
                <div class="px-6 py-5 border-t border-black/10 flex flex-col sm:flex-row gap-3 sm:justify-end">
                    <a href="{{ route('shop.index') }}"
                        class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-3 rounded-2xl
                           bg-black text-white text-xs font-black uppercase tracking-widest hover:bg-black/90 transition">
                        Shop Now
                    </a>
                    <button type="button" data-close-terms
                        class="w-full sm:w-auto inline-flex items-center justify-center px-5 py-3 rounded-2xl
                           bg-white border border-black/10 text-xs font-black uppercase tracking-widest
                           hover:bg-black/5 transition">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
