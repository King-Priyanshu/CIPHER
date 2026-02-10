<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;

class PaymentHistoryController extends Controller
{
    /**
     * Display the user's payment history.
     */
    public function index()
    {
        $user = Auth::user();
        
        $payments = Payment::where('user_id', $user->id)
            ->with('subscription.plan')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Calculate totals
        $stats = [
            'total_payments' => $payments->total(),
            'total_amount' => Payment::where('user_id', $user->id)
                ->where('status', 'succeeded')
                ->sum('amount'),
            'successful' => Payment::where('user_id', $user->id)
                ->where('status', 'succeeded')
                ->count(),
            'pending' => Payment::where('user_id', $user->id)
                ->where('status', 'pending')
                ->count(),
        ];

        return view('subscriber.payments.index', compact('payments', 'stats'));
    }

    /**
     * Show a specific payment.
     */
    public function show(Payment $payment)
    {
        // Ensure the user owns this payment
        if ($payment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $payment->load('subscription.plan', 'invoice');

        return view('subscriber.payments.show', compact('payment'));
    }
}
