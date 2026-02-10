<x-layouts.subscriber>
    <x-slot:title>
        Billing History
    </x-slot:title>

    <!-- Billing Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="card p-6">
            <p class="text-sm font-medium text-slate-500">Total Invoices</p>
            <p class="text-3xl font-bold text-navy mt-1">{{ $invoices->total() }}</p>
        </div>
        <div class="card p-6">
            <p class="text-sm font-medium text-slate-500">Total Amount</p>
            <p class="text-3xl font-bold text-navy mt-1">₹{{ number_format($totalAmount, 0) }}</p>
        </div>
        <div class="card p-6">
            <p class="text-sm font-medium text-slate-500">Paid Invoices</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ $paidInvoices }}</p>
        </div>
        <div class="card p-6">
            <p class="text-sm font-medium text-slate-500">Pending Invoices</p>
            <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $pendingInvoices }}</p>
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
                    <input type="text" placeholder="Search invoices by description or amount..."
                        class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition"
                        id="invoiceSearch" />
                </div>
            </div>
            <div class="flex gap-2">
                <select
                    class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition"
                    id="statusFilter">
                    <option value="">All Status</option>
                    <option value="paid">Paid</option>
                    <option value="pending">Pending</option>
                    <option value="failed">Failed</option>
                </select>
            </div>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 bg-white flex items-center justify-between">
            <h3 class="text-lg font-bold text-navy">Invoices & Payments</h3>
            <div class="text-sm text-slate-500">
                Showing {{ $invoices->count() }} invoices
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Date
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                            Description</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Amount
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status
                        </th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">
                            Invoice</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white" id="invoicesTable">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-slate-50/50 transition invoice-row"
                            data-description="{{ $invoice->payment->description ?? 'Subscription Payment' }}"
                            data-amount="{{ $invoice->amount }}" data-status="{{ $invoice->status }}">
                            <td class="px-6 py-4 text-sm text-slate-600">
                                {{ $invoice->issued_at->toFormattedDateString() }}
                            </td>
                            <td class="px-6 py-4 text-sm text-navy font-medium">
                                {{ $invoice->payment->description ?? 'Subscription Payment' }}
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-navy">
                                ₹{{ number_format($invoice->amount, 2) }}
                            </td>
                            <td class="px-6 py-4">
                                @if($invoice->status == 'paid')
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                        Paid
                                    </span>
                                @elseif($invoice->status == 'pending')
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                        Pending
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('subscriber.invoices.download', $invoice) }}"
                                    class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold hover:underline">
                                    Download PDF
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                <div
                                    class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6 text-slate-300" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <p>No billing history available.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($invoices->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('invoiceSearch');
            const statusFilter = document.getElementById('statusFilter');
            const invoiceRows = document.querySelectorAll('.invoice-row');

            // Search functionality
            searchInput.addEventListener('input', function () {
                const searchTerm = this.value.toLowerCase();

                invoiceRows.forEach(row => {
                    const description = row.dataset.description.toLowerCase();
                    const amount = row.dataset.amount.toLowerCase();

                    if (description.includes(searchTerm) || amount.includes(searchTerm)) {
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

                invoiceRows.forEach(row => {
                    if (status === '' || row.dataset.status === status) {
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