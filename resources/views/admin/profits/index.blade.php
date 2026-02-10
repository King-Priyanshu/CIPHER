<x-layouts.admin>
    <x-slot:title>Profit Distributions</x-slot:title>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="card p-6">
            <p class="text-sm font-medium text-slate-500">Total Declared</p>
            <p class="text-3xl font-bold text-navy mt-1">₹{{ number_format($stats['total_declared'], 0) }}</p>
        </div>
        <div class="card p-6">
            <p class="text-sm font-medium text-slate-500">Total Distributed</p>
            <p class="text-3xl font-bold text-green-600 mt-1">₹{{ number_format($stats['total_distributed'], 0) }}</p>
        </div>
        <div class="card p-6">
            <p class="text-sm font-medium text-slate-500">Pending</p>
            <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $stats['pending_distributions'] }}</p>
        </div>
        <div class="card p-6">
            <p class="text-sm font-medium text-slate-500">Profit Records</p>
            <p class="text-3xl font-bold text-navy mt-1">{{ $stats['total_profit_logs'] }}</p>
        </div>
    </div>

    <!-- Action Bar -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-navy">Distributions</h2>
        <a href="{{ route('admin.profits.create') }}" class="px-4 py-2 bg-teal-600 text-white rounded-lg text-sm font-medium hover:bg-teal-700 transition flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Declare Profit
        </a>
    </div>

    <!-- Distributions Table -->
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Project</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Total Profit</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Distributed</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Month</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Declared By</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($distributions as $dist)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-6 py-4">
                            <p class="text-sm font-semibold text-navy">{{ $dist->project->title }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-navy">₹{{ number_format($dist->total_profit, 2) }}</td>
                        <td class="px-6 py-4 text-sm font-bold text-green-600">₹{{ number_format($dist->distributed_amount, 2) }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $dist->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $dist->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $dist->status === 'distributing' ? 'bg-blue-100 text-blue-800' : '' }}">
                                {{ ucfirst($dist->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm font-medium text-navy">{{ $dist->month ? $dist->month->format('M Y') : '-' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600">{{ $dist->declaredBy->name }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $dist->declared_at?->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.profits.show', $dist) }}" class="text-teal-600 hover:underline text-sm font-medium">View</a>
                            @if($dist->status === 'pending')
                                <form action="{{ route('admin.profits.distribute', $dist) }}" method="POST" class="inline ml-2">
                                    @csrf
                                    <button type="submit" class="text-green-600 hover:underline text-sm font-medium" onclick="return confirm('Distribute profit to all investors?')">
                                        Distribute
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                            No profit distributions yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($distributions->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $distributions->links() }}
        </div>
        @endif
    </div>
</x-layouts.admin>
