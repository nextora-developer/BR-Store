<x-app-layout>
    <div class="bg-[#f7f7f9] py-10">
        <div class="max-w-7xl5 mx-auto px-4 sm:px-6 lg:px-8">

            {{-- Checkout Steps --}}
            @php
                // 你现在在哪一步：cart / checkout / complete
                $step = $step ?? 'checkout';

                $steps = [
                    ['key' => 'cart', 'label' => 'Shopping Cart'],
                    ['key' => 'checkout', 'label' => 'Checkout'],
                    ['key' => 'complete', 'label' => 'Order Complete'],
                ];

                $index = collect($steps)->search(fn($s) => $s['key'] === $step);
            @endphp

            <div
                class="bg-white/80 backdrop-blur rounded-2xl border border-gray-100 shadow-sm px-4 sm:px-6 py-3 sm:py-4 mb-6 sm:mb-8">
                {{-- mobile: 纵向；sm+：横向 --}}
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                    @foreach ($steps as $i => $s)
                        @php
                            $isDone = $i < $index;
                            $isNow = $i === $index;
                        @endphp

                        <div class="flex items-center flex-1 min-w-0">

                            {{-- Circle + Label --}}
                            <div class="flex items-center gap-3 min-w-0">
                                <div
                                    class="w-9 h-9 rounded-full grid place-items-center font-black text-sm flex-shrink-0
                        {{ $isDone ? 'bg-[#D4AF37] text-white' : ($isNow ? 'bg-black text-white' : 'bg-gray-100 text-gray-400') }}">
                                    @if ($isDone)
                                        ✓
                                    @else
                                        {{ $i + 1 }}
                                    @endif
                                </div>

                                <div class="min-w-0 text-left">
                                    <div
                                        class="text-xs sm:text-sm font-extrabold truncate
                            {{ $isNow ? 'text-gray-900' : ($isDone ? 'text-[#8f6a10]' : 'text-gray-400') }}">
                                        {{ $s['label'] }}
                                    </div>

                                    @if ($isNow)
                                        <div class="text-[11px] text-gray-400 mt-0.5">
                                            Current step
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Connector: 只在桌面显示 --}}
                            @if ($i < count($steps) - 1)
                                <div
                                    class="hidden sm:block flex-1 h-px mx-4
                        {{ $isDone ? 'bg-[#D4AF37]/70' : 'bg-gray-200' }}">
                                </div>
                            @endif
                        </div>
                    @endforeach

                </div>
            </div>

            {{-- 整个 checkout 表单 --}}
            <form method="POST" action="{{ route('checkout.store') }}" enctype="multipart/form-data">
                @csrf

                <section class="bg-transparent p-0 flex flex-col gap-6 lg:grid lg:grid-cols-5 lg:gap-8">

                    <div class="lg:col-span-3 space-y-4">

                        {{-- 左：信息 card --}}
                        <section
                            class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden lg:col-span-2">
                            {{-- Header Section --}}
                            <div class="p-6 sm:p-8 border-b border-gray-50">
                                <h1 class="text-2xl font-bold text-gray-900 mb-1">Shipping Details</h1>
                                <p class="text-sm text-gray-500">Please provide your delivery information to complete
                                    your order.</p>
                            </div>

                            <div class="p-6 sm:p-8 space-y-8">
                                {{-- 🔹 Saved Addresses Section --}}
                                @if (isset($addresses) && $addresses->count())
                                    <div>
                                        <div class="flex items-center justify-between mb-4">
                                            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Saved
                                                Addresses</h3>
                                            <span class="text-xs text-gray-400">Scroll to view more →</span>
                                        </div>

                                        <div class="flex gap-4 overflow-x-auto pb-4 no-scrollbar -mx-2 px-2"
                                            data-address-scroller>
                                            @foreach ($addresses as $addr)
                                                @php
                                                    $isDefault =
                                                        isset($defaultAddress) && $defaultAddress->id === $addr->id;
                                                    $fullAddress = trim(
                                                        implode(
                                                            ', ',
                                                            array_filter([
                                                                $addr->address_line1,
                                                                $addr->address_line2,
                                                                $addr->postcode . ' ' . $addr->city,
                                                                $addr->state,
                                                            ]),
                                                        ),
                                                    );
                                                @endphp

                                                <button type="button" data-address-choice
                                                    data-default="{{ $isDefault ? '1' : '0' }}"
                                                    data-name="{{ $addr->recipient_name }}"
                                                    data-phone="{{ $addr->phone }}" data-email="{{ $addr->email }}"
                                                    data-address_line1="{{ $addr->address_line1 }}"
                                                    data-address_line2="{{ $addr->address_line2 }}"
                                                    data-postcode="{{ $addr->postcode }}"
                                                    data-city="{{ $addr->city }}" data-state="{{ $addr->state }}"
                                                    data-country="{{ $addr->country }}"
                                                    class="address-card min-w-[260px] max-w-[260px] text-left rounded-2xl border-2 p-4 transition-all relative group border-gray-100 bg-white hover:border-gray-300">

                                                    @if ($isDefault)
                                                        <div class="absolute top-3 right-3">
                                                            <span
                                                                class="px-2.5 py-1 rounded-full bg-[#D4AF37] text-white text-[10px] font-bold uppercase tracking-widest shadow-sm">
                                                                Default
                                                            </span>
                                                        </div>
                                                    @endif


                                                    <p class="font-bold text-gray-900 mb-2 truncate pr-6">
                                                        {{ $addr->recipient_name }}</p>
                                                    <p class="text-xs text-gray-600 line-clamp-2 mb-3 leading-relaxed">
                                                        {{ $fullAddress }}</p>

                                                    <div
                                                        class="flex items-center gap-2 text-[11px] text-gray-400 font-medium">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                                        </svg>
                                                        {{ $addr->phone }}
                                                    </div>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- Error Alerts --}}
                                @if ($errors->any())
                                    <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-xl">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20"
                                                    fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm text-red-700 font-bold">There were some issues with
                                                    your submission:</p>
                                                <ul class="mt-1 text-sm text-red-600 list-disc list-inside">
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="space-y-8">
                                    {{-- Contact Information Group --}}
                                    <div class="bg-gray-50/50 p-5 rounded-2xl border border-gray-100">
                                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">
                                            Contact Information</h3>
                                        <div class="grid sm:grid-cols-3 gap-4">
                                            <div class="sm:col-span-1">
                                                <label class="block text-xs font-bold text-gray-700 mb-2 uppercase">Full
                                                    Name</label>
                                                <input type="text" name="name"
                                                    value="{{ old('name', $defaultAddress->recipient_name ?? auth()->user()->name) }}"
                                                    class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 transition-all text-sm shadow-sm"
                                                    placeholder="John Tan" required>
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-xs font-bold text-gray-700 mb-2 uppercase">Phone</label>
                                                <input type="text" name="phone"
                                                    value="{{ old('phone', $defaultAddress->phone ?? '') }}"
                                                    class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 transition-all text-sm shadow-sm"
                                                    placeholder="012-345 6789" required>
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-xs font-bold text-gray-700 mb-2 uppercase">Email</label>
                                                <input type="email" name="email"
                                                    value="{{ old('email', $defaultAddress->email ?? auth()->user()->email) }}"
                                                    class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 transition-all text-sm shadow-sm"
                                                    placeholder="name@email.com" required>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Shipping Address Group --}}
                                    <div class="p-2">
                                        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">
                                            Shipping Address</h3>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                                            <div>
                                                <label
                                                    class="block text-xs font-bold text-gray-700 mb-2 uppercase">Address
                                                    Line 1</label>
                                                <input type="text" name="address_line1"
                                                    value="{{ old('address_line1', $defaultAddress->address_line1 ?? '') }}"
                                                    class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 transition-all text-sm shadow-sm"
                                                    placeholder="No. 123, Street Name" required>
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-xs font-bold text-gray-700 mb-2 uppercase">Address
                                                    Line 2 (Optional)</label>
                                                <input type="text" name="address_line2"
                                                    value="{{ old('address_line2', $defaultAddress->address_line2 ?? '') }}"
                                                    class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 transition-all text-sm shadow-sm"
                                                    placeholder="Apartment, unit, etc.">
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-5">
                                            <div>
                                                <label
                                                    class="block text-xs font-bold text-gray-700 mb-2 uppercase">Postcode</label>
                                                <input type="text" name="postcode"
                                                    value="{{ old('postcode', $defaultAddress->postcode ?? '') }}"
                                                    class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 transition-all text-sm shadow-sm"
                                                    placeholder="43000" required>
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-xs font-bold text-gray-700 mb-2 uppercase">City</label>
                                                <input type="text" name="city"
                                                    value="{{ old('city', $defaultAddress->city ?? '') }}"
                                                    class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 transition-all text-sm shadow-sm"
                                                    placeholder="Kajang" required>
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-xs font-bold text-gray-700 mb-2 uppercase">State</label>
                                                <select name="state"
                                                    class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 transition-all text-sm shadow-sm bg-white"
                                                    data-state-select required>
                                                    <option value="">Select State</option>
                                                    @foreach ($states as $s)
                                                        <option value="{{ $s['name'] }}"
                                                            data-zone="{{ $s['zone'] }}"
                                                            @selected(old('state', $defaultAddress->state ?? '') === $s['name'])>
                                                            {{ $s['name'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-xs font-bold text-gray-700 mb-2 uppercase">Country</label>
                                                <input type="text" name="country"
                                                    value="{{ old('country', $defaultAddress->country ?? 'Malaysia') }}"
                                                    class="w-full px-4 py-3 rounded-xl border-gray-200 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 transition-all text-sm shadow-sm"
                                                    required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        {{-- Card 2：Payment Method --}}
                        <section class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                            {{-- Section Header --}}
                            <div class="px-6 py-5 border-b border-gray-50 flex items-center gap-3">
                                <div
                                    class="h-8 w-8 rounded-full bg-[#FDF3D7] flex items-center justify-center text-[#8f6a10]">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                </div>
                                <h2 class="text-lg font-bold text-gray-900">Payment Method</h2>
                            </div>

                            @php
                                $defaultCode = old(
                                    'payment_method',
                                    optional($paymentMethods->firstWhere('is_default', true))->code ??
                                        optional($paymentMethods->first())->code,
                                );
                            @endphp

                            <div class="p-6 sm:p-8">
                                <div class="space-y-4" id="payment-methods-container"
                                    data-default="{{ $defaultCode }}">
                                    @foreach ($paymentMethods as $method)
                                        @php
                                            $isOnlineTransfer = $method->code === 'online_transfer';
                                            $isHitpay = $method->code === 'hitpay';
                                            $isRM = $method->code === 'revenue_monster';
                                        @endphp

                                        <div class="payment-group">
                                            {{-- 选择卡片 --}}
                                            <label
                                                class="relative flex items-center p-4 rounded-2xl border-2 cursor-pointer transition-all duration-200
                        {{ $defaultCode === $method->code ? 'border-[#D4AF37] bg-[#FDFBF7]' : 'border-gray-100 bg-white hover:border-gray-200' }}">

                                                <div class="flex items-center h-5">
                                                    <input type="radio" name="payment_method"
                                                        value="{{ $method->code }}" @checked($defaultCode === $method->code)
                                                        class="payment-radio h-5 w-5 text-[#D4AF37] border-gray-300 focus:ring-[#D4AF37] cursor-pointer">
                                                </div>

                                                <div class="ml-4 flex-grow">
                                                    <p class="font-bold text-gray-900">{{ $method->name }}</p>

                                                    @if ($method->short_description)
                                                        <p class="text-xs text-gray-500 mt-0.5">
                                                            {{ $method->short_description }}
                                                        </p>
                                                    @endif
                                                </div>

                                                @if ($isOnlineTransfer)
                                                    <div class="hidden sm:flex items-center gap-1 opacity-60">
                                                        <div
                                                            class="px-2 py-1 rounded bg-white border border-gray-100 text-[10px] font-bold text-blue-800">
                                                            Online Banking
                                                        </div>
                                                    </div>
                                                @endif

                                                @if ($isRM)
                                                    <div class="hidden sm:flex items-center gap-1 opacity-70">
                                                        <div
                                                            class="px-2 py-1 rounded bg-white border border-gray-100 text-[10px] font-bold text-blue-800">
                                                            Payment Gateway
                                                        </div>
                                                    </div>
                                                @endif

                                            </label>

                                            {{-- 详情展开 --}}
                                            <div class="payment-detail transition-all duration-300 {{ $defaultCode === $method->code ? '' : 'hidden' }}"
                                                data-code="{{ $method->code }}">

                                                @if ($isOnlineTransfer)
                                                    @php
                                                        $amountToTransfer = $orderTotal ?? ($total ?? ($subtotal ?? 0));
                                                    @endphp

                                                    <div
                                                        class="mt-4 ml-6 p-5 bg-gray-50 rounded-2xl border border-gray-200 space-y-6">

                                                        {{-- Step 1 --}}
                                                        <div class="flex gap-4">
                                                            <span
                                                                class="flex-shrink-0 w-6 h-6 rounded-full bg-[#D4AF37] text-white text-xs font-bold flex items-center justify-center">1</span>

                                                            <div class="flex-grow">
                                                                <h4 class="text-sm font-bold text-gray-900 mb-3">
                                                                    Transfer to Bank Account
                                                                </h4>

                                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                                    <div
                                                                        class="p-3 bg-white rounded-xl border border-gray-200">
                                                                        <span
                                                                            class="text-[10px] uppercase font-bold text-gray-400 block mb-1">
                                                                            Account Number
                                                                        </span>
                                                                        <div class="flex items-center justify-between">
                                                                            <span
                                                                                class="font-mono font-bold text-gray-900">
                                                                                {{ $method->bank_account_number }}
                                                                            </span>
                                                                        </div>
                                                                    </div>

                                                                    <div
                                                                        class="p-3 bg-white rounded-xl border border-gray-200">
                                                                        <span
                                                                            class="text-[10px] uppercase font-bold text-gray-400 block mb-1">
                                                                            Bank Name
                                                                        </span>
                                                                        <span class="font-bold text-gray-900 block">
                                                                            {{ $method->bank_name }}
                                                                        </span>
                                                                    </div>

                                                                    {{-- ✅ Account Holder Name --}}
                                                                    <div
                                                                        class="p-3 bg-white rounded-xl border border-gray-200">
                                                                        <span
                                                                            class="text-[10px] uppercase font-bold text-gray-400 block mb-1">
                                                                            Account Holder Name
                                                                        </span>
                                                                        <span class="font-bold text-gray-900 block">
                                                                            {{ $method->bank_account_name }}
                                                                        </span>
                                                                    </div>

                                                                    <div
                                                                        class="p-3 bg-[#FDF3D7] border border-[#E6D8A8] rounded-xl">
                                                                        <span
                                                                            class="text-[10px] uppercase font-bold text-[#8f6a10] block mb-1">
                                                                            Exact Amount to Pay
                                                                        </span>
                                                                        <span class="text-xl font-black text-[#8f6a10]"
                                                                            data-pay-amount>
                                                                            RM 0.00
                                                                        </span>
                                                                    </div>

                                                                </div>
                                                            </div>
                                                        </div>

                                                        {{-- Step 2 QR --}}
                                                        @if ($method->duitnow_qr_path)
                                                            <div class="flex gap-4">
                                                                <span
                                                                    class="flex-shrink-0 w-6 h-6 rounded-full bg-[#D4AF37] text-white text-xs font-bold flex items-center justify-center">2</span>

                                                                <div>
                                                                    <h4 class="text-sm font-bold text-gray-900 mb-2">
                                                                        Or Scan DuitNow QR
                                                                    </h4>

                                                                    <div
                                                                        class="inline-block p-3 bg-white border-2 border-dashed border-gray-200 rounded-2xl">
                                                                        <img src="{{ asset('storage/' . $method->duitnow_qr_path) }}"
                                                                            class="w-32 h-32 object-contain">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif

                                                        {{-- Step 3 Receipt --}}
                                                        <div class="flex gap-4 items-start">
                                                            <span
                                                                class="flex-shrink-0 w-6 h-6 rounded-full bg-[#D4AF37] text-white text-xs font-bold flex items-center justify-center">
                                                                {{ $method->duitnow_qr_path ? '3' : '2' }}
                                                            </span>

                                                            <div class="flex-1 min-w-0">
                                                                <h4 class="text-sm font-bold text-gray-900 mb-2">
                                                                    Upload Transaction Receipt
                                                                </h4>

                                                                <p class="text-xs text-gray-500 mb-3 italic">
                                                                    Please upload a clear screenshot of your successful
                                                                    transfer.
                                                                </p>

                                                                <div class="max-w-full">

                                                                    <input type="file" name="payment_receipt"
                                                                        class="block w-full max-w-full text-sm text-gray-700
                                                                                border border-gray-200 rounded-2xl
                                                                                file:mr-4 file:py-2 file:px-4
                                                                                file:rounded-full file:border-0
                                                                                file:text-xs file:font-bold
                                                                                file:bg-[#D4AF37] file:text-white
                                                                                hover:file:bg-[#b9962c]
                                                                                focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/30
                                                                                focus:outline-none
                                                                                transition">
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                @if ($isRM)
                                                    <div
                                                        class="mt-4 ml-6 p-5 bg-[#F9FAFB] rounded-2xl border border-gray-200 space-y-4">
                                                        <div class="flex gap-4">
                                                            <span
                                                                class="flex-shrink-0 w-6 h-6 rounded-full bg-[#D4AF37] text-white text-xs font-bold flex items-center justify-center">
                                                                1
                                                            </span>

                                                            <div>
                                                                <h4 class="text-sm font-bold text-gray-900 mb-1">
                                                                    Pay via payment gateway
                                                                </h4>

                                                                <p
                                                                    class="text-xs text-gray-500 leading-relaxed max-w-md">
                                                                    You will be redirected to secure
                                                                    payment page to complete
                                                                    your payment. Supported methods include FPX, cards,
                                                                    and e-wallets.
                                                                </p>

                                                                <div
                                                                    class="mt-3 flex items-center gap-3 flex-wrap opacity-80">
                                                                    <img src="/images/payments/fpx.png"
                                                                        class="h-7">
                                                                    <img src="/images/payments/visa.png"
                                                                        class="h-7">
                                                                    <img src="/images/payments/mastercard.png"
                                                                        class="h-7">
                                                                    <img src="/images/payments/tng.png"
                                                                        class="h-7">
                                                                    <img src="/images/payments/grabpay.png"
                                                                        class="h-7">
                                                                    <img src="/images/payments/grabpaylater.png"
                                                                        class="h-7">
                                                                    <img src="/images/payments/boost.png"
                                                                        class="h-7">
                                                                    <img src="/images/payments/boostpaylater.png"
                                                                        class="h-7">
                                                                    <img src="/images/payments/shopeepay.png"
                                                                        class="h-7">
                                                                    <img src="/images/payments/spaylater.png"
                                                                        class="h-7">
                                                                    <img src="/images/payments/alipay.png"
                                                                        class="h-7">
                                                                    <img src="/images/payments/wechatpay.png"
                                                                        class="h-7">
                                                                    <img src="/images/payments/mae.png"
                                                                        class="h-7">
                                                                    <img src="/images/payments/mcash.png"
                                                                        class="h-7">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                @error('payment_method')
                                    <div
                                        class="mt-4 p-3 bg-red-50 text-red-600 text-xs font-bold rounded-xl flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </section>
                    </div>

                    <div class="lg:col-span-2 space-y-4">
                        {{-- 右：Order Summary card --}}
                        <aside
                            class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden h-max lg:sticky lg:top-28">
                            {{-- Card Header --}}
                            <div class="px-5 py-4 border-b border-gray-50 bg-[#F9F4E5]/30">
                                <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-[#8f6a10]" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    Order Summary
                                </h2>
                            </div>

                            <div class="p-5">
                                {{-- 商品列表（可滚动） --}}
                                <div
                                    class="space-y-4 mb-6 max-h-[230px] overflow-y-auto pr-1 scrollbar-thin scrollbar-thumb-gray-200">

                                    @foreach ($items as $item)
                                        @php $p = $item->product; @endphp

                                        <div class="flex gap-3 group mt-1">
                                            {{-- 小图 --}}
                                            <div
                                                class="w-16 h-16 rounded-xl bg-gray-50 border border-gray-100 overflow-visible flex-shrink-0 relative">
                                                @if ($p?->image)
                                                    <img src="{{ asset('storage/' . $p->image) }}"
                                                        alt="{{ $p->name }}"
                                                        class="w-full h-full object-cover group-hover:scale-110 transition duration-300">
                                                @else
                                                    <div
                                                        class="w-full h-full flex items-center justify-center text-[10px] text-gray-400">
                                                        No image
                                                    </div>
                                                @endif

                                                {{-- Qty Badge on Image --}}
                                                <span
                                                    class="absolute -top-1 -right-1 bg-gray-900 text-white text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full border-2 border-white">
                                                    {{ $item->qty }}
                                                </span>

                                            </div>

                                            {{-- Info --}}
                                            <div class="flex-1 min-w-0">
                                                <h3
                                                    class="text-sm font-medium text-gray-900 line-clamp-1 group-hover:text-[#8f6a10] transition-colors">
                                                    {{ $p->name }}
                                                </h3>

                                                @if ($item->variant_label)
                                                    <p class="text-[11px] text-gray-500 mt-0.5 truncate italic">
                                                        {{ $item->variant_label }}
                                                    </p>
                                                @endif

                                                <p class="text-sm font-bold text-gray-900 mt-1">
                                                    RM {{ number_format($item->unit_price * $item->qty, 2) }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                @php
                                    $applied = session('applied_voucher'); // ['voucher_id','code','discount']
                                    $voucherCode = $applied['code'] ?? null;
                                    $voucherDiscount = (float) ($applied['discount'] ?? 0);
                                    $voucherBenefit = $applied['benefit'] ?? null; // ✅
                                @endphp

                                {{-- 小计 / 运费 / 总额 --}}
                                <div class="space-y-3 bg-gray-50 rounded-2xl p-4">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Subtotal</span>
                                        <span class="font-bold text-gray-900 text-right">
                                            RM {{ number_format($subtotal, 2) }}
                                        </span>
                                    </div>

                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Shipping</span>
                                        <span class="font-bold text-gray-900 text-right" data-shipping-text>
                                            @if (!$hasPhysical)
                                                <span class="text-green-600">Free</span>
                                            @else
                                                <span class="text-gray-400 font-normal">TBC</span>
                                            @endif
                                        </span>
                                    </div>

                                    {{-- Voucher Summary Row (always exist, JS will toggle) --}}
                                    <div id="voucherRow" class="hidden flex justify-between text-sm">
                                        <span class="text-gray-500">
                                            Voucher (<span id="voucherCodeText"></span>)
                                        </span>
                                        <span class="font-black text-green-700 text-right">
                                            - RM <span id="voucherDiscountText">0.00</span>
                                        </span>
                                    </div>

                                    {{-- Shipping Discount Row (for free shipping) --}}
                                    <div id="shippingDiscountRow" class="hidden flex justify-between text-sm">
                                        <span class="text-gray-500">
                                            Shipping Discount (<span id="shippingVoucherCodeText"></span>)
                                        </span>
                                        <span class="font-black text-green-700 text-right">
                                            - RM <span id="shippingDiscountText">0.00</span>
                                        </span>
                                    </div>

                                    {{-- Points Redeem Row --}}
                                    <div id="pointsRedeemRow" class="hidden flex justify-between text-sm">
                                        <span class="text-gray-500">
                                            Points Redeem (<span id="pointsRedeemLabel">0</span> pts)
                                        </span>
                                        <span class="font-black text-green-700 text-right">
                                            - RM <span id="pointsRedeemRm">0.00</span>
                                        </span>
                                    </div>

                                    <div class="border-t border-gray-200 my-1 pt-3 flex justify-between items-center">
                                        <span class="text-base font-bold text-gray-900">Total</span>
                                        <div class="text-right">
                                            <span class="text-xl font-black text-[#8f6a10]" data-total-text>
                                                RM {{ number_format($subtotal, 2) }}
                                            </span>
                                            <p class="text-xs text-gray-500 uppercase tracking-widest mt-1">
                                                Estimated Earn: <span class="font-black text-gray-700"><span
                                                        id="cashbackPointsText">0</span> points</span>
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Voucher Box --}}
                                <div class="mt-5">
                                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Voucher
                                    </h3>

                                    <div id="voucherAppliedBox"
                                        class="hidden flex items-center justify-between gap-3 bg-[#FDF3D7] border border-[#E6D8A8] rounded-2xl p-4">
                                        <div class="min-w-0">
                                            <p class="text-xs text-[#8f6a10] font-black tracking-wider uppercase">
                                                Applied</p>
                                            <p class="text-sm font-bold text-gray-900 truncate"
                                                id="voucherAppliedCode">—</p>
                                        </div>

                                        <button type="button" id="btnRemoveVoucher"
                                            class="px-3 py-2 rounded-xl bg-white border border-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-50">
                                            Remove
                                        </button>
                                    </div>

                                    <div id="voucherInputBox" class="flex gap-2">
                                        <input type="text" id="voucherCodeInput" placeholder="Enter code"
                                            class="flex-1 px-4 py-3 rounded-2xl border border-gray-200 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 text-sm"
                                            maxlength="50" autocomplete="off">
                                        <button type="button" id="btnApplyVoucher"
                                            class="px-4 py-3 rounded-2xl bg-gray-900 text-white text-sm font-bold hover:bg-black">
                                            Apply
                                        </button>
                                    </div>

                                    <div id="voucherMsg" class="mt-3 hidden text-xs font-bold"></div>
                                </div>

                                {{-- 初始值给 JS 用（不会 reload） --}}
                                <span id="voucherState" data-code="{{ $voucherCode ?? '' }}"
                                    data-discount="{{ $voucherDiscount }}"
                                    data-benefit="{{ $voucherBenefit ?? '' }}" class="hidden"></span>

                                @php
                                    $userPoints = (int) (auth()->user()->points_balance ?? 0);
                                @endphp

                                {{-- Redeem Points --}}
                                <div class="mt-5">
                                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Points
                                    </h3>

                                    <div class="bg-white border border-gray-100 rounded-2xl p-4">
                                        <div class="flex items-center justify-between gap-4">
                                            <div class="min-w-0">
                                                <p
                                                    class="text-[10px] uppercase tracking-widest text-gray-400 font-black">
                                                    Available</p>
                                                <p class="text-sm font-black text-gray-900">
                                                    <span
                                                        id="availablePointsText">{{ number_format($userPoints) }}</span>
                                                    points
                                                </p>
                                            </div>

                                            <label class="inline-flex items-center gap-2 cursor-pointer select-none">
                                                <input id="toggleRedeemPoints" type="checkbox"
                                                    class="h-4 w-4 text-[#D4AF37] border-gray-300 focus:ring-[#D4AF37] rounded">
                                                <span class="text-sm font-bold text-gray-900">Use points</span>
                                            </label>
                                        </div>

                                        <div id="redeemBox" class="hidden mt-4">
                                            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                                                <div class="flex-1">
                                                    <label
                                                        class="block text-[10px] uppercase tracking-widest text-gray-400 font-black mb-2">
                                                        Points to redeem
                                                    </label>

                                                    <input id="redeemPointsInput" type="number" min="0"
                                                        step="1" inputmode="numeric" value="0"
                                                        class="w-full px-4 py-3 rounded-2xl bg-gray-50 border border-gray-100 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-[#D4AF37]/20"
                                                        placeholder="Eg: 132">

                                                    <p class="text-[11px] text-gray-500 mt-2">
                                                        Discount: <span class="font-black text-gray-900">RM <span
                                                                id="redeemRmText">0.00</span></span>
                                                    </p>
                                                </div>

                                                <button type="button" id="btnUseMaxPoints"
                                                    class="px-5 py-3 rounded-2xl bg-gray-50 text-gray-900 border border-gray-100 text-sm font-bold hover:bg-gray-100 transition">
                                                    Use Max
                                                </button>
                                            </div>

                                            <p id="redeemMsg" class="mt-3 text-xs font-bold hidden"></p>
                                        </div>
                                    </div>
                                </div>

                                {{-- 送去后端 --}}
                                <input type="hidden" name="points_redeem" id="pointsRedeemHidden" value="0">


                                {{-- Remark / Order Notes --}}
                                <div class="mt-5">
                                    <label class="block text-base font-bold text-gray-700 mb-2 ml-1">
                                        Order Remark <span class="text-gray-400 font-normal">(optional)</span>
                                    </label>

                                    <textarea name="remark" rows="3" placeholder="Eg: Please ship together / Leave at doorstep / Send as gift"
                                        class="w-full text-sm rounded-2xl border border-gray-200 focus:border-[#D4AF37] focus:ring-2 focus:ring-[#D4AF37]/20 px-4 py-3 resize-none">{{ old('remark') }}</textarea>
                                </div>


                                {{-- Checkout Button --}}
                                <div class="mt-6">
                                    <button type="submit"
                                        class="w-full relative group overflow-hidden bg-gray-900 text-white px-4 py-4 rounded-2xl font-bold text-base shadow-xl hover:shadow-black-200/50 transition-all duration-300 active:scale-[0.98]">
                                        <span class="relative z-10 flex items-center justify-center gap-2">
                                            Place Order
                                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                            </svg>
                                        </span>
                                        {{-- Hover Effect Layer --}}
                                        {{-- <div
                                            class="absolute inset-0 bg-[#8f6a10] translate-y-full group-hover:translate-y-0 transition-transform duration-300">
                                        </div> --}}
                                    </button>

                                    <div class="mt-4 flex flex-col items-center gap-2">
                                        <div class="flex items-center gap-2 text-[11px] text-gray-400 font-medium">
                                            <svg class="w-3.5 h-3.5 text-green-500" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            Secure Encrypted Checkout
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </aside>
                    </div>

                </section>
            </form>
        </div>
    </div>

    <style>
        .no-scrollbar {
            scrollbar-width: none;
            /* Firefox */
            -ms-overflow-style: none;
            /* IE/Edge */
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
            /* Chrome / Safari */
        }

        [data-address-scroller] {
            cursor: grab;
        }

        .cursor-grabbing {
            cursor: grabbing !important;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ------- 1) 点击地址卡 -> 填表单 -------
            const buttons = document.querySelectorAll('[data-address-choice]');

            const nameInput = document.querySelector('input[name="name"]');
            const phoneInput = document.querySelector('input[name="phone"]');
            const emailInput = document.querySelector('input[name="email"]');
            const line1Input = document.querySelector('input[name="address_line1"]');
            const line2Input = document.querySelector('input[name="address_line2"]');
            const postcodeInput = document.querySelector('input[name="postcode"]');
            const cityInput = document.querySelector('input[name="city"]');
            const stateSelect = document.querySelector('[data-state-select]');
            const countryInput = document.querySelector('input[name="country"]');

            // 封装一个「选中卡片」的函数，点击 / 默认都可以用它
            function activateAddress(btn) {
                if (!btn) return;

                if (nameInput) nameInput.value = btn.dataset.name || '';
                if (phoneInput) phoneInput.value = btn.dataset.phone || '';
                if (emailInput) emailInput.value = btn.dataset.email || '';
                if (line1Input) line1Input.value = btn.dataset.address_line1 || '';
                if (line2Input) line2Input.value = btn.dataset.address_line2 || '';
                if (postcodeInput) postcodeInput.value = btn.dataset.postcode || '';
                if (cityInput) cityInput.value = btn.dataset.city || '';
                if (countryInput) countryInput.value = btn.dataset.country || '';

                // 处理 state dropdown
                if (stateSelect) {
                    const stateName = btn.dataset.state || '';

                    let found = false;
                    Array.from(stateSelect.options).forEach(opt => {
                        if (opt.value === stateName) {
                            found = true;
                        }
                    });

                    if (found) {
                        stateSelect.value = stateName;
                        stateSelect.dispatchEvent(new Event('change'));
                    } else {
                        stateSelect.value = '';
                        stateSelect.dispatchEvent(new Event('change'));
                    }
                }

                // 高亮当前选中 & 还原其他
                buttons.forEach(b => {
                    b.classList.remove('border-[#D4AF37]', 'bg-[#FDF7E7]');
                    b.classList.add('border-gray-100', 'bg-white');
                });

                btn.classList.remove('border-gray-100', 'bg-white');
                btn.classList.add('border-[#D4AF37]', 'bg-[#FDF7E7]');
            }

            if (buttons.length) {
                // 点击任意地址卡 -> 选中 + 填表单
                buttons.forEach(btn => {
                    btn.addEventListener('click', () => {
                        activateAddress(btn);
                    });
                });

                // 页面载入时：如果有 data-default="1" 的卡片，就自动选中它
                const defaultBtn = Array.from(buttons).find(b => b.dataset.default === '1');
                if (defaultBtn) {
                    activateAddress(defaultBtn);
                }
            }

            // ------- 2) 水平滚动 (拖动 / 触屏 ONLY) -------
            const scroller = document.querySelector('[data-address-scroller]');
            if (!scroller) return;

            // Pointer 拖动（支持鼠标 + 触屏）⚠️ 不拦截 click
            let isDown = false;
            let startX;
            let startScrollLeft;

            scroller.addEventListener('pointerdown', function(e) {
                isDown = true;
                scroller.classList.add('cursor-grabbing');
                startX = e.clientX;
                startScrollLeft = scroller.scrollLeft;
            });

            scroller.addEventListener('pointermove', function(e) {
                if (!isDown) return;
                const dx = e.clientX - startX;
                scroller.scrollLeft = startScrollLeft - dx;
            });

            function stopDrag() {
                isDown = false;
                scroller.classList.remove('cursor-grabbing');
            }

            scroller.addEventListener('pointerup', stopDrag);
            scroller.addEventListener('pointercancel', stopDrag);
            scroller.addEventListener('pointerleave', stopDrag);
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('payment-methods-container');
            if (!container) return;

            const radios = container.querySelectorAll('.payment-radio');
            const details = container.querySelectorAll('.payment-detail');
            const groups = container.querySelectorAll('.payment-group');

            function refreshPaymentUI() {
                const checked = container.querySelector('.payment-radio:checked');
                const activeCode = checked ? checked.value : null;

                // 1) 展开 / 收起下面的 detail
                details.forEach(detail => {
                    if (detail.dataset.code === activeCode) {
                        detail.classList.remove('hidden');
                    } else {
                        detail.classList.add('hidden');
                    }
                });

                // 2) 上面卡片边框高亮跟着 radio 走
                groups.forEach(group => {
                    const radio = group.querySelector('.payment-radio');
                    const card = group.querySelector('label');

                    if (!radio || !card) return;

                    if (radio.checked) {
                        // 选中的：金边 + 淡黄背景
                        card.classList.remove('border-gray-100', 'bg-white', 'hover:border-gray-200');
                        card.classList.add('border-[#D4AF37]', 'bg-[#FDFBF7]');
                    } else {
                        // 未选中的：灰边 + 白底
                        card.classList.remove('border-[#D4AF37]', 'bg-[#FDFBF7]');
                        card.classList.add('border-gray-100', 'bg-white', 'hover:border-gray-200');
                    }
                });
            }

            radios.forEach(r => {
                r.addEventListener('change', refreshPaymentUI);
            });

            // 初始化：页面加载时根据默认选中的 payment method 调整一次
            refreshPaymentUI();
        });
    </script>


    <script>
        document.querySelector('form').addEventListener('submit', function(e) {
            const method = document.querySelector('.payment-radio:checked')?.value;
            const file = document.querySelector('input[name="payment_receipt"]');

            if (method === 'online_transfer' && !file.value) {
                e.preventDefault();
                alert('Please upload your payment receipt before placing order.');
                file.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
                file.focus();
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const stateSelect = document.querySelector('[data-state-select]');
            const shippingText = document.querySelector('[data-shipping-text]');
            const totalText = document.querySelector('[data-total-text]');
            const payAmountEl = document.querySelector('[data-pay-amount]');
            const cashbackPointsText = document.getElementById('cashbackPointsText');

            const hasPhysical = @json($hasPhysical);
            const shippingRates = @json($shippingRates);
            const subtotal = Number({{ $subtotal }});

            // Voucher UI
            const voucherStateEl = document.getElementById('voucherState');
            const voucherRow = document.getElementById('voucherRow');
            const voucherCodeText = document.getElementById('voucherCodeText');
            const voucherDiscountText = document.getElementById('voucherDiscountText');

            const shippingDiscountRow = document.getElementById('shippingDiscountRow');
            const shippingVoucherCodeText = document.getElementById('shippingVoucherCodeText');
            const shippingDiscountText = document.getElementById('shippingDiscountText');

            const voucherAppliedBox = document.getElementById('voucherAppliedBox');
            const voucherAppliedCode = document.getElementById('voucherAppliedCode');
            const voucherInputBox = document.getElementById('voucherInputBox');
            const voucherMsg = document.getElementById('voucherMsg');

            // Points redeem UI
            const availablePoints = Number(@json($userPoints)); // 从 blade 来
            const toggleRedeemPoints = document.getElementById('toggleRedeemPoints');
            const redeemBox = document.getElementById('redeemBox');
            const redeemPointsInput = document.getElementById('redeemPointsInput');
            const btnUseMaxPoints = document.getElementById('btnUseMaxPoints');
            const redeemRmText = document.getElementById('redeemRmText');
            const redeemMsg = document.getElementById('redeemMsg');

            const pointsRedeemRow = document.getElementById('pointsRedeemRow');
            const pointsRedeemLabel = document.getElementById('pointsRedeemLabel');
            const pointsRedeemRm = document.getElementById('pointsRedeemRm');

            const pointsRedeemHidden = document.getElementById('pointsRedeemHidden');

            const btnApplyVoucher = document.getElementById('btnApplyVoucher');
            const btnRemoveVoucher = document.getElementById('btnRemoveVoucher');
            const voucherCodeInput = document.getElementById('voucherCodeInput');

            if (!stateSelect || !shippingText || !totalText) return;

            // ✅ Voucher state 可变
            let currentVoucherCode = (voucherStateEl?.dataset.code || '').trim();
            let currentVoucherDiscount = Number(voucherStateEl?.dataset.discount || 0);
            let currentVoucherBenefit = (voucherStateEl?.dataset.benefit || '').trim();

            function showMsg(text, ok = true) {
                if (!voucherMsg) return;
                voucherMsg.classList.remove('hidden');
                voucherMsg.textContent = text;
                voucherMsg.className =
                    'mt-3 text-xs font-bold rounded-xl px-3 py-2 ' +
                    (ok ?
                        'bg-green-50 text-green-700 border border-green-200' :
                        'bg-red-50 text-red-700 border border-red-200');
            }

            function getShippingFee() {
                if (!hasPhysical) return 0;
                const selected = stateSelect.selectedOptions[0];
                const zone = selected ? selected.dataset.zone : null;
                if (!zone) return 0;
                return Number(shippingRates[zone] ?? 0);
            }

            function getShippingDiscount(shippingFee) {
                if (currentVoucherBenefit === 'free_shipping' && hasPhysical) return shippingFee;
                return 0;
            }

            function renderShipping() {
                if (!hasPhysical) {
                    shippingText.textContent = 'Digital Product (Free)';
                    return;
                }
                const selected = stateSelect.selectedOptions[0];
                const zone = selected ? selected.dataset.zone : null;
                if (!zone) {
                    shippingText.textContent = 'To be confirmed';
                    return;
                }
                const fee = getShippingFee();
                shippingText.textContent = (fee === 0) ? 'Free' : 'RM ' + fee.toFixed(2);
            }

            function renderVoucher() {
                const has = !!currentVoucherCode;

                if (has) {
                    voucherAppliedBox?.classList.remove('hidden');
                    if (voucherAppliedCode) voucherAppliedCode.textContent = currentVoucherCode;
                    voucherInputBox?.classList.add('hidden');

                    if (currentVoucherBenefit !== 'free_shipping' && currentVoucherDiscount > 0) {
                        voucherRow?.classList.remove('hidden');
                        if (voucherCodeText) voucherCodeText.textContent = currentVoucherCode;
                        if (voucherDiscountText) voucherDiscountText.textContent = currentVoucherDiscount.toFixed(
                            2);
                    } else {
                        voucherRow?.classList.add('hidden');
                    }

                    if (currentVoucherBenefit === 'free_shipping') {
                        shippingDiscountRow?.classList.remove('hidden');
                        if (shippingVoucherCodeText) shippingVoucherCodeText.textContent = currentVoucherCode;
                    } else {
                        shippingDiscountRow?.classList.add('hidden');
                    }
                } else {
                    voucherRow?.classList.add('hidden');
                    shippingDiscountRow?.classList.add('hidden');
                    voucherAppliedBox?.classList.add('hidden');
                    voucherInputBox?.classList.remove('hidden');
                }
            }

            // =========================
            // ✅ Points Redeem (改这里)
            // 规则：1 point = RM0.01
            // =========================
            let currentRedeemPoints = 0;

            function showRedeemMsg(text, ok = true) {
                if (!redeemMsg) return;
                redeemMsg.classList.remove('hidden');
                redeemMsg.textContent = text;
                redeemMsg.className =
                    'mt-3 text-xs font-bold rounded-xl px-3 py-2 ' +
                    (ok ?
                        'bg-green-50 text-green-700 border border-green-200' :
                        'bg-red-50 text-red-700 border border-red-200');
                setTimeout(() => redeemMsg.classList.add('hidden'), 1600);
            }

            function clamp(n, min, max) {
                n = Number(n || 0);
                if (Number.isNaN(n)) n = 0;
                return Math.max(min, Math.min(max, n));
            }

            // subtotalAfterVoucher: RM10.90 -> 1090 points
            function maxPointsBySubtotal(subtotalAfterVoucher) {
                return Math.floor(Math.max(0, Number(subtotalAfterVoucher || 0)) * 100);
            }

            // 132 -> 1.32
            function calcPointsDiscount(points) {
                return Number(points || 0) / 100;
            }

            function applyRedeemPoints(rawPoints, discountedSubtotal) {
                const maxBySub = maxPointsBySubtotal(discountedSubtotal);
                const max = Math.min(availablePoints, maxBySub);

                // ✅ 不再强制 100倍数！直接允许 1 point 级别
                const points = clamp(parseInt(rawPoints || 0, 10), 0, max);
                const discount = calcPointsDiscount(points);

                currentRedeemPoints = points;

                // UI
                if (redeemPointsInput) redeemPointsInput.value = String(points);
                if (redeemRmText) redeemRmText.textContent = discount.toFixed(2);

                // Summary row
                if (pointsRedeemRow) pointsRedeemRow.classList.toggle('hidden', points <= 0);
                if (pointsRedeemLabel) pointsRedeemLabel.textContent = points.toLocaleString();
                if (pointsRedeemRm) pointsRedeemRm.textContent = discount.toFixed(2);

                // Hidden input to server
                if (pointsRedeemHidden) pointsRedeemHidden.value = String(points);
            }

            function renderTotals() {
                const shippingFee = getShippingFee();
                const shippingDiscount = getShippingDiscount(shippingFee);

                // voucher 后 subtotal
                const discountedSubtotal = Math.max(0, subtotal - currentVoucherDiscount);

                // ✅ clamp 当前 points（基于 voucher 后 subtotal）
                applyRedeemPoints(currentRedeemPoints, discountedSubtotal);

                const pointsRm = calcPointsDiscount(currentRedeemPoints); // points -> RM
                const subtotalAfterPoints = Math.max(0, discountedSubtotal - pointsRm);

                const payableShipping = Math.max(0, shippingFee - shippingDiscount);
                const finalTotal = subtotalAfterPoints + payableShipping;

                totalText.textContent = 'RM ' + finalTotal.toFixed(2);
                if (payAmountEl) payAmountEl.textContent = 'RM ' + finalTotal.toFixed(2);

                if (shippingDiscountText) shippingDiscountText.textContent = shippingDiscount.toFixed(2);

                // Estimated Earn: 你现在是 RM1=1point（按 finalTotal）
                if (cashbackPointsText) cashbackPointsText.textContent = String(Math.floor(finalTotal));
            }

            function refreshAll() {
                renderShipping();
                renderVoucher();
                renderTotals();
            }

            // ✅ 地址必须先填好才 allow apply voucher
            function validateAddressBeforeVoucher() {
                const requiredNames = ['name', 'phone', 'email', 'address_line1', 'postcode', 'city', 'state',
                    'country'
                ];
                for (const n of requiredNames) {
                    const el = document.querySelector(`[name="${n}"]`);
                    const v = (el?.value || '').trim();
                    if (!v) {
                        showMsg('Please fill your shipping details before applying voucher.', false);
                        el?.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        el?.focus();
                        return false;
                    }
                }
                return true;
            }

            // state change updates shipping + totals
            stateSelect.addEventListener('change', refreshAll);

            // init
            refreshAll();

            // Apply voucher (no reload)
            if (btnApplyVoucher) {
                btnApplyVoucher.addEventListener('click', async () => {
                    if (!validateAddressBeforeVoucher()) return;

                    const code = (voucherCodeInput?.value || '').trim();
                    if (!code) {
                        showMsg('Please enter a voucher code.', false);
                        return;
                    }

                    try {
                        const res = await fetch("{{ route('voucher.apply') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                "Accept": "application/json",
                            },
                            body: JSON.stringify({
                                code
                            })
                        });

                        const data = await res.json().catch(() => null);

                        if (!res.ok) {
                            showMsg(data?.message || 'Voucher apply failed.', false);
                            return;
                        }

                        currentVoucherCode = data.code;
                        currentVoucherDiscount = Number(data.discount || 0);
                        currentVoucherBenefit = (data.benefit || '').trim();

                        showMsg('Voucher applied.', true);
                        refreshAll();

                    } catch (e) {
                        showMsg('Network error. Please try again.', false);
                    }
                });
            }

            // Remove voucher (no reload)
            if (btnRemoveVoucher) {
                btnRemoveVoucher.addEventListener('click', async () => {
                    try {
                        const res = await fetch("{{ route('voucher.remove') }}", {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                "Accept": "application/json",
                            }
                        });

                        const data = await res.json().catch(() => null);

                        if (!res.ok) {
                            showMsg(data?.message || 'Failed to remove voucher.', false);
                            return;
                        }

                        currentVoucherCode = '';
                        currentVoucherDiscount = 0;
                        currentVoucherBenefit = '';

                        showMsg('Voucher removed.', true);
                        refreshAll();

                    } catch (e) {
                        showMsg('Network error. Please try again.', false);
                    }
                });
            }

            // Toggle redeem UI
            if (toggleRedeemPoints && redeemBox) {
                toggleRedeemPoints.addEventListener('change', () => {
                    if (toggleRedeemPoints.checked) {
                        redeemBox.classList.remove('hidden');
                    } else {
                        redeemBox.classList.add('hidden');
                        currentRedeemPoints = 0;
                        if (redeemPointsInput) redeemPointsInput.value = 0;
                        refreshAll();
                    }
                });
            }

            // Input change
            if (redeemPointsInput) {
                redeemPointsInput.addEventListener('input', () => {
                    const discountedSubtotal = Math.max(0, subtotal - currentVoucherDiscount);
                    currentRedeemPoints = Number(redeemPointsInput.value || 0);
                    applyRedeemPoints(currentRedeemPoints, discountedSubtotal);
                    refreshAll(); // totals 更新
                });
            }

            // ✅ Use max（不再变 100）
            if (btnUseMaxPoints) {
                btnUseMaxPoints.addEventListener('click', () => {
                    const discountedSubtotal = Math.max(0, subtotal - currentVoucherDiscount);
                    const maxBySub = maxPointsBySubtotal(discountedSubtotal);
                    const max = Math.min(availablePoints, maxBySub);

                    currentRedeemPoints = max;
                    applyRedeemPoints(currentRedeemPoints, discountedSubtotal);
                    refreshAll();
                    showRedeemMsg('Max points applied.', true);
                });
            }
        });
    </script>

</x-app-layout>
