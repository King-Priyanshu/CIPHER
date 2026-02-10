<?php

namespace App\Services;

use App\Models\User;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class ReferralService
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Store referral code in session if present in request.
     */
    public function captureReferral(string $code)
    {
        // Validate code exists and is eligible (Admin/Manager check handled at registration maybe?)
        // Or check here:
        $referrer = User::where('referral_code', $code)->first();
        
        if ($referrer && $referrer->hasRole('admin') || $referrer->hasRole('manager')) {
            Session::put('referral_code', $code);
            return true;
        }
        return false;
    }

    /**
     * Link new user to referrer.
     */
    public function registerReferral(User $user, ?string $code): void
    {
        if (!$code) return;

        $referrer = User::where('referral_code', $code)->first();

        if ($referrer) {
             // Validate roles again if strict
             if ($referrer->hasRole('admin') || $referrer->hasRole('manager')) {
                 $user->update(['referred_by' => $referrer->id]);
                 Log::info("User {$user->id} linked to referrer {$referrer->id}");
             }
        }
    }

    /**
     * Distribute referral bonus on investment.
     * 
     * @param mixed $investment (UserSubscription or ProjectInvestment)
     */
    public function distributeBonus($investment)
    {
        $user = $investment->user;
        $referrer = $user->referrer;

        if (!$referrer) return;

        // Bonus Logic: e.g., 5% of investment amount
        $bonusPercentage = 0.05; 
        $bonusAmount = $investment->amount * $bonusPercentage;

        if ($bonusAmount <= 0) return;

        // Credit to Referrer Wallet
        $this->walletService->credit(
            $referrer,
            $bonusAmount,
            'referral_bonus',
            "Referral Bonus for User {$user->name} Investment",
            $investment
        );

        Log::info("Referral bonus of {$bonusAmount} credited to {$referrer->id}");
    }
}
