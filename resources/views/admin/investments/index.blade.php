<x-layouts.admin>
    <x-slot:title>Investments</x-slot:title>

    <!-- Stats -->
    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="card p-6">
            <p class="text-sm font-medium text-slate-500">Pooled Funds</p>
            <p class="text-3xl font-bold text-amber-600 mt-1">₹{{ number_format($stats['pooled_funds'], 0) }}</p>
        </div>
        <div class="card p-6">
            <p class="text-sm font-medium text-slate-500">Total Invested</p>
            <p class="text-3xl font-bold text-navy mt-1">₹{{ number_format($stats['total_invested'], 0) }}</p>
        </div>
        <div class="card p-6">
            <p class="text-sm font-medium text-slate-500">Total Investors</p>
            <p class="text-3xl font-bold text-navy mt-1">{{ $stats['total_investors'] }}</p>
        </div>
        <div class="card p-6">
            <p class="text-sm font-medium text-slate-500">Active Investments</p>
            <p class="text-3xl font-bold text-navy mt-1">{{ $stats['active_investments'] }}</p>
        </div>
    </div>

    <!-- Manual Allocation -->
    <div class="card mb-6">
        <div class="p-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg font-bold text-navy">Manual Allocation</h3>
            <span class="text-xs text-slate-400">Allocate user funds to a specific project</span>
        </div>
        <div class="p-4">
            <form action="{{ route('admin.investments.allocate') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                @csrf
                <div class="flex-1">
                    <label class="block text-sm font-medium text-slate-600 mb-1">User</label>
                    <select name="user_id" class="w-full rounded-lg border-gray-300 text-sm" required>
                        <option value="">Select User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} (Available: ₹{{ number_format($user->subscription?->amount - $user->subscription?->allocated_amount, 0) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-slate-600 mb-1">Project</label>
                    <select name="project_id" class="w-full rounded-lg border-gray-300 text-sm" required>
                        <option value="">Select Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-slate-600 mb-1">Amount (₹)</label>
                    <input type="number" name="amount" min="1" step="0.01" class="w-full rounded-lg border-gray-300 text-sm" placeholder="e.g. 5000" required value="{{ old('amount') }}">
                </div>
                <div>
                    <button type="submit" class="w-full px-4 py-2 bg-teal-600 text-white rounded-lg text-sm font-medium hover:bg-teal-700 transition">
                        Allocate Funds
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-6">
        <div class="p-4 border-b border-gray-100">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-slate-600 mb-1">Project</label>
                    <select name="project_id" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">All Projects</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-slate-600 mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">All Status</option>
                        <option value="allocated" {{ request('status') == 'allocated' ? 'selected' : '' }}>Allocated</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="withdrawn" {{ request('status') == 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-navy text-white rounded-lg text-sm font-medium hover:bg-slate-800 transition">
                    Filter
                </button>
            </form>
        </div>
    </div>

    <!-- Investments Table -->
    <div class="card overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-navy">All Investments</h3>
            <div class="flex gap-2">
                <form action="{{ route('admin.investments.auto-allocate') }}" method="POST" onsubmit="return confirm('Run Auto-Allocation for ALL eligible auto-mode users?');">
                    @csrf
                    <button type="submit" class="btn-secondary text-xs px-3 py-1.5">
                        Run Auto Allocation
                    </button>
                </form>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">User</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Project</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Amount</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Allocated</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($investments as $investment)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-sm">
                                    {{ substr($investment->user->name, 0, 1) }}
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-semibold text-navy">{{ $investment->user->name }}</p>
                                    <p class="text-xs text-slate-400">{{ $investment->user->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-navy">{{ $investment->project->title }}</td>
                        <td class="px-6 py-4 text-sm font-bold text-navy">₹{{ number_format($investment->amount, 2) }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $investment->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $investment->status === 'allocated' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $investment->status === 'withdrawn' ? 'bg-gray-100 text-gray-800' : '' }}">
                                {{ ucfirst($investment->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500">
                            {{ $investment->allocated_at?->format('M d, Y') ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                            No investments found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($investments->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $investments->links() }}
        </div>
        @endif
    </div>
</x-layouts.admin>
