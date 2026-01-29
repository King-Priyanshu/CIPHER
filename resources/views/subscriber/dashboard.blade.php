<x-layouts.subscriber>
    <x-slot:title>
        Dashboard
    </x-slot:title>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Current Plan -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Current Plan</p>
                    <p class="text-3xl font-bold text-navy mt-1">{{ $subscription ? $subscription->plan->name : 'Free' }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-teal-100 flex items-center justify-center shadow-sm">
                    <svg class="w-6 h-6 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Next Billing -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Next Billing</p>
                    <p class="text-3xl font-bold text-navy mt-1">{{ $nextPaymentDate ? $nextPaymentDate->format('M d') : 'N/A' }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center shadow-sm">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Total Rewards -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Rewards</p>
                    <p class="text-3xl font-bold text-navy mt-1">${{ number_format($totalRewards, 2) }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center shadow-sm">
                    <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="bg-gradient-to-br from-indigo-500 to-teal-400 rounded-xl shadow-lg shadow-indigo-500/20 p-6 text-white transform hover:-translate-y-1 transition duration-300">
            <p class="text-sm font-medium opacity-90">Quick Actions</p>
            <div class="mt-4 space-y-2">
                <a href="{{ route('subscriber.projects.index') }}" class="block text-sm hover:opacity-80 transition flex items-center gap-2">
                    <span>→</span> Browse Projects
                </a>
                <a href="{{ route('subscriber.rewards.index') }}" class="block text-sm hover:opacity-80 transition flex items-center gap-2">
                    <span>→</span> View Rewards
                </a>
                <a href="{{ route('subscriber.subscription.index') }}" class="block text-sm hover:opacity-80 transition flex items-center gap-2">
                    <span>→</span> Manage Plan
                </a>
            </div>
        </div>
    </div>

    <!-- Active Projects Table -->
    <div class="card overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-white">
            <h3 class="text-lg font-bold text-navy">Active Projects</h3>
            <a href="{{ route('subscriber.projects.index') }}" class="text-sm font-medium text-teal-600 hover:text-teal-700 transition">View All →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Project</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Pool Goal</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Created</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($activeProjects as $project)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-9 h-9 rounded-xl bg-teal-50 flex items-center justify-center text-teal-600 font-bold text-sm shadow-sm ring-1 ring-teal-100">
                                    {{ substr($project->title, 0, 1) }}
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-semibold text-navy">{{ $project->title }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-navy">${{ number_format($project->target_fund ?? 0, 2) }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 border border-emerald-200">
                                Active
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $project->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <a href="#" class="text-teal-600 hover:text-teal-700 text-sm font-semibold hover:underline">View Details</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                            </div>
                            <p class="font-medium text-slate-600">No active projects yet</p>
                            <a href="{{ route('subscriber.projects.index') }}" class="text-teal-600 hover:underline mt-2 inline-block text-sm font-medium">Browse available projects</a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.subscriber>
