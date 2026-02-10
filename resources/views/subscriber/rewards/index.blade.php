<x-layouts.subscriber>
    <x-slot:title>
        Rewards
    </x-slot:title>

    <div class="space-y-6">
        
        <!-- Rewards Summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="card p-6">
                <p class="text-sm font-medium text-slate-500">Total Earned</p>
                <p class="text-3xl font-bold text-navy mt-1">₹{{ number_format($totalRewards ?? 0, 2) }}</p>
            </div>
            <div class="card p-6">
                <p class="text-sm font-medium text-slate-500">Last Payout</p>
                <p class="text-3xl font-bold text-navy mt-1">₹0.00</p>
                <!-- Placeholder for future logic -->
            </div>
            <div class="card p-6">
                <p class="text-sm font-medium text-slate-500">Next Projected</p>
                <div class="flex items-center gap-2 mt-1">
                    <p class="text-3xl font-bold text-navy">--</p>
                    <span class="text-xs text-slate-400">(Estimating)</span>
                </div>
            </div>
        </div>

        <!-- Rewards History Table -->
        <div class="card overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-white">
                <h3 class="text-lg font-bold text-navy">Reward History</h3>
            </div>
            <div class="overflow-x-auto">
                {{-- Assuming $rewards is passed from controller. If not, we'll need empty state. 
                     I will handle both cases safeley. --}}
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Source</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @if(isset($rewards) && $rewards->isNotEmpty())
                            @foreach($rewards as $reward)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="px-6 py-4 text-sm text-slate-600">
                                        {{ $reward->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                                            {{ $reward->source_type ?? 'System' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-navy font-medium">
                                        {{ $reward->description }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-bold text-emerald-600">
                                        +₹{{ number_format($reward->amount, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                    <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <p>No rewards received yet.</p>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.subscriber>
