<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; color: #1e293b; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #0f172a; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { background-color: #f8fafc; padding: 20px; border: 1px solid #e2e8f0; border-top: none; }
        .btn { display: inline-block; background-color: #0d9488; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 20px; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #64748b; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to CIPHER!</h1>
        </div>
        <div class="content">
            <p>Hi {{ $subscription->user->name }},</p>
            <p>Your subscription has been successfully activated.</p>
            
            <p><strong>Plan:</strong> {{ $subscription->plan->name ?? 'Standard Plan' }}<br>
            <strong>Start Date:</strong> {{ $subscription->starts_at->format('M d, Y') }}<br>
            <strong>Next Renewal:</strong> {{ $subscription->current_period_end->format('M d, Y') }}</p>
            
            <p>You can now access your subscriber dashboard to view upcoming projects.</p>
            
            <div style="text-align: center;">
                <a href="{{ route('subscriber.dashboard') }}" class="btn">Go to Dashboard</a>
            </div>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} CIPHER. All rights reserved.
        </div>
    </div>
</body>
</html>
