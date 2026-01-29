<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RewardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $rewards = $user->rewards()
            ->with(['rewardPool.project'])
            ->latest()
            ->paginate(10);
            
        $totalRewards = $user->rewards()->sum('amount');

        return view('subscriber.rewards.index', compact('rewards', 'totalRewards'));
    }
}
