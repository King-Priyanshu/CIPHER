<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FundPool;
use Illuminate\Http\Request;

class FundPoolController extends Controller
{
    public function index()
    {
        $pools = FundPool::latest()->paginate(10);
        return view('admin.pools.index', compact('pools'));
    }

    public function create()
    {
        return view('admin.pools.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after:period_start',
            'total_amount' => 'required|numeric|min:0',
        ]);

        FundPool::create($validated);

        return redirect()->route('admin.pools.index')->with('success', 'Fund Pool created successfully.');
    }

    public function edit(FundPool $pool)
    {
        return view('admin.pools.edit', compact('pool'));
    }

    public function update(Request $request, FundPool $pool)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after:period_start',
            'total_amount' => 'required|numeric|min:0',
            'allocated_amount' => 'nullable|numeric|min:0',
        ]);

        $pool->update($validated);

        return redirect()->route('admin.pools.index')->with('success', 'Fund Pool updated successfully.');
    }

    public function destroy(FundPool $pool)
    {
        $pool->delete();

        return redirect()->route('admin.pools.index')->with('success', 'Fund Pool deleted successfully.');
    }
}
