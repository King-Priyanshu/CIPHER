<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ActivityLog;
use App\Services\InvestmentAllocationService; // Using the service for consistency
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvestmentController extends Controller
{
    /**
     * Display a listing of user investments.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Use eager loading
        $investments = $user->investments()
            ->with('project')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
            
        $totalInvested = $user->investments()->active()->sum('amount');
        
        $activeProjects = $user->investments()
            ->whereIn('status', ['allocated', 'active'])
            ->distinct('project_id')
            ->count('project_id');
            
        $totalProfits = \App\Models\UserProfitLog::where('user_id', $user->id)
            ->where('status', 'credited')
            ->sum('amount');
        
        return view('subscriber.investments.index', compact('investments', 'totalInvested', 'activeProjects', 'totalProfits'));
    }

    /**
     * Store a newly created investment (Manual Allocation).
     * Only available for users with participation_mode = 'manual'.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Check if user is in manual mode
        if ($user->participation_mode !== 'manual') {
            return redirect()->back()->with('error', 'Your account is set to Auto mode. The admin will allocate funds on your behalf.');
        }

        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'amount' => 'required|numeric|min:1',
        ]);
        
        $subscription = $user->subscription;
        
        if (!$subscription || !$subscription->isActive()) {
            return redirect()->back()->with('error', 'No active subscription found.');
        }
        
        $available = $subscription->amount - $subscription->allocated_amount;
        
        if ($request->amount > $available) {
             return redirect()->back()->with('error', 'Insufficient funds. Available: ₹' . number_format($available, 0));
        }

        $project = Project::findOrFail($request->project_id);
        
        if ($project->status !== 'active') {
            return redirect()->back()->with('error', 'Project is not active.');
        }
        
        if ($project->allocation_eligibility === 'auto_only') {
            return redirect()->back()->with('error', 'This project does not accept manual allocations.');
        }

        // Create Investment
        \App\Models\ProjectInvestment::create([
            'user_id' => $user->id,
            'project_id' => $project->id,
            'subscription_id' => $subscription->id,
            'amount' => $request->amount,
            'status' => 'allocated',
            'allocation_type' => 'manual',
            'allocated_at' => now(),
        ]);
        
        // Update subscription and project
        $subscription->increment('allocated_amount', $request->amount);
        $project->increment('current_fund', $request->amount);

        // Log activity
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'manual_allocation',
            'description' => "Allocated ₹{$request->amount} to project {$project->title}",
            'entity_type' => \App\Models\ProjectInvestment::class,
        ]);

        return redirect()->back()->with('success', 'Investment allocated successfully!');
    }

    /**
     * Withdraw an investment.
     */
    /**
     * Withdraw an investment.
     * @deprecated Disabled in favor of Admin-only management.
     */
    public function withdraw(Request $request)
    {
        abort(403, 'Withdrawal is not allowed directly. Please contact support.');
    } // End withdraw method
}
