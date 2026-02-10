<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReferralController extends Controller
{
    /**
     * Display the referral tree/list.
     */
    public function index()
    {
        // Get users with their referral counts and referrer info
        $users = User::with('referrer')
                    ->withCount('referrals')
                    ->latest()
                    ->paginate(20);

        return view('admin.referrals.index', compact('users'));
    }

    /**
     * Generate a referral code for a specific user (Admin/Manager action).
     */
    public function generate(Request $request, User $user)
    {
        // Ensure only admins/managers can do this (middleware handles RBAC generally, 
        // but logic here enforces they are generating FOR someone or themselves)
        
        if (empty($user->referral_code)) {
            $user->referral_code = strtoupper(Str::random(8));
            $user->save();
        }

        return back()->with('success', 'Referral code generated: ' . $user->referral_code);
    }
}
