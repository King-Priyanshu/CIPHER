<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InvestmentPlan;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvestmentPlanController extends Controller
{
    /**
     * Display a listing of investment plans.
     */
     * Display a listing of investment plans.
     */
    public function index()
    {
        $plans = InvestmentPlan::with('project')->latest()->paginate(10);
        return view('admin.investment-plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $projects = Project::where('status', 'active')->get(); // Only active projects? or all?
        // Let's allow all for now
        $projects = Project::all();
        return view('admin.investment-plans.create', compact('projects'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:sip,onetime',
            'min_investment' => 'required|numeric|min:0',
            'max_investment' => 'nullable|numeric|min:0',
            'frequency' => 'required_if:type,sip|in:monthly,quarterly,yearly|nullable',
            'duration_months' => 'nullable|integer|min:1',
            'expected_return_percentage' => 'nullable|numeric|min:0',
            'refund_rule' => 'required|in:full,partial,none',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'tiers' => 'nullable|array',
        ]);

        $validated['slug'] = Str::slug($validated['name'] . '-' . Str::random(4));

        if (isset($validated['tiers'])) {
            $validated['tiers'] = json_encode($validated['tiers']);
        }

        InvestmentPlan::create($validated);

        return redirect()->route('admin.investment-plans.index')->with('success', 'Investment Plan created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(InvestmentPlan $investmentPlan)
    {
        return view('admin.investment-plans.show', compact('investmentPlan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InvestmentPlan $investmentPlan)
    {
        $projects = Project::all();
        return view('admin.investment-plans.edit', compact('investmentPlan', 'projects'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InvestmentPlan $investmentPlan)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'name' => 'required|string|max:255',
            'type' => 'required|in:sip,onetime',
            'min_investment' => 'required|numeric|min:0',
            'max_investment' => 'nullable|numeric|min:0',
            'frequency' => 'required_if:type,sip|in:monthly,quarterly,yearly|nullable',
            'duration_months' => 'nullable|integer|min:1',
            'expected_return_percentage' => 'nullable|numeric|min:0',
            'refund_rule' => 'required|in:full,partial,none',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'tiers' => 'nullable|array',
        ]);

        if ($investmentPlan->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name'] . '-' . Str::random(4));
        }

        if (isset($validated['tiers'])) {
            $validated['tiers'] = json_encode($validated['tiers']);
        }

        $investmentPlan->update($validated);

        return redirect()->route('admin.investment-plans.index')->with('success', 'Investment Plan updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InvestmentPlan $investmentPlan)
    {
        $investmentPlan->delete();
        return redirect()->route('admin.investment-plans.index')->with('success', 'Investment Plan deleted successfully.');
    }
}
