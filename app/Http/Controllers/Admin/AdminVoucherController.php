<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminVoucherController extends Controller
{
    public function index(Request $request)
    {
        $q = Voucher::query();

        if ($request->filled('keyword')) {
            $kw = $request->string('keyword');
            $q->where(function ($qq) use ($kw) {
                $qq->where('code', 'like', "%{$kw}%")
                    ->orWhere('name', 'like', "%{$kw}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->string('status') === 'active') $q->where('is_active', true);
            if ($request->string('status') === 'inactive') $q->where('is_active', false);
        }

        $vouchers = $q->latest()->paginate(15)->withQueryString();

        return view('admin.vouchers.index', compact('vouchers'));
    }

    public function create()
    {
        $voucher = new Voucher();
        return view('admin.vouchers.form', compact('voucher'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $data['code'] = strtoupper(trim($data['code']));
        Voucher::create($data);

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher created.');
    }

    public function edit(Voucher $voucher)
    {
        return view('admin.vouchers.form', compact('voucher'));
    }

    public function update(Request $request, Voucher $voucher)
    {
        $data = $this->validateData($request, $voucher->id);

        $data['code'] = strtoupper(trim($data['code']));
        $voucher->update($data);

        return redirect()->route('admin.vouchers.index')->with('success', 'Voucher updated.');
    }

    public function toggle(Voucher $voucher)
    {
        $voucher->update(['is_active' => !$voucher->is_active]);
        return back()->with('success', 'Voucher status updated.');
    }

    private function validateData(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('vouchers', 'code')->ignore($ignoreId),
            ],
            'name' => ['required', 'string', 'max:120'],
            'type' => ['required', Rule::in(['fixed', 'percent'])],
            'value' => ['required', 'numeric', 'min:0.01'],
            'min_spend' => ['nullable', 'numeric', 'min:0'],
            'usage_limit' => ['nullable', 'integer', 'min:1'],
            'starts_at' => ['nullable', 'date'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['nullable', 'boolean'],
        ]);
    }
}
