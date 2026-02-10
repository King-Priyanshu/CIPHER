<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WalletController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Store a newly created resource in storage.
     * Manually credit or debit a user's wallet.
     */
    public function store(Request $request, User $user)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:credit,debit',
            'description' => 'required|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($request, $user) {
                $amount = $request->amount;
                $description = "Admin Adjustment: " . $request->description;

                if ($request->type === 'credit') {
                    $this->walletService->credit($user, $amount, 'admin_adjustment', $description);
                } else {
                    $this->walletService->debit($user, $amount, 'admin_adjustment', $description);
                }
            });

            return back()->with('success', ucfirst($request->type) . ' of ₹' . number_format($request->amount, 2) . ' processed successfully.');

        } catch (\Exception $e) {
            Log::error('Admin Wallet Adjustment Failed', ['error' => $e->getMessage()]);
            return back()->with('error', 'Transaction failed: ' . $e->getMessage());
        }
    }
}
