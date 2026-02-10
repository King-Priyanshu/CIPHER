<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Mock invoices for now as we don't have a full invoice system populated
        $invoices = \App\Models\Invoice::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Calculate statistics
        $totalAmount = \App\Models\Invoice::where('user_id', $user->id)
            ->sum('amount');

        $paidInvoices = \App\Models\Invoice::where('user_id', $user->id)
            ->where('status', 'paid')
            ->count();

        $pendingInvoices = \App\Models\Invoice::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        return view('subscriber.billing.index', compact('invoices', 'totalAmount', 'paidInvoices', 'pendingInvoices'));
    }
}
