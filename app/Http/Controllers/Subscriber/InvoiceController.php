<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class InvoiceController extends Controller
{
    /**
     * Download the invoice PDF.
     */
    public function download(Invoice $invoice)
    {
        // Security: Ensure user owns the invoice
        if ($invoice->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to invoice.');
        }

        // Generate PDF
        $pdf = Pdf::loadView('admin.invoices.pdf', compact('invoice'));
        
        return $pdf->download($invoice->invoice_number . '.pdf');
    }
}
