<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RewardPool;
use App\Models\Project;
use App\Services\Rewards\RewardCalculationService;
use App\Services\Rewards\RewardDistributionService;
use Illuminate\Http\Request;

class RewardPoolController extends Controller
{
    protected $calculationService;
    protected $distributionService;

    public function __construct(
        RewardCalculationService $calculationService,
        RewardDistributionService $distributionService
    ) {
        $this->calculationService = $calculationService;
        $this->distributionService = $distributionService;
    }

    public function index()
    {
        $pools = RewardPool::with('project')->latest()->paginate(10);
        return view('admin.reward-pools.index', compact('pools'));
    }

    public function create()
    {
        $projects = Project::where('status', 'active')->get();
        return view('admin.reward-pools.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'total_amount' => 'required|numeric|min:0',
            'distribution_date' => 'nullable|date',
        ]);

        $pool = RewardPool::create($validated);

        // Auto-calculate rewards upon creation
        $this->calculationService->calculateForPool($pool);

        return redirect()->route('admin.reward-pools.index')->with('success', 'Reward Pool created and calculated.');
    }

    public function edit(RewardPool $reward_pool)
    {
        $projects = Project::where('status', 'active')->get();
        return view('admin.reward-pools.edit', compact('reward_pool', 'projects'));
    }

    public function update(Request $request, RewardPool $reward_pool)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'total_amount' => 'required|numeric|min:0',
            'distribution_date' => 'nullable|date',
        ]);

        $reward_pool->update($validated);

        return redirect()->route('admin.reward-pools.index')->with('success', 'Reward Pool updated.');
    }

    public function destroy(RewardPool $reward_pool)
    {
        $reward_pool->delete();

        return redirect()->route('admin.reward-pools.index')->with('success', 'Reward Pool deleted.');
    }

    public function distribute(RewardPool $reward_pool)
    {
        $this->distributionService->distribute($reward_pool);
        return redirect()->back()->with('success', 'Rewards distributed successfully.');
    }
}
