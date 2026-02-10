# CIPHER Project - Issue Fix Guide

## Overview

This guide provides step-by-step instructions to fix all issues identified in the comprehensive audit report. The issues are organized by priority (critical, high, medium, low) to help you focus on the most important fixes first.

---

## Priority: Critical (Must Fix Immediately)

### 1. Stripe Payment Gateway Implementation

**File:** `app/Services/Payments/StripePaymentGateway.php`

**Current Status:** All methods are TODO placeholders

**Steps to Fix:**

1. Install Stripe PHP library:
   ```bash
   composer require stripe/stripe-php
   ```

2. Update the StripePaymentGateway class with actual implementation:

```php
// app/Services/Payments/StripePaymentGateway.php
<?php

namespace App\Services\Payments;

use Stripe\Stripe;
use Stripe\Charge;
use Stripe\Customer;
use Stripe\Subscription as StripeSubscription;

class StripePaymentGateway implements PaymentGatewayInterface
{
    public function __construct()
    {
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function createSubscription($user, $plan, $paymentMethod)
    {
        // Implement Stripe subscription creation
    }

    public function cancelSubscription($subscriptionId)
    {
        // Implement Stripe subscription cancellation
    }

    public function processPayment($amount, $paymentMethod, $description)
    {
        // Implement Stripe payment processing
    }

    public function handleWebhook($payload, $signature)
    {
        // Implement Stripe webhook handling
    }

    public function verifyWebhookSignature($payload, $signature)
    {
        // Implement Stripe webhook signature verification
    }
}
```

3. Add Stripe configuration to .env file:
   ```
   STRIPE_KEY=your_stripe_publishable_key
   STRIPE_SECRET=your_stripe_secret_key
   STRIPE_WEBHOOK_SECRET=your_stripe_webhook_secret
   ```

4. Update config/services.php with Stripe configuration:
   ```php
   'stripe' => [
       'key' => env('STRIPE_KEY'),
       'secret' => env('STRIPE_SECRET'),
       'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
   ],
   ```

### 2. Connect Razorpay to Checkout Flow

**Files:** 
- `app/Services/Payment/RazorpayService.php`
- `app/Http/Controllers/CheckoutController.php`
- `resources/views/checkout.blade.php`

**Current Status:** Razorpay service implemented but not connected to checkout flow

**Steps to Fix:**

1. Add Razorpay configuration to .env file:
   ```
   RAZORPAY_KEY=your_razorpay_key
   RAZORPAY_SECRET=your_razorpay_secret
   RAZORPAY_WEBHOOK_SECRET=your_razorpay_webhook_secret
   ```

2. Update config/services.php with Razorpay configuration:
   ```php
   'razorpay' => [
       'key' => env('RAZORPAY_KEY'),
       'secret' => env('RAZORPAY_SECRET'),
       'webhook_secret' => env('RAZORPAY_WEBHOOK_SECRET'),
   ],
   ```

3. Modify CheckoutController to use Razorpay instead of mock payment service:

```php
// app/Http/Controllers/CheckoutController.php
public function processPayment(Request $request)
{
    $paymentMethod = $request->input('payment_method');
    
    if ($paymentMethod === 'razorpay') {
        $razorpayService = new RazorpayService();
        return $razorpayService->processPayment($request);
    } elseif ($paymentMethod === 'stripe') {
        $stripeService = new StripePaymentGateway();
        return $stripeService->processPayment($request);
    }
    
    // Fallback to other payment methods
}
```

4. Update checkout.blade.php to include Razorpay payment option:
   - Add Razorpay payment button
   - Include Razorpay SDK
   - Implement payment form submission

### 3. Integrate Double-Entry Ledger System

**Files:**
- `app/Services/JournalEntryService.php`
- `app/Services/WalletService.php`
- `app/Services/Payment/RazorpayService.php`
- `app/Http/Controllers/CheckoutController.php`

**Current Status:** JournalEntryService not integrated with WalletService or Payment processing

**Steps to Fix:**

1. Modify JournalEntryService to create entries for wallet transactions:

```php
// app/Services/JournalEntryService.php
public function createWalletTransactionEntry($walletTransaction)
{
    $this->createEntry([
        'user_id' => $walletTransaction->user_id,
        'transaction_type' => $walletTransaction->type,
        'amount' => $walletTransaction->amount,
        'reference_id' => $walletTransaction->id,
        'reference_type' => WalletTransaction::class,
    ]);
}
```

2. Update WalletService to use JournalEntryService:

```php
// app/Services/WalletService.php
use App\Services\JournalEntryService;

public function __construct(JournalEntryService $journalEntryService)
{
    $this->journalEntryService = $journalEntryService;
}

public function addFunds($userId, $amount, $description)
{
    // Existing wallet fund addition logic
    
    // Create journal entry
    $this->journalEntryService->createWalletTransactionEntry($walletTransaction);
    
    return $walletTransaction;
}

public function deductFunds($userId, $amount, $description)
{
    // Existing wallet fund deduction logic
    
    // Create journal entry
    $this->journalEntryService->createWalletTransactionEntry($walletTransaction);
    
    return $walletTransaction;
}
```

3. Update payment processors to create journal entries:

```php
// app/Services/Payment/RazorpayService.php
use App\Services\JournalEntryService;

public function __construct(JournalEntryService $journalEntryService)
{
    $this->journalEntryService = $journalEntryService;
}

public function processPayment($request)
{
    // Existing payment processing logic
    
    // Create journal entry for payment
    $this->journalEntryService->createEntry([
        'user_id' => $user->id,
        'transaction_type' => 'payment',
        'amount' => $amount,
        'reference_id' => $payment->id,
        'reference_type' => Payment::class,
    ]);
    
    return $payment;
}
```

### 4. Implement Missing Notifications

**Files:**
- `app/Http/Controllers/WebhookController.php`
- `app/Console/Commands/ExpireGracePeriods.php`
- `app/Mail/PaymentFailedNotification.php`
- `app/Mail/SubscriptionSuspendedNotification.php`
- `app/Mail/GracePeriodExpiredNotification.php`

**Current Status:** Missing notification implementations

**Steps to Fix:**

1. Create payment failed notification:

```php
// app/Mail/PaymentFailedNotification.php
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PaymentFailedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $payment;
    public $retryCount;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($payment, $retryCount)
    {
        $this->payment = $payment;
        $this->retryCount = $retryCount;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Payment Failed')
                    ->view('emails.payment-failed');
    }
}
```

2. Create subscription suspended notification:

```php
// app/Mail/SubscriptionSuspendedNotification.php
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscriptionSuspendedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $subscription;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Subscription Suspended')
                    ->view('emails.subscription-suspended');
    }
}
```

3. Create grace period expired notification:

```php
// app/Mail/GracePeriodExpiredNotification.php
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GracePeriodExpiredNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $subscription;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Grace Period Expired')
                    ->view('emails.grace-period-expired');
    }
}
```

4. Update WebhookController to send notifications:

```php
// app/Http/Controllers/WebhookController.php
use App\Mail\PaymentFailedNotification;
use App\Mail\SubscriptionSuspendedNotification;
use Illuminate\Support\Facades\Mail;

// Send subscription suspended notification
Mail::to($user->email)->send(new SubscriptionSuspendedNotification($subscription));

// Send payment failed notification with retry count
Mail::to($user->email)->send(new PaymentFailedNotification($payment, $retryCount));
```

5. Update ExpireGracePeriods command to send notifications:

```php
// app/Console/Commands/ExpireGracePeriods.php
use App\Mail\GracePeriodExpiredNotification;
use Illuminate\Support\Facades\Mail;

// Send expiration notification to user
Mail::to($user->email)->send(new GracePeriodExpiredNotification($subscription));
```

---

## Priority: High (Fix Within 2 Weeks)

### 1. Complete Referral System

**Files:**
- `app/Http/Controllers/Subscriber/ReferralController.php`
- `resources/views/subscriber/referral/index.blade.php`
- `app/Services/ReferralService.php`

**Current Status:** Controller exists, but missing UI and bonus distribution

**Steps to Fix:**

1. Update ReferralService to calculate and distribute bonuses:

```php
// app/Services/ReferralService.php
public function calculateReferralBonus($investment)
{
    // Calculate referral bonus based on investment amount
    $bonusAmount = $investment->amount * config('referral.bonus_percentage');
    
    return $bonusAmount;
}

public function distributeReferralBonus($investment, $referrer)
{
    $bonusAmount = $this->calculateReferralBonus($investment);
    
    // Add bonus to referrer's wallet
    $walletService = new WalletService();
    $walletService->addFunds($referrer->id, $bonusAmount, 'Referral Bonus');
    
    // Create reward record
    Reward::create([
        'user_id' => $referrer->id,
        'type' => 'referral',
        'amount' => $bonusAmount,
        'reference_id' => $investment->id,
        'reference_type' => ProjectInvestment::class,
    ]);
}
```

2. Add referral configuration to config/app.php:
   ```php
   'referral' => [
       'bonus_percentage' => 0.05, // 5% referral bonus
   ],
   ```

3. Update ReferralController to display referral data:

```php
// app/Http/Controllers/Subscriber/ReferralController.php
public function index()
{
    $referrerCode = $this->referralService->getReferralCode(auth()->user());
    $referrals = $this->referralService->getUserReferrals(auth()->user());
    $totalEarnings = $this->referralService->getTotalReferralEarnings(auth()->user());
    
    return view('subscriber.referral.index', [
        'referrerCode' => $referrerCode,
        'referrals' => $referrals,
        'totalEarnings' => $totalEarnings,
    ]);
}
```

4. Create referral index view:
   - Display user's referral code
   - Show list of referred users
   - Display referral earnings
   - Add referral link sharing options

### 2. Add Queue Integration for SIP Processing

**Files:**
- `app/Console/Commands/ProcessSipPayments.php`
- `app/Jobs/ProcessSipPaymentJob.php`

**Current Status:** ProcessSipPayments command exists, but no queue integration

**Steps to Fix:**

1. Create ProcessSipPaymentJob:

```php
// app/Jobs/ProcessSipPaymentJob.php
<?php

namespace App\Jobs;

use App\Models\Sip;
use App\Models\SipPaymentSchedule;
use App\Services\Payment\RazorpayService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessSipPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $sipPaymentSchedule;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(SipPaymentSchedule $sipPaymentSchedule)
    {
        $this->sipPaymentSchedule = $sipPaymentSchedule;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Process SIP payment
        $razorpayService = new RazorpayService();
        $razorpayService->processSipPayment($this->sipPaymentSchedule);
    }
}
```

2. Update ProcessSipPayments command to use queues:

```php
// app/Console/Commands/ProcessSipPayments.php
use App\Jobs\ProcessSipPaymentJob;

public function handle()
{
    $duePayments = SipPaymentSchedule::where('payment_date', '<=', now())
                                    ->where('status', 'pending')
                                    ->get();

    foreach ($duePayments as $payment) {
        ProcessSipPaymentJob::dispatch($payment);
    }

    $this->info('SIP payments queued for processing');
}
```

3. Configure queue connection in .env:
   ```
   QUEUE_CONNECTION=redis
   REDIS_HOST=127.0.0.1
   REDIS_PASSWORD=null
   REDIS_PORT=6379
   ```

4. Start queue worker:
   ```bash
   php artisan queue:work
   ```

### 3. Integrate Chart.js for Analytics

**Files:**
- `resources/views/admin/analytics/dashboard.blade.php`
- `resources/views/admin/finance/dashboard.blade.php`
- `app/Http/Controllers/Admin/AnalyticsController.php`
- `app/Http/Controllers/Admin/FinanceController.php`

**Current Status:** Controllers exist, views need chart integration

**Steps to Fix:**

1. Update AnalyticsController to provide chart data:

```php
// app/Http/Controllers/Admin/AnalyticsController.php
public function dashboard()
{
    // Get monthly investment data for chart
    $monthlyInvestments = ProjectInvestment::selectRaw('MONTH(created_at) as month, SUM(amount) as total')
                                            ->groupBy('month')
                                            ->orderBy('month')
                                            ->get()
                                            ->toArray();

    // Get project performance data
    $projectPerformance = Project::withCount('projectInvestments')
                                ->withSum('projectInvestments', 'amount')
                                ->get()
                                ->toArray();

    return view('admin.analytics.dashboard', [
        'monthlyInvestments' => $monthlyInvestments,
        'projectPerformance' => $projectPerformance,
    ]);
}
```

2. Update FinanceController to provide chart data:

```php
// app/Http/Controllers/Admin/FinanceController.php
public function dashboard()
{
    // Get monthly revenue data
    $monthlyRevenue = Payment::selectRaw('MONTH(created_at) as month, SUM(amount) as total')
                            ->where('status', 'success')
                            ->groupBy('month')
                            ->orderBy('month')
                            ->get()
                            ->toArray();

    return view('admin.finance.dashboard', [
        'monthlyRevenue' => $monthlyRevenue,
    ]);
}
```

3. Update admin/analytics/dashboard.blade.php to include Chart.js:

```html
<!-- resources/views/admin/analytics/dashboard.blade.php -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Monthly Investment Chart -->
<canvas id="monthlyInvestmentChart"></canvas>

<script>
    const monthlyInvestmentChart = new Chart(document.getElementById('monthlyInvestmentChart'), {
        type: 'bar',
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            datasets: [{
                label: 'Total Investment',
                data: @json($monthlyInvestments),
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
```

4. Update admin/finance/dashboard.blade.php to include Chart.js:

```html
<!-- resources/views/admin/finance/dashboard.blade.php -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Monthly Revenue Chart -->
<canvas id="monthlyRevenueChart"></canvas>

<script>
    const monthlyRevenueChart = new Chart(document.getElementById('monthlyRevenueChart'), {
        type: 'line',
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            datasets: [{
                label: 'Total Revenue',
                data: @json($monthlyRevenue),
                borderColor: 'rgba(75, 192, 192, 1)',
                tension: 0.1,
                fill: true,
                backgroundColor: 'rgba(75, 192, 192, 0.2)'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
```

### 4. Write Basic Integration Tests

**Files:**
- `tests/Feature/AuthenticationTest.php`
- `tests/Feature/SubscriptionTest.php`
- `tests/Feature/PaymentTest.php`

**Current Status:** 0 test classes, 0% coverage

**Steps to Fix:**

1. Create authentication test:

```php
// tests/Feature/AuthenticationTest.php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen()
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }
}
```

2. Create subscription test:

```php
// tests/Feature/SubscriptionTest.php
<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SubscriptionPlan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_subscription_plans()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/subscriber/subscription');

        $response->assertStatus(200);
        $response->assertViewHas('subscriptionPlans');
    }
}
```

3. Create payment test:

```php
// tests/Feature/PaymentTest.php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_page_can_be_rendered()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/checkout');

        $response->assertStatus(200);
    }
}
```

4. Run tests:
   ```bash
   php artisan test
   ```

---

## Priority: Medium (Fix Within 1 Month)

### 1. Implement Webhook Retry Logic

**Files:**
- `app/Models/WebhookEvent.php`
- `app/Http/Controllers/WebhookController.php`
- `app/Console/Commands/RetryFailedWebhooks.php`

**Steps to Fix:**

1. Add retry tracking to WebhookEvent model:

```php
// app/Models/WebhookEvent.php
use Illuminate\Database\Eloquent\Model;

class WebhookEvent extends Model
{
    protected $fillable = [
        'event_type',
        'payload',
        'status',
        'retry_count',
        'last_retry_at',
    ];

    protected $casts = [
        'payload' => 'json',
        'last_retry_at' => 'datetime',
    ];

    public function incrementRetryCount()
    {
        $this->retry_count = $this->retry_count + 1;
        $this->last_retry_at = now();
        $this->save();
    }
}
```

2. Create RetryFailedWebhooks command:

```php
// app/Console/Commands/RetryFailedWebhooks.php
<?php

namespace App\Console\Commands;

use App\Models\WebhookEvent;
use App\Http\Controllers\WebhookController;
use Illuminate\Console\Command;

class RetryFailedWebhooks extends Command
{
    protected $signature = 'webhook:retry';
    protected $description = 'Retry failed webhook events';

    public function handle()
    {
        $failedEvents = WebhookEvent::where('status', 'failed')
                                    ->where('retry_count', '<', config('webhook.max_retries'))
                                    ->where('last_retry_at', '<', now()->subMinutes(config('webhook.retry_delay')))
                                    ->orWhereNull('last_retry_at')
                                    ->get();

        $controller = new WebhookController();

        foreach ($failedEvents as $event) {
            $this->info("Retrying webhook event #{$event->id} ({$event->event_type})");
            $controller->retryWebhook($event);
        }

        $this->info("Retried {$failedEvents->count()} failed webhook events");
    }
}
```

3. Add webhook configuration to config/app.php:
   ```php
   'webhook' => [
       'max_retries' => 5,
       'retry_delay' => 15, // minutes
   ],
   ```

4. Add retry method to WebhookController:

```php
// app/Http/Controllers/WebhookController.php
use App\Models\WebhookEvent;

public function retryWebhook(WebhookEvent $event)
{
    $event->incrementRetryCount();
    
    try {
        // Re-process the webhook event
        $this->handleWebhookEvent($event->event_type, $event->payload);
        
        $event->status = 'processed';
        $event->save();
        
        return true;
    } catch (\Exception $e) {
        $event->status = 'failed';
        $event->error_message = $e->getMessage();
        $event->save();
        
        return false;
    }
}
```

### 2. Configure Rate Limiting

**Files:**
- `app/Http/Kernel.php`
- `routes/web.php`

**Steps to Fix:**

1. Update Kernel.php to add rate limiting middleware:

```php
// app/Http/Kernel.php
protected $middlewareGroups = [
    'web' => [
        // ... existing middleware
        \Illuminate\Routing\Middleware\ThrottleRequests::class.':60,1', // 60 requests per minute
    ],
];
```

2. Add specific rate limits to routes:

```php
// routes/web.php
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Middleware\ThrottleRequests;

// Rate limit login attempts
Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware(['guest', ThrottleRequests::class.':5,1']); // 5 attempts per minute

// Rate limit API routes
Route::middleware(['auth', 'api'])
    ->group(function () {
        // API routes
    })
    ->middleware(ThrottleRequests::class.':100,1'); // 100 requests per minute
```

### 3. Implement KYC/AML Framework

**Files:**
- `app/Models/KycDocument.php`
- `app/Http/Controllers/Subscriber/KycController.php`
- `resources/views/subscriber/kyc/index.blade.php`

**Steps to Fix:**

1. Create KycDocument model:

```php
// app/Models/KycDocument.php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KycDocument extends Model
{
    protected $fillable = [
        'user_id',
        'document_type',
        'document_number',
        'document_file',
        'status',
        'verified_at',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
```

2. Create KycController:

```php
// app/Http/Controllers/Subscriber/KycController.php
<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use App\Models\KycDocument;
use Illuminate\Http\Request;

class KycController extends Controller
{
    public function index()
    {
        $kycDocuments = KycDocument::where('user_id', auth()->id())->get();
        
        return view('subscriber.kyc.index', [
            'kycDocuments' => $kycDocuments,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_type' => 'required|string|max:255',
            'document_number' => 'required|string|max:255',
            'document_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $documentPath = $request->file('document_file')->store('kyc-documents', 'public');

        KycDocument::create([
            'user_id' => auth()->id(),
            'document_type' => $validated['document_type'],
            'document_number' => $validated['document_number'],
            'document_file' => $documentPath,
            'status' => 'pending',
        ]);

        return redirect()->route('subscriber.kyc.index')->with('success', 'KYC document uploaded successfully');
    }
}
```

3. Create KYC index view:
   - Display list of uploaded KYC documents
   - Add form to upload new KYC documents
   - Show document verification status

### 4. Install fileinfo PHP Extension

**Current Status:** PHP Startup: Unable to load dynamic library 'fileinfo'

**Steps to Fix:**

1. Install fileinfo extension on Ubuntu/Debian:
   ```bash
   sudo apt-get update
   sudo apt-get install php8.2-fileinfo
   sudo service apache2 restart
   ```

2. Install fileinfo extension on CentOS/RHEL:
   ```bash
   sudo yum install php8.2-fileinfo
   sudo systemctl restart httpd
   ```

3. Verify installation:
   ```bash
   php -m | grep fileinfo
   ```

---

## Priority: Low (Fix Within 3 Months)

### 1. Advanced Analytics

**Files:**
- `app/Http/Controllers/Admin/AnalyticsController.php`
- `resources/views/admin/analytics/dashboard.blade.php`

**Steps to Fix:**

1. Add more chart types to AnalyticsController:

```php
// app/Http/Controllers/Admin/AnalyticsController.php
public function dashboard()
{
    // Existing monthly investment data
    
    // Get user growth data
    $userGrowth = User::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
                        ->groupBy('month')
                        ->orderBy('month')
                        ->get()
                        ->toArray();

    // Get average investment per user
    $avgInvestmentPerUser = ProjectInvestment::selectRaw('MONTH(created_at) as month, AVG(amount) as average')
                                            ->groupBy('month')
                                            ->orderBy('month')
                                            ->get()
                                            ->toArray();

    return view('admin.analytics.dashboard', [
        'monthlyInvestments' => $monthlyInvestments,
        'projectPerformance' => $projectPerformance,
        'userGrowth' => $userGrowth,
        'avgInvestmentPerUser' => $avgInvestmentPerUser,
    ]);
}
```

2. Add new charts to dashboard view:

```html
<!-- User Growth Chart -->
<canvas id="userGrowthChart"></canvas>

<!-- Average Investment per User Chart -->
<canvas id="avgInvestmentChart"></canvas>

<script>
    // User Growth Chart
    const userGrowthChart = new Chart(document.getElementById('userGrowthChart'), {
        type: 'line',
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            datasets: [{
                label: 'New Users',
                data: @json($userGrowth),
                borderColor: 'rgba(255, 99, 132, 1)',
                tension: 0.1,
                fill: true,
                backgroundColor: 'rgba(255, 99, 132, 0.2)'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Average Investment Chart
    const avgInvestmentChart = new Chart(document.getElementById('avgInvestmentChart'), {
        type: 'bar',
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
            datasets: [{
                label: 'Average Investment',
                data: @json($avgInvestmentPerUser),
                backgroundColor: 'rgba(153, 102, 255, 0.5)',
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>
```

### 2. Fraud Detection

**Files:**
- `app/Services/FraudDetectionService.php`
- `app/Http/Controllers/CheckoutController.php`

**Steps to Fix:**

1. Create FraudDetectionService:

```php
// app/Services/FraudDetectionService.php
<?php

namespace App\Services;

use App\Models\Payment;

class FraudDetectionService
{
    public function detectFraud(Payment $payment)
    {
        $riskScore = 0;

        // Check payment amount
        if ($payment->amount > config('fraud.high_amount_threshold')) {
            $riskScore += 30;
        }

        // Check multiple payments from same IP
        $ipCount = Payment::where('ip_address', $payment->ip_address)
                        ->where('created_at', '>=', now()->subHour())
                        ->count();

        if ($ipCount > config('fraud.max_payments_per_ip')) {
            $riskScore += 40;
        }

        // Check payment frequency for user
        $userPaymentCount = Payment::where('user_id', $payment->user_id)
                                ->where('created_at', '>=', now()->subHour())
                                ->count();

        if ($userPaymentCount > config('fraud.max_payments_per_user')) {
            $riskScore += 25;
        }

        return $riskScore;
    }

    public function isFraudulent(Payment $payment)
    {
        $riskScore = $this->detectFraud($payment);
        
        return $riskScore >= config('fraud.risk_threshold');
    }
}
```

2. Add fraud configuration to config/app.php:
   ```php
   'fraud' => [
       'high_amount_threshold' => 10000, // ₹10,000
       'max_payments_per_ip' => 5,
       'max_payments_per_user' => 3,
       'risk_threshold' => 70,
   ],
   ```

3. Update CheckoutController to use fraud detection:

```php
// app/Http/Controllers/CheckoutController.php
use App\Services\FraudDetectionService;

public function processPayment(Request $request)
{
    $payment = Payment::create([
        // Payment details
        'ip_address' => $request->ip(),
    ]);

    $fraudDetection = new FraudDetectionService();
    
    if ($fraudDetection->isFraudulent($payment)) {
        $payment->status = 'failed';
        $payment->error_message = 'Fraudulent payment detected';
        $payment->save();
        
        return redirect()->back()->with('error', 'Payment failed');
    }

    // Continue with payment processing
}
```

---

## Conclusion

By following this guide, you will:
1. Fix all critical issues within 1 week
2. Complete high priority tasks within 2 weeks
3. Address medium priority tasks within 1 month
4. Implement low priority features within 3 months

Once all these fixes are implemented, the platform will be production-ready with improved security, functionality, and performance.

---

## Last Updated

February 10, 2026
