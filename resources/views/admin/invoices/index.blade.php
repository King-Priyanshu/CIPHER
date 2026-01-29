<x-layouts.admin>
    <x-slot:title>Invoices</x-slot:title>

    <!-- Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="card p-6">
            <p class="text-sm font-medium text-slate-500">Total Invoiced</p>
            <p class="text-3xl font-bold text-navy mt-1">₹{{ number_format($stats['total_invoiced'], 0) }}</p>
        </div>
        <div class="card p-6">
            <p class="text-sm font-medium text-slate-500">Paid Invoices</p>
            <p class="text-3xl font-bold text-green-600 mt-1">{{ $stats['paid_invoices'] }}</p>
        </div>
        <div class="card p-6">
            <p class="text-sm font-medium text-slate-500">Pending</p>
            <p class="text-3xl font-bold text-yellow-600 mt-1">{{ $stats['pending_invoices'] }}</p>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="card overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-navy">All Invoices</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Invoice #</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">User</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Amount</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Issued</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($invoices as $invoice)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-6 py-4 text-sm font-mono font-semibold text-navy">{{ $invoice->invoice_number }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-sm">
                                    {{ substr($invoice->user->name ?? 'U', 0, 1) }}
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-semibold text-navy">{{ $invoice->user->name ?? 'Unknown' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-navy">₹{{ number_format($invoice->total, 2) }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ ($invoice->status ?? 'paid') === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ ucfirst($invoice->status ?? 'paid') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $invoice->issued_at?->format('M d, Y') }}</td>
                        <td class="px-6 py-4 flex gap-2">
                            <a href="{{ route('admin.invoices.show', $invoice) }}" class="text-teal-600 hover:underline text-sm font-medium">View</a>
                            <a href="{{ route('admin.invoices.download', $invoice) }}" class="text-blue-600 hover:underline text-sm font-medium" target="_blank">Download</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                            No invoices yet.
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
</x-layouts.admin>
