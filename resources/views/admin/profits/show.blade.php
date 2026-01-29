<x-layouts.admin>
    <x-slot:title>Distribution Details</x-slot:title>

    <div class="max-w-4xl mx-auto">
        <!-- Distribution Info -->
        <div class="card mb-6">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-navy">{{ $profit->project->title }}</h2>
                    <p class="text-sm text-slate-500">Profit Distribution #{{ $profit->id }}</p>
                </div>
                <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium
                    {{ $profit->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $profit->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $profit->status === 'distributing' ? 'bg-blue-100 text-blue-800' : '' }}">
                    {{ ucfirst($profit->status) }}
                </span>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    <div>
                        <p class="text-sm text-slate-500">Total Profit</p>
                        <p class="text-xl font-bold text-navy">₹{{ number_format($profit->total_profit, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Distributed</p>
                        <p class="text-xl font-bold text-green-600">₹{{ number_format($profit->distributed_amount, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Declared By</p>
                        <p class="text-sm font-semibold text-navy">{{ $profit->declaredBy->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Declared At</p>
                        <p class="text-sm font-semibold text-navy">{{ $profit->declared_at?->format('M d, Y H:i') }}</p>
                    </div>
                </div>

                @if($profit->notes)
                <div class="mt-4 p-4 bg-slate-50 rounded-lg">
                    <p class="text-sm text-slate-600">{{ $profit->notes }}</p>
                </div>
                @endif

                @if($profit->status === 'pending')
                <div class="mt-6">
                    <form action="{{ route('admin.profits.distribute', $profit) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition"
                                onclick="return confirm('This will distribute profit to all investors. Continue?')">
                            Distribute to Investors
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>

        <!-- Profit Logs -->
        @if($profit->profitLogs->count() > 0)
        <div class="card overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg font-bold text-navy">Distribution Details</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Investor</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Amount</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Credited At</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($profit->profitLogs as $log)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-teal-100 flex items-center justify-center text-teal-600 font-bold text-sm">
                                        {{ substr($log->user->name, 0, 1) }}
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-semibold text-navy">{{ $log->user->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $log->user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-green-600">₹{{ number_format($log->amount, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ ucfirst($log->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500">{{ $log->credited_at?->format('M d, Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</x-layouts.admin>
