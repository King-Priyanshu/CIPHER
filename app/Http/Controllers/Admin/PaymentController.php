<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display all payments.
     */
    public function index(Request $request)
    {
        $query = Payment::with(['user', 'subscription'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('gateway')) {
            $query->where('gateway', $request->gateway);
        }

        $payments = $query->paginate(20);

        $stats = [
            'total_revenue' => Payment::where('status', 'succeeded')->sum('amount'),
            'total_payments' => Payment::count(),
            'successful' => Payment::where('status', 'succeeded')->count(),
            'failed' => Payment::where('status', 'failed')->count(),
        ];

        return view('admin.payments.index', compact('payments', 'stats'));
    }

    /**
     * Show payment details.
     */
    public function show(Payment $payment)
    {
        $payment->load(['user', 'subscription', 'invoice']);

        return view('admin.payments.show', compact('payment'));
    }
}
