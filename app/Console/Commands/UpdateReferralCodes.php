<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Str;

class UpdateReferralCodes extends Command
{
    protected $signature = 'users:update-referral-codes';
    protected $description = 'Generate missing referral codes for existing users';

    public function handle()
    {
        $users = User::whereNull('referral_code')->get();
        $count = 0;

        foreach ($users as $user) {
            $user->update([
                'referral_code' => strtoupper(Str::random(8))
            ]);
            $count++;
        }

        $this->info("Updated {$count} users with referral codes.");
    }
}
