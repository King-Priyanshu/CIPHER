<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('slug', 'admin')->first();

        if ($adminRole) {
            User::firstOrCreate(
                ['email' => 'admin@cipher.com'],
                [
                    'name' => 'Super Admin',
                    'password' => Hash::make('password'),
                    'role_id' => $adminRole->id,
                    'email_verified_at' => Carbon::now(),
                    'terms_accepted_at' => Carbon::now(),
                ]
            );
        }
    }
}
