<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Refund;
use App\Models\User;
use App\Models\ProjectInvestment;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\ActivityLogger;
use App\Models\Payment;
use Carbon\Carbon;

class FinanceController extends Controller
{
    protected ActivityLogger $logger;

    public function __construct(WalletService $walletService, ActivityLogger $logger)
    {
        $this->walletService = $walletService;
        $this->logger = $logger;
    }

    /**
     * Display finance dashboard.
     */
    public function dashboard()
    {
        // Get financial metrics
        $metrics = [
            'total_revenue' => Payment::where('status', 'succeeded')->sum('amount') ?? 0,
            'monthly_revenue' => Payment::where('status', 'succeeded')
                ->where('paid_at', '>=', now()->startOfMonth())
                ->sum('amount') ?? 0,
            'pending_payments' => Payment::where('status', 'pending')->sum('amount') ?? 0,
            'total_refunds' => Refund::where('status', 'approved')->sum('amount') ?? 0,
        ];

        // Revenue data for chart (last 30 days)
        $revenueData = Payment::select(
                DB::raw('DATE(paid_at) as date'),
                DB::raw('SUM(amount) as total')
            )
            ->where('status', 'succeeded')
            ->where('paid_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $revenueLabels = $revenueData->pluck('date')->map(function($date) {
            return Carbon::parse($date)->format('M d');
        });
        $revenueValues = $revenueData->pluck('total');

        // Payment status distribution
        $paymentStatusData = Payment::select(
                'status',
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('status')
            ->get();

        $paymentStatusLabels = $paymentStatusData->pluck('status')->map(function($status) {
            return ucfirst($status);
        });
        $paymentStatusValues = $paymentStatusData->pluck('count');

        // Recent transactions
        $recentTransactions = Payment::with('user')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.finance.dashboard', [
            'metrics' => $metrics,
            'revenueData' => [
                'labels' => $revenueLabels,
                'data' => $revenueValues
            ],
            'paymentStatusData' => [
                'labels' => $paymentStatusLabels,
                'data' => $paymentStatusValues
            ],
            'recentTransactions' => $recentTransactions
        ]);
    }

    /**
     * Display all transactions.
     */
    public function transactions(Request $request)
    {
        $query = Payment::with('user');

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('start_date') && $request->start_date) {
            $query->where('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->where('created_at', '<=', $request->end_date . ' 23:59:59');
        }

        $transactions = $query->latest()->paginate(20);

        return view('admin.finance.transactions', compact('transactions'));
    }

    /**
     * Export transactions to CSV.
     */
    public function exportCsv()
    {
        $fileName = 'transactions-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $transactions = Payment::with('user')->get();

        $callback = function() use ($transactions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Transaction ID', 'User', 'Amount', 'Status', 'Type']);

            foreach ($transactions as $transaction) {
                fputcsv($file, [
                    $transaction->created_at->format('Y-m-d H:i:s'),
                    $transaction->id,
                    $transaction->user->name ?? 'N/A',
                    $transaction->amount,
                    $transaction->status,
                    $transaction->type
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export transactions to PDF.
     */
    public function exportPdf()
    {
        $transactions = Payment::with('user')->get();

        $pdf = \PDF::loadView('admin.finance.transactions.pdf', compact('transactions'));
        
        return $pdf->download('transactions-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Export refunds to CSV.
     */
    public function exportRefundsCsv()
    {
        $fileName = 'refunds-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ];

        $refunds = Refund::with(['user', 'investment'])->get();

        $callback = function() use ($refunds) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Refund ID', 'User', 'Amount', 'Status', 'Investment ID']);

            foreach ($refunds as $refund) {
                fputcsv($file, [
                    $refund->created_at->format('Y-m-d H:i:s'),
                    $refund->id,
                    $refund->user->name ?? 'N/A',
                    $refund->amount,
                    $refund->status,
                    $refund->investment_id
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export refunds to PDF.
     */
    public function exportRefundsPdf()
    {
        $refunds = Refund::with(['user', 'investment'])->get();

        $pdf = \PDF::loadView('admin.finance.refunds.pdf', compact('refunds'));
        
        return $pdf->download('refunds-' . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Display a listing of refunds.
     */
    public function refunds(Request $request)
    {
        $query = Refund::with(['user', 'investment', 'subscription']);

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $refunds = $query->latest()->paginate(20);

        return view('admin.finance.refunds.index', compact('refunds'));
    }

    /**
     * Approve a refund request.
     */
    public function approveRefund(Request $request, Refund $refund)
    {
        if ($refund->status !== 'pending') {
            return back()->with('error', 'Refund request is not pending.');
        }

        $request->validate([
            'admin_note' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($refund, $request) {
                // Credit User Wallet
                $transaction = $this->walletService->credit(
                    $refund->user,
                    $refund->amount,
                    'refund',
                    "Refund approved: " . ($request->admin_note ?? 'Request #' . $refund->id),
                    $refund
                );

                $refund->update([
                    'status' => 'approved', // Or 'processed' immediately if wallet credit is final
                    'processed_at' => now(),
                    'admin_note' => $request->admin_note,
                    'transaction_id' => $transaction->id,
                ]);
                
                // If linked to investment, maybe update investment status?
                if ($refund->investment) {
                    $refund->investment->update(['status' => 'refunded']); // Assuming status exists
                }

                $this->logger->logFinancial('refund.approved', "Refund #{$refund->id} approved. Amount: {$refund->amount}", $refund);
            });

            return back()->with('success', 'Refund approved and wallet credited.');

        } catch (\Exception $e) {
            Log::error("Refund approval failed: " . $e->getMessage());
            return back()->with('error', 'Failed to approve refund: ' . $e->getMessage());
        }
    }

    /**
     * Reject a refund request.
     */
    public function rejectRefund(Request $request, Refund $refund)
    {
        if ($refund->status !== 'pending') {
            return back()->with('error', 'Refund request is not pending.');
        }

        $request->validate([
            'admin_note' => 'required|string',
        ]);

        $refund->update([
            'status' => 'rejected',
            'admin_note' => $request->admin_note,
        ]);

        $this->logger->log('refund.rejected', "Refund #{$refund->id} rejected.", get_class($refund), $refund->id, 'warning');

        return back()->with('success', 'Refund rejected.');
    }
}
