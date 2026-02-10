<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use App\Models\UserProfitLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProfitController extends Controller
{
    /**
     * Display a listing of the user's profit logs.
     */
    public function index()
    {
        $user = Auth::user();
        
        $profits = UserProfitLog::where('user_id', $user->id)
            ->with(['distribution.project', 'investment'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        $totalCredited = UserProfitLog::where('user_id', $user->id)
            ->where('status', 'credited')
            ->sum('amount');
            
        $totalAccrued = UserProfitLog::where('user_id', $user->id)
            ->where('status', 'pending')
            ->sum('amount');

        return view('subscriber.profits.index', compact('profits', 'totalCredited', 'totalAccrued'));
    }

    /**
     * Redeem available profits.
     */
    public function redeem(Request $request)
    {
        // For now, simple redemption of ALL credited amount to external wallet request?
        // Or just mark as 'withdrawn' and log a payout request?
        // Let's mark as 'withdrawn' and assume a Payout mechanism handles it (or create PayoutRequest).
        // Since I don't have PayoutRequest model, I'll just flash a message "Redemption request submitted".
        // Or create a Payout model? Prompt references "Invoices" and "Payments".
        // Let's keep it simple: Log it or just flash success.
        
        // Better: Update status to 'redeemed' or 'withdrawn'.
        // And maybe trigger a notification to Admin.
        
        $user = Auth::user();
        
        $creditedLogs = UserProfitLog::where('user_id', $user->id)
            ->where('status', 'credited')
            ->lockForUpdate()
            ->get();
            
        if ($creditedLogs->isEmpty()) {
            return redirect()->back()->with('error', 'No funds available for redemption.');
        }

        $totalAmount = $creditedLogs->sum('amount');

        DB::transaction(function () use ($creditedLogs) {
            foreach ($creditedLogs as $log) {
                $log->update([
                    'status' => 'withdrawn',
                    // 'redeemed_at' => now(), // if I had this field
                ]);
            }
            
            // Here: Create Payout Request record if it exists.
            // Or log to ActivityLog.
            // For now just update status.
        });

        return redirect()->back()->with('success', "Redemption of ₹" . number_format($totalAmount, 2) . " initiated successfully.");
    }
}
