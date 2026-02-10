<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProfitDistribution;
use App\Models\Project;
use App\Services\RoyaltyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfitDistributionController extends Controller
{
    protected RoyaltyService $royaltyService;
    protected \App\Services\ActivityLogger $logger;

    public function __construct(RoyaltyService $royaltyService, \App\Services\ActivityLogger $logger)
    {
        $this->royaltyService = $royaltyService;
        $this->logger = $logger;
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
        $stats = [
            'total_declared' => ProfitDistribution::sum('total_profit'),
            'total_distributed' => ProfitDistribution::sum('distributed_amount'),
            'pending_distributions' => ProfitDistribution::where('status', 'pending')->count(),
            'total_profit_logs' => \App\Models\UserProfitLog::count(),
        ];

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
            'month' => 'required|date', // or date_format:Y-m
            'supporting_documents' => 'nullable|file|mimes:pdf,jpg,png,doc,docx|max:10240',
            'notes' => 'nullable|string|max:1000',
        ]);

        $project = Project::findOrFail($request->project_id);

        $documents = [];
        if ($request->hasFile('supporting_documents')) {
            $path = $request->file('supporting_documents')->store('profit-documents', 'public');
            $documents[] = $path; // Store as array for future multiple files support
        }

        $distribution = ProfitDistribution::create([
            'project_id' => $project->id,
            'total_profit' => $request->amount,
            'month' => $request->month,
            'declared_by' => Auth::id(),
            'notes' => $request->notes,
            'supporting_documents' => $documents,
            'status' => 'pending',
            'declared_at' => now(),
        ]);

        $this->logger->log('profit.declared', "Declared Profit ₹{$request->amount} for Project {$project->id}", 'ProfitDistribution', $distribution->id, 'info');

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
            $count = $this->royaltyService->distribute($profit);
            
            $this->logger->logFinancial('profit.distributed', "Distributed Profit #{$profit->id} to {$count} investors", $profit);

            return redirect()->route('admin.profits.show', $profit)
                ->with('success', "Profit distributed to {$count} investors");

        } catch (\Exception $e) {
            $this->logger->log('profit.distribution_failed', "Failed to distribute Profit #{$profit->id}: " . $e->getMessage(), 'ProfitDistribution', $profit->id, 'critical');
            return redirect()->back()
                ->with('error', 'Distribution failed: ' . $e->getMessage());
        }
    }
}
