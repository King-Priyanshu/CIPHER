<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\InvestmentPlan;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::where('status', 'active')
            ->where('visibility_status', 'visible')
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(9);

        return view('public.projects.index', compact('projects'));
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $project = Project::where('status', '!=', 'draft')
            ->where('visibility_status', 'visible')
            ->findOrFail($id);
            
        $plans = InvestmentPlan::all();
        $investorCount = $project->investments()->active()->distinct('user_id')->count();
        
        // Mock data for graph if needed, or real data
        $recentInvestments = $project->investments()->with('user')->latest()->take(5)->get();

        return view('public.projects.show', compact('project', 'plans', 'investorCount', 'recentInvestments'));
    }
}
