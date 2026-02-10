<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LedgerAccountSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            // Assets (1000-1999)
            ['name' => 'Cash/Bank', 'code' => '1001', 'type' => 'asset', 'is_system' => true],
            ['name' => 'Payment Gateway Receivables', 'code' => '1002', 'type' => 'asset', 'is_system' => true],
            
            // Liabilities (2000-2999)
            ['name' => 'User Wallet Liability', 'code' => '2001', 'type' => 'liability', 'is_system' => true], // Funds held on behalf of users
            ['name' => 'Pending Refunds', 'code' => '2002', 'type' => 'liability', 'is_system' => true],

            // Equity (3000-3999)
            ['name' => 'Retained Earnings', 'code' => '3001', 'type' => 'equity', 'is_system' => true],

            // Revenue (4000-4999)
            ['name' => 'Platform Fees', 'code' => '4001', 'type' => 'revenue', 'is_system' => true],
            ['name' => 'Project Success Fees', 'code' => '4002', 'type' => 'revenue', 'is_system' => true],

            // Expenses (5000-5999)
            ['name' => 'Gateway Charges', 'code' => '5001', 'type' => 'expense', 'is_system' => true],
        ];

        foreach ($accounts as $account) {
            DB::table('ledger_accounts')->updateOrInsert(
                ['code' => $account['code']],
                array_merge($account, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
