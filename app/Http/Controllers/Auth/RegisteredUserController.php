<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ReferralLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'ref' => ['nullable', 'string', 'max:20', 'exists:users,referral_code'],
        ]);

        $refCode = strtoupper(trim((string) $request->input('ref', '')));

        // 找上级（referrer）
        $referrer = null;
        if ($refCode !== '') {
            $referrer = User::where('referral_code', $refCode)->first();
        }

        $user = DB::transaction(function () use ($request, $referrer) {

            $newUser = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),

                // under 谁
                'referred_by' => $referrer?->id,
            ]);

            // 写 referral log（以后做 reward/cashback 用）
            if ($referrer) {
                ReferralLog::firstOrCreate([
                    'referrer_id' => $referrer->id,
                    'referred_user_id' => $newUser->id,
                ]);
            }

            return $newUser;
        });

        event(new Registered($user));

        return redirect()->route('login')
            ->with('status', 'Register successfully, Please login your account');
    }
}
