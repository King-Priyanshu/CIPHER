<x-layouts.subscriber>
    <x-slot:title>
        Payment History
    </x-slot:title>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="card p-6">
            <p class="text-sm font-medium text-slate-500">Total Paid</p>
            <p class="text-3xl font-bold text-navy mt-1">₹{{ number_format($stats['total_amount'], 0) }}</p>
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
            <p class="text-sm font-medium text-slate-500">Pending</p>
            <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $stats['pending'] }}</p>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-white">
            <h3 class="text-lg font-bold text-navy">All Payments</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Gateway</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Plan</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-slate-50/50 transition">
                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ $payment->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-navy">
                                ₹{{ number_format($payment->amount, 2) }}
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 capitalize">
                                {{ $payment->gateway ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($payment->status === 'succeeded')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        Successful
                                    </span>
                                @elseif($payment->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                        Pending
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ $payment->subscription?->plan?->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('subscriber.payments.show', $payment) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold hover:underline">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                </div>
                                <p>No payment history available.</p>
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
</x-layouts.subscriber>
