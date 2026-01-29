<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectInvestment;
use App\Models\User;
use App\Services\InvestmentAllocationService;
use Illuminate\Http\Request;

class InvestmentController extends Controller
{
    protected InvestmentAllocationService $investmentService;

    public function __construct(InvestmentAllocationService $investmentService)
    {
        $this->investmentService = $investmentService;
    }

    /**
     * Display all investments.
     */
    public function index(Request $request)
    {
        $query = ProjectInvestment::with(['user', 'project', 'subscription'])
            ->orderBy('created_at', 'desc');

        // Filter by project
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $investments = $query->paginate(20);
        $projects = Project::orderBy('title')->get();
        $users = User::orderBy('name')->get();

        // Stats
        $stats = [
            'total_invested' => ProjectInvestment::active()->sum('amount'),
            'total_investors' => ProjectInvestment::active()->distinct('user_id')->count(),
            'active_investments' => ProjectInvestment::active()->count(),
        ];

        return view('admin.investments.index', compact('investments', 'projects', 'users', 'stats'));
    }

    /**
     * Show investments for a specific project.
     */
    public function byProject(Project $project)
    {
        $investments = ProjectInvestment::where('project_id', $project->id)
            ->with(['user', 'subscription'])
            ->orderBy('amount', 'desc')
            ->get();

        $investors = $this->investmentService->getProjectInvestors($project);
        $totalInvestment = $this->investmentService->getProjectTotalInvestment($project);

        return view('admin.investments.project', compact('project', 'investments', 'investors', 'totalInvestment'));
    }

    /**
     * Manually allocate investment.
     */
    public function allocate(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        $user = User::findOrFail($request->user_id);
        $project = Project::findOrFail($request->project_id);

        $investment = $this->investmentService->allocateToProject(
            $user,
            $project,
            $request->amount
        );

        return redirect()->route('admin.investments.index')
            ->with('success', "₹{$request->amount} allocated to {$user->name} for {$project->title}");
    }

    /**
     * Activate all investments for a project.
     */
    public function activateProject(Project $project)
    {
        $count = $this->investmentService->activateProjectInvestments($project);

        return redirect()->back()
            ->with('success', "{$count} investments activated for {$project->title}");
    }
}
