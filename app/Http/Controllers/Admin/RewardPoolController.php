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
        return view('admin.rewards.index', compact('pools'));
    }

    public function create()
    {
        $projects = Project::where('status', 'active')->get();
        return view('admin.rewards.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'total_amount' => 'required|numeric|min:0',
        ]);

        $pool = RewardPool::create($validated);

        // Auto-calculate rewards upon creation (simplification)
        $this->calculationService->calculateForPool($pool);

        return redirect()->route('admin.rewards.index')->with('success', 'Reward Pool created and calculated.');
    }

    public function distribute(RewardPool $pool)
    {
        $this->distributionService->distribute($pool);
        return redirect()->back()->with('success', 'Rewards distributed successfully.');
    }
}
