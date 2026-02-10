<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transactions Report</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #00BFA6;
        }
        .header h1 {
            color: #1A2F4B;
            margin: 0;
            font-size: 24px;
        }
        .header .date {
            color: #64748B;
            margin-top: 5px;
        }
        .table-container {
            margin: 20px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th {
            background-color: #F8FAFC;
            color: #1A2F4B;
            padding: 8px;
            text-align: left;
            border: 1px solid #E2E8F0;
            font-weight: bold;
        }
        table td {
            padding: 8px;
            border: 1px solid #E2E8F0;
        }
        .status {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
        }
        .status.succeeded {
            background-color: #D1FAE5;
            color: #065F46;
        }
        .status.pending {
            background-color: #FEF3C7;
            color: #92400E;
        }
        .status.failed {
            background-color: #FEE2E2;
            color: #991B1B;
        }
        .status.refunded {
            background-color: #DDD6FE;
            color: #5B21B6;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #E2E8F0;
            color: #64748B;
            font-size: 10px;
        }
        .summary {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #F8FAFC;
            border-radius: 5px;
        }
        .summary-item {
            text-align: right;
        }
        .summary-item .label {
            font-size: 10px;
            color: #64748B;
        }
        .summary-item .value {
            font-weight: bold;
            color: #1A2F4B;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>CIPHER Transactions Report</h1>
        <div class="date">{{ now()->format('F j, Y') }}</div>
    </div>

    <div class="summary">
        <div class="summary-item">
            <div class="label">Total Transactions</div>
            <div class="value">{{ $transactions->count() }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Total Amount</div>
            <div class="value">₹{{ number_format($transactions->sum('amount'), 2) }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Date Range</div>
            <div class="value">{{ $transactions->min('created_at')->format('M j, Y') }} - {{ $transactions->max('created_at')->format('M j, Y') }}</div>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Transaction ID</th>
                    <th>User</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Type</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                        <td>{{ $transaction->id }}</td>
                        <td>{{ $transaction->user->name ?? 'N/A' }}</td>
                        <td>₹{{ number_format($transaction->amount, 2) }}</td>
                        <td>
                            <span class="status {{ $transaction->status }}">
                                {{ ucfirst($transaction->status) }}
                            </span>
                        </td>
                        <td>{{ ucfirst($transaction->type) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Generated on {{ now()->format('F j, Y \a\t H:i:s') }}</p>
        <p>This is an automated report. Please contact support for assistance.</p>
    </div>
</body>
</html>
