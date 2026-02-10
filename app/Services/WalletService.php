<?php

namespace App\Services;

use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class WalletService
{
    protected $journalEntryService;

    public function __construct(JournalEntryService $journalEntryService)
    {
        $this->journalEntryService = $journalEntryService;
    }
    /**
     * Get or create user wallet.
     */
    public function getWallet(User $user): Wallet
    {
        return Wallet::firstOrCreate(['user_id' => $user->id]);
    }

    /**
     * Credit the user's wallet.
     */
    public function credit(User $user, float $amount, string $transactionType, string $description, ?Model $reference = null): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $transactionType, $description, $reference) {
            $wallet = $this->getWallet($user);
            
            // Lock wallet for update
            $wallet = Wallet::where('id', $wallet->id)->lockForUpdate()->first();

            $wallet->balance += $amount;
            $wallet->save();

            $transaction = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'credit',
                'amount' => $amount,
                'description' => $description,
                'transaction_type' => $transactionType,
                'reference_id' => $reference?->id,
                'reference_type' => $reference ? get_class($reference) : null,
                'running_balance' => $wallet->balance,
            ]);

            // Ledger Entry: Debit Asset (e.g., Bank/Gateway), Credit Liability (User Wallet)
            // Note: This logic assumes 'credit' to wallet comes from external source or profit.
            // You might need dynamic codes based on transaction type.
            // For now, mapping general 'deposit' -> Debit: 1002 (Gateway), Credit: 2001 (User Liability)
            
            $debitCode = '1002'; // Default: Gateway Receivables
            $creditCode = '2001'; // Default: User Wallet Liability

            if ($transactionType === 'profit_distribution') {
                 $debitCode = '4002'; // Reduce Revenue? Or Expense? Actually Profit Distribution is Expense (5xxx) or Equity reduction? 
                 // Let's assume Expense for now or handled elsewhere.
                 // For simplicity in this phase, we map Deposit to Gateway.
            }

            $this->journalEntryService->record("Wallet Credit: $description", [
                ['code' => $debitCode, 'debit' => $amount, 'credit' => 0],
                ['code' => $creditCode, 'debit' => 0, 'credit' => $amount],
            ], $transaction->id);

            return $transaction;
        });
    }

    /**
     * Debit the user's wallet.
     */
    public function debit(User $user, float $amount, string $transactionType, string $description, ?Model $reference = null): WalletTransaction
    {
        return DB::transaction(function () use ($user, $amount, $transactionType, $description, $reference) {
            $wallet = $this->getWallet($user);

            // Lock wallet for update
            $wallet = Wallet::where('id', $wallet->id)->lockForUpdate()->first();

            if ($wallet->balance < $amount) {
                throw new \Exception("Insufficient wallet balance. Available: {$wallet->balance}, Required: {$amount}");
            }

            $wallet->balance -= $amount;
            $wallet->save();

            $transaction = WalletTransaction::create([
                'wallet_id' => $wallet->id,
                'type' => 'debit',
                'amount' => $amount,
                'description' => $description,
                'transaction_type' => $transactionType,
                'reference_id' => $reference?->id,
                'reference_type' => $reference ? get_class($reference) : null,
                'running_balance' => $wallet->balance,
            ]);

            // Ledger Entry: Debit Liability (User Wallet), Credit Asset/Revenue
            // Debit: 2001 (User Liability) - Reducing liability
            // Credit: 1001 (Cash - if withdrawal) OR 4xxx (Revenue - if investment/fee)

            $debitCode = '2001'; 
            $creditCode = '1001'; // Default: Cash (Withdrawal)

            if ($transactionType === 'investment') {
                $creditCode = '4002'; // Project Revenue / Funds
                // Actually, Investment moves money from User Liability to Project Fund (Liability/Equity).
                // For this phase, let's map to Project Success Fees or similar for now, 
                // or just keep it simple. Real accounting needs exact codes.
            }

            $this->journalEntryService->record("Wallet Debit: $description", [
                ['code' => $debitCode, 'debit' => $amount, 'credit' => 0],
                ['code' => $creditCode, 'debit' => 0, 'credit' => $amount],
            ], $transaction->id);

            return $transaction;
        });
    }

    /**
     * Get wallet balance.
     */
    public function getBalance(User $user): float
    {
        return (float) ($this->getWallet($user)->balance ?? 0.0);
    }
}
