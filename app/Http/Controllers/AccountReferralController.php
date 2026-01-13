<?php

namespace App\Http\Controllers;

use App\Models\ReferralLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AccountReferralController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $refLink = url('/register?ref=' . $user->referral_code);

        $referrals = ReferralLog::with(['referredUser:id,name,email,created_at'])
            ->where('referrer_id', $user->id)
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $stats = [
            'total' => ReferralLog::where('referrer_id', $user->id)->count(),
            'points' => 0,
        ];

        return view('account.referral.index', compact('user', 'refLink', 'referrals', 'stats'));
    }
}
