<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Wallet;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subscriberRole = Role::where('slug', 'subscriber')->first();
        
        if ($subscriberRole) {
            $user = User::updateOrCreate(
                ['email' => 'user@cipher.com'],
                [
                    'name' => 'John Doe',
                    'password' => Hash::make('password'),
                    'role_id' => $subscriberRole->id,
                    'email_verified_at' => Carbon::now(),
                    'terms_accepted_at' => Carbon::now(),
                ]
            );

            // Initialize Wallet with dummy balance for testing
            Wallet::firstOrCreate(
                ['user_id' => $user->id],
                ['balance' => 50000.00]
            );
            
            // Generate some random users
            // User::factory(10)->create(['role_id' => $subscriberRole->id]);
        }
    }
}
