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
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
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
                @if(Route::has('admin.payments.index'))
                <a href="{{ route('admin.payments.index') }}" class="flex items-center gap-3 p-3 rounded-lg bg-white/10 hover:bg-white/20 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                    </svg>
                    <span class="text-sm font-medium">View Payments</span>
                </a>
                @endif
                @if(Route::has('admin.settings.payment-gateway'))
                <a href="{{ route('admin.settings.payment-gateway') }}" class="flex items-center gap-3 p-3 rounded-lg bg-teal-500 hover:bg-teal-600 transition">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="text-sm font-medium">Payment Settings</span>
                </a>
                @endif
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="lg:col-span-2 card overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-navy">Recent Payments</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">User</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($stats['recent_payments'] ?? [] as $payment)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-3">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-sm">
                                        {{ substr($payment->user->name ?? 'U', 0, 1) }}
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-semibold text-navy">{{ $payment->user->name ?? 'Unknown' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3 text-sm font-bold text-navy">₹{{ number_format($payment->amount ?? 0, 0) }}</td>
                            <td class="px-6 py-3">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium 
                                    {{ $payment->status == 'succeeded' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $payment->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $payment->status == 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ ucfirst($payment->status ?? 'N/A') }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-sm text-slate-500">{{ $payment->created_at?->format('M d, Y') ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-slate-400">
                                No payments yet
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.admin>
