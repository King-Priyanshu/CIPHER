<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refunds Report</title>
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
        .status.approved {
            background-color: #D1FAE5;
            color: #065F46;
        }
        .status.pending {
            background-color: #FEF3C7;
            color: #92400E;
        }
        .status.rejected {
            background-color: #FEE2E2;
            color: #991B1B;
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
        <h1>CIPHER Refunds Report</h1>
        <div class="date">{{ now()->format('F j, Y') }}</div>
    </div>

    <div class="summary">
        <div class="summary-item">
            <div class="label">Total Refunds</div>
            <div class="value">{{ $refunds->count() }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Total Amount</div>
            <div class="value">₹{{ number_format($refunds->sum('amount'), 2) }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Approved Refunds</div>
            <div class="value">{{ $refunds->where('status', 'approved')->count() }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Pending Refunds</div>
            <div class="value">{{ $refunds->where('status', 'pending')->count() }}</div>
        </div>
        <div class="summary-item">
            <div class="label">Rejected Refunds</div>
            <div class="value">{{ $refunds->where('status', 'rejected')->count() }}</div>
        </div>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Refund ID</th>
                    <th>User</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Investment ID</th>
                    <th>Processed At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($refunds as $refund)
                    <tr>
                        <td>{{ $refund->created_at->format('M d, Y H:i') }}</td>
                        <td>{{ $refund->id }}</td>
                        <td>{{ $refund->user->name ?? 'N/A' }}</td>
                        <td>₹{{ number_format($refund->amount, 2) }}</td>
                        <td>
                            <span class="status {{ $refund->status }}">
                                {{ ucfirst($refund->status) }}
                            </span>
                        </td>
                        <td>{{ $refund->investment_id ?? 'N/A' }}</td>
                        <td>{{ $refund->processed_at ? $refund->processed_at->format('M d, Y H:i') : 'N/A' }}</td>
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
