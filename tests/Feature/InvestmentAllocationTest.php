<?php

namespace Tests\Feature;

use App\Models\InvestmentPlan;
use App\Models\Project;
use App\Models\ProjectInvestment;
use App\Models\User;
use App\Models\Role;
use App\Models\Wallet;
use App\Services\InvestmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvestmentAllocationTest extends TestCase
{
    use RefreshDatabase;

    protected InvestmentService $investmentService;
    protected Role $adminRole;
    protected Role $subscriberRole;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\LedgerAccountSeeder::class);
        $this->investmentService = app(InvestmentService::class);

        $this->adminRole = Role::create(['name' => 'Admin', 'slug' => 'admin']);
        $this->subscriberRole = Role::create(['name' => 'Subscriber', 'slug' => 'subscriber']);
    }

    protected function createVerifiedUser($role = 'subscriber')
    {
        $user = User::factory()->create([
            'role_id' => $role === 'admin' ? $this->adminRole->id : $this->subscriberRole->id
        ]);
        Wallet::create(['user_id' => $user->id, 'balance' => 50000]);
        return $user;
    }

    public function test_user_can_create_pending_manual_investment()
    {
        $user = $this->createVerifiedUser();
        $project = Project::factory()->create(['status' => 'active', 'visibility_status' => 'visible', 'allocation_eligibility' => 'both']);
        $plan = InvestmentPlan::create([
            'project_id' => $project->id,
            'name' => 'Basic Plan',
            'slug' => 'basic-plan',
            'expected_return_percentage' => 10,
            'duration_months' => 12,
            'min_investment' => 1000,
        ]);

        $this->actingAs($user);

        $response = $this->post(route('subscriber.projects.invest', $project), [
            'amount' => 5000,
            'plan_id' => $plan->id,
            'payment_method' => 'gateway',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('project_investments', [
            'user_id' => $user->id,
            'project_id' => $project->id,
            'amount' => 5000,
            'status' => ProjectInvestment::STATUS_PENDING_PAYMENT,
            'allocation_type' => 'manual',
        ]);
    }

    public function test_user_can_create_pending_auto_investment()
    {
        $user = $this->createVerifiedUser();
        $plan = InvestmentPlan::create([
            'project_id' => null,
            'name' => 'Auto Plan',
            'slug' => 'auto-plan',
            'expected_return_percentage' => 15,
            'duration_months' => 12,
            'min_investment' => 1000,
        ]);

        $this->actingAs($user);

        $response = $this->post(route('subscriber.projects.auto_invest'), [
            'amount' => 10000,
            'plan_id' => $plan->id,
            'payment_method' => 'gateway',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('project_investments', [
            'user_id' => $user->id,
            'project_id' => null,
            'amount' => 10000,
            'status' => ProjectInvestment::STATUS_PENDING_PAYMENT,
            'allocation_type' => 'auto',
        ]);
    }

    public function test_finalizing_manual_investment_updates_project_funding()
    {
        $user = $this->createVerifiedUser();
        $project = Project::factory()->create(['current_fund' => 0]);
        $plan = InvestmentPlan::create([
            'project_id' => $project->id,
            'name' => 'Direct Plan',
            'slug' => 'direct-plan',
            'expected_return_percentage' => 12,
            'duration_months' => 12,
            'min_investment' => 1000,
        ]);

        $investment = ProjectInvestment::create([
            'user_id' => $user->id,
            'project_id' => $project->id,
            'investment_plan_id' => $plan->id,
            'amount' => 5000,
            'status' => ProjectInvestment::STATUS_PENDING_PAYMENT,
            'allocation_type' => 'manual',
        ]);

        $this->investmentService->finalizeInvestment($investment);

        $this->assertEquals(ProjectInvestment::STATUS_ACTIVE, $investment->fresh()->status);
        $this->assertEquals(5000, (float) $project->fresh()->current_fund);
    }

    public function test_admin_can_allocate_pending_auto_investment()
    {
        $admin = $this->createVerifiedUser('admin');
        $user = $this->createVerifiedUser('subscriber');
        $project = Project::factory()->create(['status' => 'active', 'current_fund' => 0]);
        $plan = InvestmentPlan::create([
            'project_id' => null,
            'name' => 'Diversified Plan',
            'slug' => 'diversified-plan',
            'expected_return_percentage' => 14,
            'duration_months' => 12,
            'min_investment' => 1000,
        ]);

        $investment = ProjectInvestment::create([
            'user_id' => $user->id,
            'project_id' => null,
            'investment_plan_id' => $plan->id,
            'amount' => 10000,
            'status' => ProjectInvestment::STATUS_PENDING_ADMIN_ALLOCATION,
            'allocation_type' => 'auto',
        ]);

        $this->actingAs($admin);

        $response = $this->post(route('admin.investments.allocate-pending', $investment), [
            'project_id' => $project->id,
        ]);

        $response->assertRedirect();
        $this->assertEquals(ProjectInvestment::STATUS_ACTIVE, $investment->fresh()->status);
        $this->assertEquals($project->id, $investment->fresh()->project_id);
        $this->assertEquals(10000, (float) $project->fresh()->current_fund);
    }
}
