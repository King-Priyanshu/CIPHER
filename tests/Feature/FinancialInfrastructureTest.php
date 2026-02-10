<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\LedgerAccount;
use App\Models\JournalEntry;
use App\Models\LedgerEntry;
use App\Models\WebhookEvent;
use App\Models\Payment;
use App\Services\JournalEntryService;
use App\Services\WalletService;
use App\Jobs\ProcessWebhook;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Event;
use App\Events\PaymentSucceeded;

class FinancialInfrastructureTest extends TestCase
{
    use RefreshDatabase;

    protected $journalEntryService;
    protected $walletService;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed Ledger Accounts
        $this->seed(\Database\Seeders\RoleSeeder::class);
        $this->seed(\Database\Seeders\LedgerAccountSeeder::class);

        $this->journalEntryService = new JournalEntryService();
        $this->walletService = new WalletService($this->journalEntryService);
    }

    /** @test */
    public function it_records_valid_journal_entry()
    {
        $entries = [
            ['code' => '1001', 'debit' => 100, 'credit' => 0],
            ['code' => '2001', 'debit' => 0, 'credit' => 100],
        ];

        $journalEntry = $this->journalEntryService->record('Test Transaction', $entries);

        $this->assertDatabaseHas('journal_entries', ['description' => 'Test Transaction']);
        $this->assertDatabaseHas('ledger_entries', ['debit' => 100, 'ledger_account_id' => LedgerAccount::where('code', '1001')->first()->id]);
        $this->assertDatabaseHas('ledger_entries', ['credit' => 100, 'ledger_account_id' => LedgerAccount::where('code', '2001')->first()->id]);
    }

    /** @test */
    public function it_throws_exception_for_unbalanced_entry()
    {
        $this->expectException(\Exception::class);

        $entries = [
            ['code' => '1001', 'debit' => 100, 'credit' => 0],
            ['code' => '2001', 'debit' => 0, 'credit' => 90], // Unbalanced
        ];

        $this->journalEntryService->record('Unbalanced Transaction', $entries);
    }

    /** @test */
    public function wallet_credit_creates_ledger_entry()
    {
        $user = User::factory()->create();
        $initialBalance = $this->walletService->getBalance($user);

        $this->walletService->credit($user, 500, 'deposit', 'Test Deposit');

        $this->assertEquals($initialBalance + 500, $this->walletService->getBalance($user));

        // Check Ledger
        $this->assertDatabaseHas('journal_entries', ['description' => 'Wallet Credit: Test Deposit']);
        // 1002 (Gateway) Debit 500
        $this->assertDatabaseHas('ledger_entries', [
            'ledger_account_id' => LedgerAccount::where('code', '1002')->first()->id,
            'debit' => 500
        ]);
        // 2001 (User Liability) Credit 500
        $this->assertDatabaseHas('ledger_entries', [
            'ledger_account_id' => LedgerAccount::where('code', '2001')->first()->id,
            'credit' => 500
        ]);
    }

    /** @test */
    public function webhook_job_processes_payment_captured()
    {
        Queue::fake();
        Event::fake();

        $user = User::factory()->create();

        // Ensure user has subscription (mocked) to link
        $plan = \App\Models\SubscriptionPlan::factory()->create(['price' => 1000, 'interval' => 'monthly']);
        $sub = \App\Models\UserSubscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'razorpay_order_id' => 'order_12345',
            'status' => 'pending',
            'amount' => 1000,
        ]);

        $eventId = 'evt_test_123';
        $payload = [
            'event' => 'payment.captured',
            'account_id' => 'acc_123',
            'payload' => [
                'payment' => [
                    'entity' => [
                        'id' => 'pay_12345',
                        'amount' => 100000, // 1000 INR
                        'currency' => 'INR',
                        'status' => 'captured',
                        'order_id' => 'order_12345',
                        'notes' => ['user_id' => $user->id]
                    ]
                ]
            ]
        ];

        $webhookEvent = WebhookEvent::create([
            'event_id' => $eventId,
            'gateway' => 'razorpay',
            'event_type' => 'payment.captured',
            'payload' => $payload,
            'status' => 'pending'
        ]);

        $job = new ProcessWebhook($eventId);
        $job->handle(new \App\Services\Payment\RazorpayService(), new \App\Services\Payment\StripeService(), $this->walletService);

        // Assert Subscription Active
        $this->assertDatabaseHas('user_subscriptions', [
            'id' => $sub->id,
            'status' => 'active',
        ]);

        // Assert Payment Recorded
        $this->assertDatabaseHas('payments', [
            'gateway_transaction_id' => 'pay_12345',
            'amount' => 1000
        ]);

        // Assert Wallet Credited
        $this->assertEquals(1000, $this->walletService->getBalance($user));

        // Assert Webhook Marked Processed
        $this->assertDatabaseHas('webhook_events', [
            'event_id' => $eventId,
            'status' => 'processed'
        ]);
    }
}
