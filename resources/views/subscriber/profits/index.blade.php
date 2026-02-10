<x-layouts.subscriber>
    <x-slot:title>
        Royalty History
    </x-slot:title>

    <div class="max-w-5xl mx-auto space-y-8">
        
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="card p-6 bg-gradient-to-br from-teal-500 to-emerald-600 text-white">
                <p class="text-sm font-medium opacity-90">Available for Redemption</p>
                <div class="flex items-end justify-between mt-2">
                    <p class="text-3xl font-bold">₹{{ number_format($totalCredited, 2) }}</p>
                    @if($totalCredited > 0)
                        <form action="{{ route('subscriber.profits.redeem') }}" method="POST">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-white text-teal-600 rounded-lg text-sm font-bold hover:bg-teal-50 transition shadow-sm">
                                Redeem Now
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="card p-6 border-l-4 border-amber-400">
                <p class="text-sm font-medium text-slate-500">Accrued Royalties (Locked)</p>
                <p class="text-3xl font-bold text-navy mt-2">₹{{ number_format($totalAccrued, 2) }}</p>
                <p class="text-xs text-slate-400 mt-2">Royalties are locked for 11 months from declaration.</p>
            </div>
        </div>

        <!-- Profit History Table -->
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-bold text-navy">Earnings History</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Project</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase text-right">Amount</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Status</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Date</th>
                            <th class="px-6 py-4 text-xs font-bold text-slate-500 uppercase">Details</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($profits as $profit)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4">
                                <p class="text-sm font-semibold text-navy">{{ $profit->distribution->project->title }}</p>
                                <p class="text-xs text-slate-400">{{ $profit->distribution->month ? $profit->distribution->month->format('M Y') : 'N/A' }}</p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="font-bold text-emerald-600">+₹{{ number_format($profit->amount, 2) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                    $profit->status === 'credited' ? 'bg-emerald-100 text-emerald-800' : 
                                    ($profit->status === 'pending' ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600') 
                                }}">
                                    {{ ucfirst($profit->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ $profit->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500">
                                From Investment date: {{ $profit->project_investment_id ? \App\Models\ProjectInvestment::find($profit->project_investment_id)?->created_at->format('M d, Y') : 'N/A' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                No royalty earnings yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($profits->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $profits->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.subscriber>
