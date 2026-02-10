<!DOCTYPE html>
<html>
<head>
    <title>Payment Reminder</title>
</head>
<body>
    <h1>Upcoming Payment Reminder</h1>
    <p>Hello,</p>
    <p>This is a reminder that your payment for <strong>{{ $planName }}</strong> of <strong>{{ number_format($amount, 2) }}</strong> is due on <strong>{{ $dueDate }}</strong>.</p>
    <p>Please ensure your account has sufficient funds or your payment method is up to date.</p>
    <p>Thank you,<br>{{ config('app.name') }} Team</p>
</body>
</html>
