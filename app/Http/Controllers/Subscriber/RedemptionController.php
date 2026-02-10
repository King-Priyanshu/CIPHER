<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use App\Models\UserProfitLog;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RedemptionController extends Controller
{
    protected WalletService $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Redeem all available profits to wallet.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        try {
            DB::transaction(function () use ($user) {
                // Lock rows
                $pendingRedemptions = UserProfitLog::where('user_id', $user->id)
                    ->where('status', 'credited')
                    ->lockForUpdate()
                    ->get();

                $totalAmount = $pendingRedemptions->sum('amount');

                if ($totalAmount <= 0) {
                    throw new \Exception('No profits available for redemption.');
                }

                // Credit Wallet
                $this->walletService->credit(
                    $user,
                    $totalAmount,
                    'profit_redemption',
                    'Redemption of accumulated profits',
                    $pendingRedemptions->first() // Linking to one log as reference, or could be null
                );

                // Update Logs
                UserProfitLog::whereIn('id', $pendingRedemptions->pluck('id'))
                    ->update(['status' => 'redeemed', 'updated_at' => now()]);
                
                Log::info('Profits redeemed to wallet', [
                    'user_id' => $user->id,
                    'amount' => $totalAmount
                ]);
            });

            return redirect()->back()->with('success', 'Profits successfully redeemed to your wallet!');

        } catch (\Exception $e) {
            Log::error('Redemption failed', ['error' => $e->getMessage(), 'user_id' => $user->id]);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
