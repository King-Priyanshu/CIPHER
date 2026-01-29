<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use App\Models\ProjectInvestment;
use App\Models\UserProfitLog;
use App\Services\InvestmentAllocationService;
use App\Services\ProfitDistributionService;
use Illuminate\Support\Facades\Auth;

class InvestmentController extends Controller
{
    protected InvestmentAllocationService $investmentService;
    protected ProfitDistributionService $profitService;

    public function __construct(
        InvestmentAllocationService $investmentService,
        ProfitDistributionService $profitService
    ) {
        $this->investmentService = $investmentService;
        $this->profitService = $profitService;
    }

    /**
     * Display user's investments.
     */
    public function index()
    {
        $user = Auth::user();

        $investments = ProjectInvestment::where('user_id', $user->id)
            ->with('project')
            ->orderBy('created_at', 'desc')
            ->get();

        $totalInvested = $this->investmentService->getUserTotalInvestment($user);
        $totalProfits = $this->profitService->getUserTotalProfits($user);
        $activeProjects = $investments->where('status', '!=', 'withdrawn')->pluck('project_id')->unique()->count();

        return view('subscriber.investments.index', compact(
            'investments',
            'totalInvested',
            'totalProfits',
            'activeProjects'
        ));
    }

    /**
     * Display user's profit history.
     */
    public function profits()
    {
        $user = Auth::user();

        $profits = $this->profitService->getUserProfitHistory($user, 50);
        $totalProfits = $this->profitService->getUserTotalProfits($user);
        $profitCount = UserProfitLog::where('user_id', $user->id)->count();

        return view('subscriber.profits.index', compact('profits', 'totalProfits', 'profitCount'));
    }
}
