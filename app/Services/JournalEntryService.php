<?php

namespace App\Services;

use App\Models\JournalEntry;
use App\Models\LedgerAccount;
use App\Models\LedgerEntry;
use Illuminate\Support\Facades\DB;
use Exception;

class JournalEntryService
{
    /**
     * Record a double-entry transaction.
     * 
     * @param string $description
     * @param array $entries Array of ['code' => '1001', 'debit' => 100, 'credit' => 0]
     * @param string|null $referenceId
     * @param string|null $date
     * @return JournalEntry
     * @throws Exception
     */
    public function record(string $description, array $entries, ?string $referenceId = null, ?string $date = null): JournalEntry
    {
        return DB::transaction(function () use ($description, $entries, $referenceId, $date) {
            
            // 1. Verify Balance (Total Debit must equal Total Credit)
            $totalDebit = 0;
            $totalCredit = 0;

            foreach ($entries as $entry) {
                $totalDebit += $entry['debit'] ?? 0;
                $totalCredit += $entry['credit'] ?? 0;
            }

            // Floating point comparison with epsilon
            if (abs($totalDebit - $totalCredit) > 0.01) {
                throw new Exception("Journal Entry is not balanced. Debit: $totalDebit, Credit: $totalCredit");
            }

            // 2. Create Journal Entry
            $journalEntry = JournalEntry::create([
                'description' => $description,
                'reference_id' => $referenceId,
                'date' => $date ?? now(),
            ]);

            // 3. Create Ledger Entries
            foreach ($entries as $entry) {
                $account = LedgerAccount::where('code', $entry['code'])->first();
                
                if (!$account) {
                    throw new Exception("Ledger Account with code {$entry['code']} not found.");
                }

                LedgerEntry::create([
                    'journal_entry_id' => $journalEntry->id,
                    'ledger_account_id' => $account->id,
                    'debit' => $entry['debit'] ?? 0,
                    'credit' => $entry['credit'] ?? 0,
                ]);
            }

            return $journalEntry;
        });
    }
}
