<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Order;
use App\Models\PointTransaction;
use App\Models\ReferralLog;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $q = User::query();

        if ($request->filled('keyword')) {
            $kw = $request->string('keyword');
            $q->where(function ($qq) use ($kw) {
                $qq->where('name', 'like', "%{$kw}%")
                    ->orWhere('email', 'like', "%{$kw}%");
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $q->where('is_active', true);
            } else {
                $q->where(function ($qq) {
                    $qq->where('is_active', false)
                        ->orWhereNull('is_active');
                });
            }
        }


        if ($request->filled('verified')) {
            if ($request->verified === 'verified') {
                $q->where('is_verified', true);
            } else {
                $q->where(function ($qq) {
                    $qq->where('is_verified', false)
                        ->orWhereNull('is_verified');
                });
            }
        }


        $users = $q->latest()->paginate(15)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function show(User $user)
    {
        $user->load('addresses', 'defaultAddress', 'referrer:id,name,email');

        $pointsBalance = (int) ($user->points_balance ?? 0);

        $invitedCount = User::where('referred_by', $user->id)->count();

        $referralEarned = (int) PointTransaction::where('user_id', $user->id)
            ->where('source', 'referral')
            ->where('type', 'earn')
            ->sum('points');

        $recentPoints = PointTransaction::with(['order:id,order_no'])
            ->where('user_id', $user->id)
            ->latest()
            ->limit(10)
            ->get();

        $recentOrders = Order::where('user_id', $user->id)
            ->latest()
            ->withCount('items')
            ->take(7)
            ->get();

        return view('admin.users.show', compact(
            'user',
            'recentOrders',
            'pointsBalance',
            'invitedCount',
            'referralEarned',
            'recentPoints'
        ));
    }

    public function edit(User $user)
    {
        $user->load('addresses');

        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],

            'ic_number'  => ['nullable', 'string', 'max:30'],
            'birth_date' => ['nullable', 'date'],
            'ic_image'   => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            'password'   => ['nullable', 'string', 'min:8'],
            'is_active'  => ['nullable', 'boolean'],
            'is_verified' => ['nullable', 'boolean'],
        ]);

        $user->name  = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'] ?? null;

        // identity fields
        $user->ic_number  = $data['ic_number'] ?? $user->ic_number;
        $user->birth_date = $data['birth_date'] ?? $user->birth_date;

        // active
        $user->is_active = (bool) ($data['is_active'] ?? false);

        // verified
        $isVerified = (bool) ($data['is_verified'] ?? false);
        if ($isVerified && !$user->is_verified) $user->verified_at = now();
        if (!$isVerified) $user->verified_at = null;
        $user->is_verified = $isVerified;

        // password
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        // ic image upload
        if ($request->hasFile('ic_image')) {
            if ($user->ic_image && Storage::disk('public')->exists($user->ic_image)) {
                Storage::disk('public')->delete($user->ic_image);
            }
            $user->ic_image = $request->file('ic_image')->store('ic-images', 'public');
        }

        $user->save();

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    public function adjustPoints(Request $request, \App\Models\User $user)
    {
        $data = $request->validate([
            'action' => 'required|in:add,deduct',
            'points' => 'required|integer|min:1',
            'note'   => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($data, $user) {
            $user = \App\Models\User::whereKey($user->id)->lockForUpdate()->first();

            $points = (int) $data['points'];
            $isDeduct = $data['action'] === 'deduct';

            // ✅ 防止扣到负数
            if ($isDeduct && (int)($user->points_balance ?? 0) < $points) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'points' => 'Insufficient points balance to deduct.',
                ]);
            }

            PointTransaction::create([
                'user_id' => $user->id,
                'type'    => $isDeduct ? 'spend' : 'earn',
                'source'  => 'admin_adjust',
                'points'  => $points,
                'note'    => $data['note'] ?: ($isDeduct ? 'Admin deducted points' : 'Admin added points'),
            ]);

            if ($isDeduct) {
                $user->decrement('points_balance', $points);
            } else {
                $user->increment('points_balance', $points);
            }
        });

        return back()->with('success', 'Points updated successfully.');
    }
}
