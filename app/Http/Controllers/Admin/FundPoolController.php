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
        return view('admin.funds.index', compact('pools'));
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

        return redirect()->back()->with('success', 'Fund Pool initialized.');
    }
}
