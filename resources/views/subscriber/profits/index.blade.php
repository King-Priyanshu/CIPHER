<x-layouts.subscriber>
    <x-slot:title>My Profits</x-slot:title>

    <!-- Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="card p-6">
            <p class="text-sm font-medium text-slate-500">Total Profit Earned</p>
            <p class="text-3xl font-bold text-green-600 mt-1">₹{{ number_format($totalProfits, 2) }}</p>
        </div>
        <div class="card p-6">
            <p class="text-sm font-medium text-slate-500">Profit Credits</p>
            <p class="text-3xl font-bold text-navy mt-1">{{ $profitCount }}</p>
        </div>
    </div>

    <!-- Profit History -->
    <div class="card overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-navy">Profit History</h3>
        </div>
        
        @if($profits->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Project</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Amount</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($profits as $profit)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-6 py-4">
                            <p class="text-sm font-semibold text-navy">
                                {{ $profit->profitDistribution->project->title ?? 'N/A' }}
                            </p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-bold text-green-600">+₹{{ number_format($profit->amount, 2) }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $profit->status === 'credited' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $profit->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $profit->status === 'withdrawn' ? 'bg-blue-100 text-blue-800' : '' }}">
                                {{ ucfirst($profit->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500">
                            {{ $profit->credited_at?->format('M d, Y H:i') ?? $profit->created_at->format('M d, Y H:i') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-12 text-center text-slate-400">
            <div class="w-16 h-16 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="font-medium text-slate-600">No profits yet</p>
            <p class="text-sm mt-1">Profits will be credited when projects return earnings.</p>
        </div>
        @endif
    </div>
</x-layouts.subscriber>
