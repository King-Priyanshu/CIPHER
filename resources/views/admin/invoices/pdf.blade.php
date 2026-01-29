<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica Neue', Arial, sans-serif; font-size: 14px; color: #1e293b; line-height: 1.5; padding: 40px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 40px; }
        .logo { font-size: 24px; font-weight: bold; color: #0d1b3e; }
        .invoice-title { font-size: 28px; font-weight: bold; color: #0d1b3e; }
        .invoice-number { font-size: 16px; color: #00bfa6; font-family: monospace; margin-top: 4px; }
        .details { display: flex; justify-content: space-between; margin-bottom: 40px; }
        .details-section { flex: 1; }
        .details-label { font-size: 10px; font-weight: bold; color: #64748b; text-transform: uppercase; margin-bottom: 4px; }
        .table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        .table th { text-align: left; padding: 12px 0; border-bottom: 2px solid #e2e8f0; font-size: 10px; font-weight: bold; color: #64748b; text-transform: uppercase; }
        .table td { padding: 16px 0; border-bottom: 1px solid #e2e8f0; }
        .table .total td { border-bottom: none; font-size: 18px; font-weight: bold; }
        .text-right { text-align: right; }
        .footer { background: #f8fafc; padding: 20px; border-radius: 8px; font-size: 12px; color: #64748b; }
        @media print { body { padding: 20px; } }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <div class="invoice-title">INVOICE</div>
            <div class="invoice-number">{{ $invoice->invoice_number }}</div>
        </div>
        <div style="text-align: right;">
            <div class="logo">CIPHER</div>
            <div style="font-size: 12px; color: #64748b;">Community Investment Platform</div>
        </div>
    </div>

    <div class="details">
        <div class="details-section">
            <div class="details-label">Bill To</div>
            <div style="font-weight: 600;">{{ $invoice->user->name ?? 'Customer' }}</div>
            <div style="color: #64748b;">{{ $invoice->user->email ?? '' }}</div>
        </div>
        <div class="details-section" style="text-align: right;">
            <div class="details-label">Invoice Date</div>
            <div>{{ $invoice->issued_at?->format('M d, Y') ?? now()->format('M d, Y') }}</div>
            <div style="margin-top: 12px;">
                <div class="details-label">Status</div>
                <div style="color: #22c55e; font-weight: 600;">{{ ucfirst($invoice->status ?? 'Paid') }}</div>
            </div>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Subscription Payment</td>
                <td class="text-right">₹{{ number_format($invoice->amount, 2) }}</td>
            </tr>
            @if($invoice->tax > 0)
            <tr>
                <td style="color: #64748b;">Tax</td>
                <td class="text-right" style="color: #64748b;">₹{{ number_format($invoice->tax, 2) }}</td>
            </tr>
            @endif
            <tr class="total">
                <td>Total</td>
                <td class="text-right">₹{{ number_format($invoice->total, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <strong>Payment Reference:</strong> {{ $invoice->payment->gateway_transaction_id ?? 'N/A' }}<br>
        <strong>Payment Gateway:</strong> {{ ucfirst($invoice->payment->gateway ?? 'N/A') }}<br><br>
        Thank you for your subscription!
    </div>

    <script>window.print();</script>
</body>
</html>
