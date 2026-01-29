<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get subscriber role
        $subscriberRole = Role::where('slug', 'subscriber')->first();

        // Create 20 dummy users
        User::factory()->count(20)->create([
            'role_id' => $subscriberRole ? $subscriberRole->id : null, 
        ]);
        
        // Also create a few specific users if needed
    }
}
