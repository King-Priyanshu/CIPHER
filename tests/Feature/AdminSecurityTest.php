<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_dashboard()
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_regular_subscriber_cannot_access_admin_dashboard()
    {
        $subscriberRole = Role::create(['name' => 'Subscriber', 'slug' => 'subscriber']);
        $subscriber = User::factory()->create(['role_id' => $subscriberRole->id]);

        $response = $this->actingAs($subscriber)->get('/admin/dashboard');
        
        // Assuming Middleware 'admin' is working, this should return 403
        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_dashboard()
    {
        $adminRole = Role::create(['name' => 'Admin', 'slug' => 'admin']);
        $admin = User::factory()->create(['role_id' => $adminRole->id]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        
        $response->assertStatus(200);
    }
}
