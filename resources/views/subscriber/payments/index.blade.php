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

    <!-- Filter and Search Section -->
    <div class="card p-6 mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex-1">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400" width="18"
                        height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" placeholder="Search payments by amount, plan, or gateway..."
                        class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition"
                        id="paymentSearch" />
                </div>
            </div>
            <div class="flex gap-2">
                <select
                    class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition"
                    id="statusFilter">
                    <option value="">All Status</option>
                    <option value="succeeded">Successful</option>
                    <option value="pending">Pending</option>
                    <option value="failed">Failed</option>
                </select>
                <select
                    class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition"
                    id="gatewayFilter">
                    <option value="">All Gateways</option>
                    <option value="stripe">Stripe</option>
                    <option value="razorpay">Razorpay</option>
                    <option value="wallet">Wallet</option>
                </select>
            </div>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-white flex items-center justify-between">
            <h3 class="text-lg font-bold text-navy">All Payments</h3>
            <div class="text-sm text-slate-500">
                Showing {{ $payments->count() }} payments
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Date
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Amount
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                            Gateway</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Plan
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white" id="paymentsTable">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-slate-50/50 transition payment-row" data-amount="{{ $payment->amount }}"
                            data-plan="{{ $payment->subscription?->plan?->name ?? 'N/A' }}"
                            data-gateway="{{ $payment->gateway ?? 'N/A' }}" data-status="{{ $payment->status }}">
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
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        Successful
                                    </span>
                                @elseif($payment->status === 'pending')
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                        Pending
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ $payment->subscription?->plan?->name ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('subscriber.payments.show', $payment) }}"
                                    class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold hover:underline">
                                    View Details
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <div
                                    class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-slate-300" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('paymentSearch');
            const statusFilter = document.getElementById('statusFilter');
            const gatewayFilter = document.getElementById('gatewayFilter');
            const paymentRows = document.querySelectorAll('.payment-row');

            // Search functionality
            searchInput.addEventListener('input', function () {
                const searchTerm = this.value.toLowerCase();

                paymentRows.forEach(row => {
                    const amount = row.dataset.amount.toLowerCase();
                    const plan = row.dataset.plan.toLowerCase();
                    const gateway = row.dataset.gateway.toLowerCase();

                    if (amount.includes(searchTerm) || plan.includes(searchTerm) || gateway.includes(searchTerm)) {
                        row.style.display = '';
                        row.style.animation = 'fadeIn 0.3s ease-out';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            // Status filter
            statusFilter.addEventListener('change', function () {
                const status = this.value;

                paymentRows.forEach(row => {
                    if (status === '' || row.dataset.status === status) {
                        row.style.display = '';
                        row.style.animation = 'fadeIn 0.3s ease-out';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            // Gateway filter
            gatewayFilter.addEventListener('change', function () {
                const gateway = this.value;

                paymentRows.forEach(row => {
                    if (gateway === '' || row.dataset.gateway === gateway) {
                        row.style.display = '';
                        row.style.animation = 'fadeIn 0.3s ease-out';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    </script>
</x-layouts.subscriber>