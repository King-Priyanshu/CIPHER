<x-layouts.admin>
    <x-slot:title>Payments</x-slot:title>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="card p-6">
            <p class="text-sm font-medium text-slate-500">Total Revenue</p>
            <p class="text-3xl font-bold text-navy mt-1">₹{{ number_format($stats['total_revenue'], 0) }}</p>
        </div>
        <div class="card p-6">
            <p class="text-sm font-medium text-slate-500">Total Payments</p>
            <p class="text-3xl font-bold text-navy mt-1">{{ $stats['total_payments'] }}</p>
        </div>
        <div class="card p-6">
            <p class="text-sm font-medium text-slate-500">Successful</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ $stats['successful'] }}</p>
        </div>
        <div class="card p-6">
            <p class="text-sm font-medium text-slate-500">Failed</p>
            <p class="text-3xl font-bold text-red-600 mt-1">{{ $stats['failed'] }}</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-6">
        <div class="p-4">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-sm font-medium text-slate-600 mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">All Status</option>
                        <option value="succeeded" {{ request('status') == 'succeeded' ? 'selected' : '' }}>Succeeded</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-sm font-medium text-slate-600 mb-1">Gateway</label>
                    <select name="gateway" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">All Gateways</option>
                        <option value="razorpay" {{ request('gateway') == 'razorpay' ? 'selected' : '' }}>Razorpay</option>
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-navy text-white rounded-lg text-sm font-medium hover:bg-slate-800 transition">
                    Filter
                </button>
            </form>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="card overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-navy">All Payments</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">ID</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">User</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Amount</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Gateway</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($payments as $payment)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-6 py-4 text-sm font-mono text-slate-600">#{{ $payment->id }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-sm">
                                    {{ substr($payment->user->name ?? 'U', 0, 1) }}
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-semibold text-navy">{{ $payment->user->name ?? 'Unknown' }}</p>
                                    <p class="text-xs text-slate-400">{{ $payment->user->email ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-navy">₹{{ number_format($payment->amount, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-slate-600 capitalize">{{ $payment->gateway ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $payment->status === 'succeeded' ? 'bg-green-100 text-green-800' : '' }}
                                {{ $payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                {{ $payment->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $payment->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.payments.show', $payment) }}" class="text-teal-600 hover:underline text-sm font-medium">View</a>
                            @if(!$payment->invoice)
                                <form action="{{ route('admin.invoices.generate', $payment) }}" method="POST" class="inline ml-2">
                                    @csrf
                                    <button type="submit" class="text-blue-600 hover:underline text-sm font-medium">
                                        Generate Invoice
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                            No payments found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $payments->links() }}
        </div>
        @endif
    </div>
</x-layouts.admin>
