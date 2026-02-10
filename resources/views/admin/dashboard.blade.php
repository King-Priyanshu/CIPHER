<x-layouts.admin>
    <x-slot:title>
        Admin Dashboard
    </x-slot:title>

    <!-- Primary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Users -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Users</p>
                    <p class="text-3xl font-bold text-navy mt-1">{{ $stats['total_users'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center shadow-sm">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Active Subscriptions -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Active Subscriptions</p>
                    <p class="text-3xl font-bold text-navy mt-1">{{ $stats['active_subscriptions'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-teal-100 flex items-center justify-center shadow-sm">
                    <svg class="w-6 h-6 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Total Revenue -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Revenue</p>
                    <p class="text-3xl font-bold text-navy mt-1">₹{{ number_format($stats['total_revenue'] ?? 0, 0) }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center shadow-sm">
                    <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Active Projects -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Active Projects</p>
                    <p class="text-3xl font-bold text-navy mt-1">{{ $stats['active_projects'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center shadow-sm">
                    <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Investment & Profit Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Invested -->
        <div class="card p-6 bg-gradient-to-br from-blue-50 to-indigo-50 border-blue-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-600">Total Invested</p>
                    <p class="text-2xl font-bold text-navy mt-1">₹{{ number_format($stats['total_invested'] ?? 0, 0) }}</p>
                </div>
                @if(Route::has('admin.investments.index'))
                <a href="{{ route('admin.investments.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">View →</a>
                @endif
            </div>
        </div>
        
        <!-- Pooled Funds (Available for Allocation) -->
        <div class="card p-6 bg-gradient-to-br from-indigo-50 to-purple-50 border-indigo-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-indigo-600">Pooled Funds (Users' Wallets)</p>
                    <p class="text-2xl font-bold text-navy mt-1">₹{{ number_format($stats['total_pooled_funds'] ?? 0, 0) }}</p>
                </div>
            </div>
        </div>
        
        <!-- Total Profits Distributed -->
        <div class="card p-6 bg-gradient-to-br from-green-50 to-emerald-50 border-green-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-green-600">Profits Distributed</p>
                    <p class="text-2xl font-bold text-navy mt-1">₹{{ number_format($stats['total_profits_distributed'] ?? 0, 0) }}</p>
                </div>
                @if(Route::has('admin.profits.index'))
                <a href="{{ route('admin.profits.index') }}" class="text-green-600 hover:text-green-700 text-sm font-medium">View →</a>
                @endif
            </div>
        </div>
        
        <!-- Pending Distributions -->
        <div class="card p-6 {{ ($stats['pending_distributions'] ?? 0) > 0 ? 'bg-gradient-to-br from-yellow-50 to-amber-50 border-yellow-100' : '' }}">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium {{ ($stats['pending_distributions'] ?? 0) > 0 ? 'text-yellow-600' : 'text-slate-500' }}">Pending Distributions</p>
                    <p class="text-2xl font-bold text-navy mt-1">{{ $stats['pending_distributions'] ?? 0 }}</p>
                </div>
                @if(($stats['pending_distributions'] ?? 0) > 0 && Route::has('admin.profits.index'))
                <a href="{{ route('admin.profits.index') }}?status=pending" class="px-3 py-1.5 bg-yellow-500 text-white text-xs font-semibold rounded-lg hover:bg-yellow-600 transition">
                    Review
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions & Recent Payments -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Quick Actions -->
        <div class="bg-gradient-to-br from-slate-900 to-slate-800 rounded-xl shadow-lg p-6 text-white">
            <h3 class="text-lg font-bold mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 p-3 rounded-lg bg-white/10 hover:bg-white/20 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span class="text-sm font-medium">Manage Users</span>
                </a>
                <a href="{{ route('admin.projects.index') }}" class="flex items-center gap-3 p-3 rounded-lg bg-white/10 hover:bg-white/20 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span class="text-sm font-medium">Manage Projects</span>
                </a>
                <a href="{{ route('admin.audit-logs.index') }}" class="flex items-center gap-3 p-3 rounded-lg bg-white/10 hover:bg-white/20 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    <span class="text-sm font-medium">Audit Logs</span>
                </a>
            </div>
        </div>

        <!-- Recent Logs -->
        <div class="lg:col-span-2 card overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-navy">Recent Activity</h3>
                <a href="{{ route('admin.audit-logs.index') }}" class="text-sm text-blue-600 hover:underline">View All</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <tbody class="divide-y divide-gray-100">
                        @forelse($stats['recent_logs'] ?? [] as $log)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-3 text-sm font-medium text-navy">
                                {{ $log->user->name ?? 'System' }}
                            </td>
                            <td class="px-6 py-3 text-sm text-slate-600">
                                {{ $log->action }}
                            </td>
                            <td class="px-6 py-3 text-sm text-slate-500 max-w-xs truncate">
                                {{ $log->description }}
                            </td>
                            <td class="px-6 py-3 text-sm text-slate-400 text-right">
                                {{ $log->created_at->diffForHumans() }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-400">
                                No activity recorded yet
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>
