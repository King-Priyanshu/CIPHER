<?php

namespace App\Services;

use App\Models\ProfitDistribution;
use App\Models\Project;
use App\Models\UserProfitLog;
use App\Models\ProjectInvestment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoyaltyService
{
    /**
     * Distribute profit for a project.
     *
     * @param ProfitDistribution $distribution
     * @return int Number of users distributed to.
     */
    public function distribute(ProfitDistribution $distribution)
    {
        return DB::transaction(function () use ($distribution) {
            $project = $distribution->project;
            $month = $distribution->month; // Distribution month

            // Get all active investments for this project up to the distribution month?
            // Or just currently active? 
            // Usually profit is for a specific month, so investments active DURING that month.
            // For simplicity, let's use currently active investments, or investments created before end of that month.
            // Let's assume investments created before the distribution declaration.
            
            $investments = ProjectInvestment::where('project_id', $project->id)
                ->whereIn('status', ['active', 'allocated']) // allocated counts as active for profit?
                ->where('created_at', '<=', $distribution->created_at) // Snapshot at distribution time
                ->get();

            $totalInvestment = $investments->sum('amount');

            if ($totalInvestment <= 0) {
                throw new \Exception("No active investments found for this project.");
            }

            $count = 0;
            $totalDistributed = 0;

            foreach ($investments as $investment) {
                // Calculate share
                // Share = (User Amount / Total Amount) * Total Profit
                
                // Check if already distributed? 
                // We should prevent double distribution for same investment/distribution ID.
                $exists = UserProfitLog::where('profit_distribution_id', $distribution->id)
                    ->where('project_investment_id', $investment->id)
                    ->exists();

                if ($exists) {
                    continue;
                }

                $share = ($investment->amount / $totalInvestment) * $distribution->total_profit;
                $share = floor($share * 100) / 100; // Round down to 2 decimals

                if ($share > 0) {
                    UserProfitLog::create([
                        'user_id' => $investment->user_id,
                        'profit_distribution_id' => $distribution->id,
                        'project_investment_id' => $investment->id,
                        'amount' => $share,
                        'status' => 'pending', // Pending maturity (11 months)
                        'credited_at' => null,
                    ]);

                    $totalDistributed += $share;
                    $count++;
                }
            }

            // Update Distribution Record
            $distribution->update([
                'distributed_amount' => $totalDistributed,
                'status' => 'completed', // or 'distributing' if async?
                'distributed_at' => now(),
            ]);

            try {
                \App\Models\ActivityLog::create([
                    'user_id' => $distribution->declared_by ?? auth()->id(),
                    'action' => 'distribution_completed',
                    'description' => "Distributed ₹{$totalDistributed} profit for project {$project->title}",
                    'entity_type' => ProfitDistribution::class,
                    'entity_id' => $distribution->id,
                    'admin_id' => $distribution->declared_by,
                ]);
            } catch (\Exception $e) {}

            return $count;
        });
    }
}
