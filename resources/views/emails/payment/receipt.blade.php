<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; color: #1e293b; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #0f172a; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background-color: #f8fafc; padding: 20px; border: 1px solid #e2e8f0; border-top: none; }
        .amount { font-size: 24px; font-weight: bold; color: #0d9488; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #64748b; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Payment Receipt</h1>
        </div>
        <div class="content">
            <p>Hi {{ $payment->user->name }},</p>
            <p>Thank you for your payment. Here are the details:</p>
            
            <p class="amount">₹{{ number_format($payment->amount, 2) }}</p>
            
            <p><strong>Transaction ID:</strong> {{ $payment->razorpay_payment_id }}<br>
            <strong>Date:</strong> {{ $payment->paid_at->format('M d, Y h:i A') }}</p>
            
            <p>If you have any questions, please reply to this email.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} CIPHER. All rights reserved.
        </div>
    </div>
</body>
</html>
