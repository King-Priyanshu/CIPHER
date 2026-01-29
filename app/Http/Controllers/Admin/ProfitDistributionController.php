<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfitDistribution;
use App\Models\Project;
use App\Services\ProfitDistributionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfitDistributionController extends Controller
{
    protected ProfitDistributionService $profitService;

    public function __construct(ProfitDistributionService $profitService)
    {
        $this->profitService = $profitService;
    }

    /**
     * Display all profit distributions.
     */
    public function index(Request $request)
    {
        $query = ProfitDistribution::with(['project', 'declaredBy'])
            ->orderBy('declared_at', 'desc');

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $distributions = $query->paginate(20);
        $projects = Project::orderBy('title')->get();
        $stats = $this->profitService->getPlatformStats();

        return view('admin.profits.index', compact('distributions', 'projects', 'stats'));
    }

    /**
     * Show form to declare new profit.
     */
    public function create()
    {
        $projects = Project::where('status', 'active')
            ->orWhere('status', 'completed')
            ->orderBy('title')
            ->get();

        return view('admin.profits.create', compact('projects'));
    }

    /**
     * Declare a new profit distribution.
     */
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:1000',
        ]);

        $project = Project::findOrFail($request->project_id);

        $distribution = $this->profitService->declareProfit(
            $project,
            $request->amount,
            Auth::user(),
            $request->notes
        );

        return redirect()->route('admin.profits.show', $distribution)
            ->with('success', "Profit of ₹{$request->amount} declared for {$project->title}");
    }

    /**
     * Show distribution details.
     */
    public function show(ProfitDistribution $profit)
    {
        $profit->load(['project', 'declaredBy', 'profitLogs.user']);

        return view('admin.profits.show', compact('profit'));
    }

    /**
     * Distribute profit to all investors.
     */
    public function distribute(ProfitDistribution $profit)
    {
        try {
            $profitLogs = $this->profitService->distributeProfit($profit);

            return redirect()->route('admin.profits.show', $profit)
                ->with('success', "Profit distributed to " . count($profitLogs) . " investors");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Distribution failed: ' . $e->getMessage());
        }
    }
}
