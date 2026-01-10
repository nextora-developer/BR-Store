@extends('admin.layouts.app')

@section('content')
    <div class="flex items-start justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">
                {{ $voucher->exists ? 'Edit Voucher' : 'New Voucher' }}
            </h1>
            <p class="text-sm text-gray-500 mt-1">Configure voucher rules and validity</p>
        </div>

        <a href="{{ route('admin.vouchers.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-gray-200
                   text-sm font-semibold text-gray-600 hover:bg-gray-50 transition shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            <span>Back</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <form method="POST"
            action="{{ $voucher->exists ? route('admin.vouchers.update', $voucher) : route('admin.vouchers.store') }}"
            class="space-y-6">
            @csrf
            @if ($voucher->exists)
                @method('PUT')
            @endif

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Code</label>
                    <input name="code" value="{{ old('code', $voucher->code) }}" placeholder="WELCOME10"
                        class="mt-2 w-full px-4 py-3 bg-gray-50 border-transparent rounded-xl text-sm
                               focus:bg-white focus:ring-2 focus:ring-[#D4AF37]/20 focus:border-[#D4AF37] transition-all">
                    @error('code')
                        <p class="text-xs text-red-600 mt-2 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Name</label>
                    <input name="name" value="{{ old('name', $voucher->name) }}" placeholder="Welcome discount"
                        class="mt-2 w-full px-4 py-3 bg-gray-50 border-transparent rounded-xl text-sm
                               focus:bg-white focus:ring-2 focus:ring-[#D4AF37]/20 focus:border-[#D4AF37] transition-all">
                    @error('name')
                        <p class="text-xs text-red-600 mt-2 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid md:grid-cols-4 gap-4">
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Type</label>
                    <select name="type"
                        class="mt-2 w-full px-4 py-3 bg-gray-50 border-transparent rounded-xl text-sm
                               focus:bg-white focus:ring-2 focus:ring-[#D4AF37]/20 focus:border-[#D4AF37] transition-all">
                        <option value="fixed" @selected(old('type', $voucher->type) === 'fixed')>Fixed (RM)</option>
                        <option value="percent" @selected(old('type', $voucher->type) === 'percent')>Percent (%)</option>
                    </select>
                    @error('type')
                        <p class="text-xs text-red-600 mt-2 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Value</label>
                    <input name="value" type="number" step="0.01" min="0"
                        value="{{ old('value', $voucher->value) }}" placeholder="10"
                        class="mt-2 w-full px-4 py-3 bg-gray-50 border-transparent rounded-xl text-sm
                               focus:bg-white focus:ring-2 focus:ring-[#D4AF37]/20 focus:border-[#D4AF37] transition-all">
                    @error('value')
                        <p class="text-xs text-red-600 mt-2 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Min Spend</label>
                    <input name="min_spend" type="number" step="0.01" min="0"
                        value="{{ old('min_spend', $voucher->min_spend) }}" placeholder="(optional)"
                        class="mt-2 w-full px-4 py-3 bg-gray-50 border-transparent rounded-xl text-sm
                               focus:bg-white focus:ring-2 focus:ring-[#D4AF37]/20 focus:border-[#D4AF37] transition-all">
                    @error('min_spend')
                        <p class="text-xs text-red-600 mt-2 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Usage Limit</label>
                    <input name="usage_limit" type="number" min="1"
                        value="{{ old('usage_limit', $voucher->usage_limit) }}" placeholder="(optional)"
                        class="mt-2 w-full px-4 py-3 bg-gray-50 border-transparent rounded-xl text-sm
                               focus:bg-white focus:ring-2 focus:ring-[#D4AF37]/20 focus:border-[#D4AF37] transition-all">
                    @error('usage_limit')
                        <p class="text-xs text-red-600 mt-2 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Starts At</label>
                    <input name="starts_at" type="datetime-local"
                        value="{{ old('starts_at', $voucher->starts_at ? $voucher->starts_at->format('Y-m-d\TH:i') : '') }}"
                        class="mt-2 w-full px-4 py-3 bg-gray-50 border-transparent rounded-xl text-sm
                               focus:bg-white focus:ring-2 focus:ring-[#D4AF37]/20 focus:border-[#D4AF37] transition-all">
                    @error('starts_at')
                        <p class="text-xs text-red-600 mt-2 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Expires At</label>
                    <input name="expires_at" type="datetime-local"
                        value="{{ old('expires_at', $voucher->expires_at ? $voucher->expires_at->format('Y-m-d\TH:i') : '') }}"
                        class="mt-2 w-full px-4 py-3 bg-gray-50 border-transparent rounded-xl text-sm
                               focus:bg-white focus:ring-2 focus:ring-[#D4AF37]/20 focus:border-[#D4AF37] transition-all">
                    @error('expires_at')
                        <p class="text-xs text-red-600 mt-2 font-semibold">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center gap-3">
                <input id="is_active" type="checkbox" name="is_active" value="1" @checked(old('is_active', $voucher->exists ? $voucher->is_active : true))
                    class="h-5 w-5 rounded border-gray-300 text-[#D4AF37] focus:ring-[#D4AF37]">
                <label for="is_active" class="text-sm font-semibold text-gray-700">Active</label>
            </div>

            <div class="flex gap-3 pt-2">
                <button
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gray-900 text-white text-sm font-semibold
                           hover:bg-gray-800 transition-all shadow-md hover:shadow-lg active:scale-95">
                    Save
                </button>

                <a href="{{ route('admin.vouchers.index') }}"
                    class="px-6 py-3 rounded-xl border border-gray-100 text-gray-600 hover:bg-gray-50
                           transition-all text-sm flex items-center justify-center">
                    Cancel
                </a>
            </div>
        </form>
    </div>
@endsection
