<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use App\Models\ProjectInvestment;
use App\Models\UserSubscription;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoAllocationService
{
    /**
     * Allocate funds for a user to eligible projects.
     * 
     * @param User $user
     * @param float|null $amount If null, allocate all remaining available balance.
     * @param int|null $adminId ID of the admin performing this action.
     * @return array List of created investments.
     */
    public function allocateForUser(User $user, ?float $amount = null, ?int $adminId = null)
    {
        return DB::transaction(function () use ($user, $amount, $adminId) {
            // Get active subscription
            $subscription = $user->subscriptions()->latest()->first();

            if (!$subscription || !$subscription->isActive()) {
                // If checking history, maybe look for active ones. 
                // But for allocation, we need active sub.
                // Or maybe 'grace' period is fine? 
                // Let's assume active for now.
                if (!$subscription) {
                    throw new \Exception("User does not have a subscription.");
                }
                if ($subscription->status !== 'active') {
                    throw new \Exception("User subscription is not active (Status: {$subscription->status}).");
                }
            }

            // Calculate active available balance
            $availableBalance = $subscription->amount - $subscription->allocated_amount;
            
            if ($availableBalance <= 0) {
                 return []; // No funds to allocate
            }

            $amountToAllocate = $amount ?? $availableBalance;

            if ($amountToAllocate > $availableBalance) {
                throw new \Exception("Insufficient balance. Available: {$availableBalance}, Requested: {$amountToAllocate}");
            }
            
            // Find eligible projects
            // Logic: Active, Visible (or internal?), Eligible for Auto
            $projects = Project::where('status', 'active')
                ->whereIn('allocation_eligibility', ['auto_only', 'both'])
                ->where('visibility_status', 'visible') 
                ->get();
                
            if ($projects->isEmpty()) {
                throw new \Exception("No eligible projects found for auto-allocation.");
            }

            // Distribute equally
            $projectCount = $projects->count();
            $amountPerProject = floor(($amountToAllocate / $projectCount) * 100) / 100;

            if ($amountPerProject <= 0) {
                 throw new \Exception("Amount per project is too small to allocate.");
            }

            $createdInvestments = [];

            foreach ($projects as $project) {
                // Check if project is fully funded?
                // Optional: If fully funded, skip?
                // For now, assume we can overshoot or manual check.
                // Let's check fund goal.
                if ($project->fund_goal > 0 && $project->current_fund >= $project->fund_goal) {
                    continue; // Skip fully funded projects
                }

                // Create Investment
                $investment = ProjectInvestment::create([
                    'user_id' => $user->id,
                    'project_id' => $project->id,
                    'subscription_id' => $subscription->id,
                    'amount' => $amountPerProject,
                    'status' => 'allocated', // Default
                    'allocation_type' => 'auto',
                    'admin_id' => $adminId,
                    'allocated_at' => now(),
                ]);

                // Update Project Fund
                $project->increment('current_fund', $amountPerProject);

                // Update Subscription Allocated Amount
                $subscription->increment('allocated_amount', $amountPerProject);
                
                $createdInvestments[] = $investment;
                
                // Log?
                Log::info("Auto-allocated {$amountPerProject} to Project {$project->id} for User {$user->id}");
                
                try {
                    \App\Models\ActivityLog::create([
                        'user_id' => $user->id,
                        'action' => 'auto_allocation',
                        'description' => "Auto-allocated ₹{$amountPerProject} to project {$project->title}",
                        'entity_type' => ProjectInvestment::class,
                        'entity_id' => $investment->id,
                        'admin_id' => $adminId,
                    ]);
                } catch (\Exception $e) {}
            }
            
            // Handle remainder? 
            // $remainder = $amountToAllocate - ($amountPerProject * count($createdInvestments));
            // Maybe add to the first project or keep in balance. 
            // Keeping in balance is safer.

            return $createdInvestments;
        });
    }
}
