<x-layouts.admin>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Stat Card 1 -->
        <div class="bg-gray-800 p-6 rounded-lg shadow border border-gray-700">
            <h3 class="text-gray-400 text-sm font-medium">Total Users</h3>
            <p class="text-3xl font-bold text-white mt-2">{{ $stats['total_users'] }}</p>
        </div>
        
        <!-- Stat Card 2 -->
        <div class="bg-gray-800 p-6 rounded-lg shadow border border-gray-700">
            <h3 class="text-gray-400 text-sm font-medium">Active Subs</h3>
            <p class="text-3xl font-bold text-green-400 mt-2">{{ $stats['active_subscriptions'] }}</p>
        </div>

        <!-- Stat Card 3 -->
        <div class="bg-gray-800 p-6 rounded-lg shadow border border-gray-700">
            <h3 class="text-gray-400 text-sm font-medium">Revenue</h3>
            <p class="text-3xl font-bold text-blue-400 mt-2">${{ number_format($stats['total_revenue'], 2) }}</p>
        </div>
    </div>

    <!-- Recent Payments Table -->
    <div class="bg-gray-800 rounded-lg shadow border border-gray-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-700">
            <h3 class="text-lg font-semibold text-white">Recent Payments</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-gray-300">
                <thead class="bg-gray-900 text-gray-400 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">User</th>
                        <th class="px-6 py-3">Amount</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($stats['recent_payments'] as $payment)
                    <tr>
                        <td class="px-6 py-4">{{ $payment->user->name }}</td>
                        <td class="px-6 py-4">${{ number_format($payment->amount, 2) }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded {{ $payment->status == 'succeeded' ? 'bg-green-900 text-green-300' : 'bg-red-900 text-red-300' }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">{{ $payment->created_at->format('M d, Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No payments yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.admin>
