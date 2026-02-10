<x-layouts.subscriber>
    <x-slot:title>
        Dashboard
    </x-slot:title>

    <!-- Portfolio Overview - Premium Professional Header -->
    <div class="dashboard-hero bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 overflow-hidden rounded-3xl p-8 mb-8 text-white shadow-2xl relative">
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-8">
            <!-- Left Side: Portfolio Value -->
            <div class="space-y-4">
                <div class="flex items-center gap-2 text-teal-400 font-semibold tracking-wide uppercase text-xs">
                    <div class="p-1.5 bg-teal-500/10 rounded-lg">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                    </div>
                    <span>Total Asset Value</span>
                </div>
                <div>
                     <h1 class="text-6xl font-black tracking-tighter text-transparent bg-clip-text bg-gradient-to-r from-white via-slate-200 to-slate-400 drop-shadow-sm">
                        ₹{{ number_format($totalInvested + $totalProfits + $accruedProfits, 2) }}
                    </h1>
                    <p class="text-slate-400 text-sm mt-1">Net Portfolio Valuation</p>
                </div>
                
                <div class="flex flex-wrap items-center gap-4 text-sm">
                    <div class="flex items-center gap-1.5 bg-white/10 px-3 py-1 rounded-full border border-white/10">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="text-slate-300">Member since</span>
                        <span class="font-bold">{{ $user->created_at->format('M Y') }}</span>
                    </div>
                    
                    <span class="px-3 py-1 rounded-full bg-{{ $user->participation_mode === 'manual' ? 'indigo' : 'teal' }}-500/20 text-{{ $user->participation_mode === 'manual' ? 'indigo' : 'teal' }}-300 border border-{{ $user->participation_mode === 'manual' ? 'indigo' : 'teal' }}-500/30 text-[10px] font-bold uppercase tracking-widest">
                        {{ $user->participation_mode }} Mode
                    </span>
                </div>
            </div>

            <!-- Right Side: Metrics Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-2 xl:grid-cols-3 gap-4">
                <div class="glass-card rounded-2xl p-4 transition-transform hover:scale-105">
                    <p class="text-[10px] text-teal-400 uppercase font-bold tracking-widest mb-1">Total Put In</p>
                    <p class="text-2xl font-black">₹{{ number_format($totalInvested, 0) }}</p>
                </div>
                
                <div class="glass-card rounded-2xl p-4 transition-transform hover:scale-105 border-emerald-500/20">
                    <p class="text-[10px] text-emerald-400 uppercase font-bold tracking-widest mb-1">Total Profits</p>
                    <p class="text-2xl font-black text-emerald-400">₹{{ number_format($totalProfits + $accruedProfits, 0) }}</p>
                </div>

                <div class="glass-card rounded-2xl p-4 transition-transform hover:scale-105 hidden sm:block">
                    <p class="text-[10px] text-indigo-300 uppercase font-bold tracking-widest mb-1">Live Projects</p>
                    <p class="text-2xl font-black text-white">{{ $activeProjects }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        
        <!-- Left Column: Allocations & Activity -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Project Portfolio - Trading Style -->
            <div class="card overflow-hidden border-0 shadow-xl ring-1 ring-slate-200">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-indigo-50/50 to-white">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-200">
                            <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                        </div>
                        <div>
                            <h3 class="font-black text-navy tracking-tight text-lg">Portfolio Assets</h3>
                            <p class="text-[10px] text-slate-400 uppercase font-bold tracking-widest">Active Stakes</p>
                        </div>
                    </div>
                    <a href="{{ route('subscriber.investments.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 bg-indigo-50 px-3 py-1.5 rounded-lg transition-all">Details →</a>
                </div>
                
                <div class="divide-y divide-gray-50">
                    @forelse($investmentsByProject as $item)
                    <div class="px-5 py-4 hover:bg-slate-50/50 transition flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <!-- Project Icon with Gradient -->
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold shadow-lg shadow-indigo-500/20">
                                {{ substr($item['project']->title ?? 'P', 0, 2) }}
                            </div>
                            <div>
                                <p class="font-semibold text-navy">{{ $item['project']->title ?? 'Unknown Project' }}</p>
                                <p class="text-xs text-slate-400">{{ $item['count'] }} allocation(s) · {{ $item['project']->status ?? 'Active' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-navy">₹{{ number_format($item['total'], 0) }}</p>
                            <p class="text-xs text-emerald-500 font-medium">Active</p>
                        </div>
                    </div>
                    @empty
                    <div class="px-5 py-12 text-center">
                        <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                        </div>
                        <p class="font-medium text-slate-600">No investments yet</p>
                        <p class="text-sm text-slate-400 mt-1">Start contributing to projects to build your portfolio</p>
                        @if($subscription && $subscription->isActive())
                        <a href="{{ route('subscriber.projects.index') }}" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                            Explore Projects
                        </a>
                        @endif
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Activity Feed -->
            <div class="card p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center">
                        <svg class="w-4 h-4 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h3 class="font-bold text-navy">Recent Activity</h3>
                </div>
                
                @if($activities->count() > 0)
                <div class="space-y-3">
                    @foreach($activities->take(5) as $activity)
                    <div class="flex items-start gap-3 text-sm">
                        <div class="w-2 h-2 rounded-full bg-teal-500 mt-2 shrink-0"></div>
                        <div class="flex-1">
                            <p class="text-slate-700">{{ $activity->description ?? $activity->action }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $activity->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-slate-400 text-center py-4">No recent activity</p>
                @endif
            </div>
        </div>

        <!-- Right Column: Stats & Actions -->
        <div class="space-y-6">
            
            <!-- Royalty Earnings Card -->
            <div class="card p-5 border-l-4 border-emerald-500">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Royalty Earnings</p>
                        <p class="text-xl font-bold text-navy">₹{{ number_format($totalProfits + $accruedProfits, 2) }}</p>
                    </div>
                </div>
                
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-slate-500 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Available
                        </span>
                        <span class="font-bold text-emerald-600">₹{{ number_format($totalProfits, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-slate-500 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span> Locked (Pending)
                        </span>
                        <span class="font-bold text-amber-600">₹{{ number_format($accruedProfits, 2) }}</span>
                    </div>
                </div>

                @if($totalProfits > 0)
                <form action="{{ route('subscriber.profits.redeem') }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-lg transition shadow-lg shadow-emerald-500/20">
                        Redeem ₹{{ number_format($totalProfits, 2) }}
                    </button>
                </form>
                @endif
            </div>

            <!-- Subscription Status -->
            <div class="card p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" /></svg>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Current Plan</p>
                        <p class="text-lg font-bold text-navy">{{ $subscription?->plan?->name ?? 'Free Tier' }}</p>
                    </div>
                </div>
                
                @if($subscription && $subscription->isActive())
                <div class="bg-slate-50 rounded-lg p-3 text-sm">
                    <div class="flex justify-between items-center">
                        <span class="text-slate-500">Monthly</span>
                        <span class="font-bold text-navy">₹{{ number_format($subscription->plan->price ?? 0, 0) }}</span>
                    </div>
                    @if($nextPaymentDate)
                    <div class="flex justify-between items-center mt-2 pt-2 border-t border-gray-200">
                        <span class="text-slate-500">Next billing</span>
                        <span class="text-slate-700">{{ $nextPaymentDate->format('M d, Y') }}</span>
                    </div>
                    @endif
                </div>
                
                <a href="{{ route('subscriber.subscription.index') }}" class="block mt-4 text-center py-2 border border-slate-200 text-slate-600 text-sm font-medium rounded-lg hover:bg-slate-50 transition">
                    Manage Plan
                </a>
                @else
                <a href="{{ route('subscriber.subscription.index') }}" class="block mt-4 text-center py-2.5 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition">
                    Subscribe Now
                </a>
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="bg-gray-50 card p-5 bg-gradient-to-br from-slate-800 to-slate-900 text-white">
                <h4 class="font-bold mb-4">Quick Actions</h4>
                <div class="space-y-2">
                    <a href="{{ route('subscriber.projects.index') }}" class="flex items-center gap-3 p-3 rounded-lg bg-white/10 hover:bg-white/20 transition text-sm">
                        <svg class="w-5 h-5 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>
                        Browse Projects
                    </a>
                    <a href="{{ route('subscriber.card.show') }}" class="flex items-center gap-3 p-3 rounded-lg bg-white/10 hover:bg-white/20 transition text-sm">
                        <svg class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" /></svg>
                        Member Card
                    </a>
                    <a href="{{ route('subscriber.payments.index') }}" class="flex items-center gap-3 p-3 rounded-lg bg-white/10 hover:bg-white/20 transition text-sm">
                        <svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                        Payment History
                    </a>
                    <a href="{{ route('subscriber.deposit.create') }}" class="flex items-center gap-3 p-3 rounded-lg bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 transition text-sm font-bold border border-emerald-500/30">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></svg>
                        Add Funds
                    </a>
                </div>
            </div>

            <!-- Referral Card -->
            @if($user->referral_code)
            <div class="card p-5 border border-dashed border-amber-300 bg-amber-50">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" /></svg>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-600">Invite Friends</p>
                        <p class="text-xs text-slate-500">Earn rewards for referrals</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 bg-white rounded-lg p-2 border border-amber-200">
                    <code class="flex-1 text-center font-mono font-bold text-amber-700">{{ $user->referral_code }}</code>
                    <button onclick="navigator.clipboard.writeText('{{ $user->referral_code }}')" class="p-2 hover:bg-amber-100 rounded transition" title="Copy">
                        <svg class="w-4 h-4 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                    </button>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-layouts.subscriber>
