<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    /**
     * Display all invoices.
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['user', 'payment'])
            ->orderBy('issued_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $invoices = $query->paginate(20);

        $stats = [
            'total_invoiced' => Invoice::sum('total'),
            'paid_invoices' => Invoice::where('status', 'paid')->count(),
            'pending_invoices' => Invoice::where('status', 'pending')->count(),
        ];

        return view('admin.invoices.index', compact('invoices', 'stats'));
    }

    /**
     * Show invoice details.
     */
    public function show(Invoice $invoice)
    {
        $invoice->load(['user', 'payment']);

        return view('admin.invoices.show', compact('invoice'));
    }

    /**
     * Generate invoice for a payment.
     */
    public function generate(Payment $payment)
    {
        // Check if invoice already exists
        if ($payment->invoice) {
            return redirect()->route('admin.invoices.show', $payment->invoice)
                ->with('info', 'Invoice already exists for this payment.');
        }

        $invoiceNumber = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        $invoice = Invoice::create([
            'user_id' => $payment->user_id,
            'payment_id' => $payment->id,
            'invoice_number' => $invoiceNumber,
            'amount' => $payment->amount,
            'tax' => 0, // Add tax calculation if needed
            'total' => $payment->amount,
            'status' => 'paid',
            'issued_at' => now(),
            'paid_at' => $payment->paid_at ?? now(),
        ]);

        return redirect()->route('admin.invoices.show', $invoice)
            ->with('success', 'Invoice generated successfully.');
    }

    /**
     * Download invoice as PDF.
     */
    public function download(Invoice $invoice)
    {
        // Generate PDF view
        return view('admin.invoices.pdf', compact('invoice'));
    }
}
