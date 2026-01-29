<x-layouts.admin>
    <x-slot:title>Invoice {{ $invoice->invoice_number }}</x-slot:title>

    <div class="max-w-3xl mx-auto">
        <div class="card">
            <!-- Invoice Header -->
            <div class="p-8 border-b border-gray-100">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-bold text-navy">INVOICE</h1>
                        <p class="text-lg font-mono text-teal-600 mt-1">{{ $invoice->invoice_number }}</p>
                    </div>
                    <div class="text-right">
                        <h2 class="text-xl font-bold text-navy">CIPHER</h2>
                        <p class="text-sm text-slate-500 mt-1">Community Investment Platform</p>
                    </div>
                </div>
            </div>

            <!-- Invoice Details -->
            <div class="p-8">
                <div class="grid grid-cols-2 gap-8 mb-8">
                    <div>
                        <h3 class="text-xs font-bold text-slate-500 uppercase mb-2">Bill To</h3>
                        <p class="font-semibold text-navy">{{ $invoice->user->name ?? 'Unknown' }}</p>
                        <p class="text-sm text-slate-500">{{ $invoice->user->email ?? '' }}</p>
                    </div>
                    <div class="text-right">
                        <div class="mb-3">
                            <span class="text-xs font-bold text-slate-500 uppercase">Invoice Date</span>
                            <p class="text-sm text-navy">{{ $invoice->issued_at?->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-slate-500 uppercase">Status</span>
                            <span class="inline-flex ml-2 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ ucfirst($invoice->status ?? 'paid') }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Items -->
                <table class="w-full mb-8">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="py-3 text-left text-xs font-bold text-slate-500 uppercase">Description</th>
                            <th class="py-3 text-right text-xs font-bold text-slate-500 uppercase">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-100">
                            <td class="py-4 text-navy">Subscription Payment</td>
                            <td class="py-4 text-right text-navy">₹{{ number_format($invoice->amount, 2) }}</td>
                        </tr>
                        @if($invoice->tax > 0)
                        <tr class="border-b border-gray-100">
                            <td class="py-4 text-slate-500">Tax</td>
                            <td class="py-4 text-right text-slate-500">₹{{ number_format($invoice->tax, 2) }}</td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="py-4 text-lg font-bold text-navy">Total</td>
                            <td class="py-4 text-right text-lg font-bold text-navy">₹{{ number_format($invoice->total, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>

                <!-- Payment Info -->
                @if($invoice->payment)
                <div class="bg-slate-50 rounded-xl p-4 text-sm">
                    <p class="font-medium text-slate-600">Payment Reference: {{ $invoice->payment->gateway_transaction_id ?? 'N/A' }}</p>
                    <p class="text-slate-500">Payment Gateway: {{ ucfirst($invoice->payment->gateway ?? 'N/A') }}</p>
                </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="px-8 py-4 bg-slate-50 border-t border-gray-100 flex gap-4">
                <a href="{{ route('admin.invoices.download', $invoice) }}" target="_blank" class="px-4 py-2 bg-navy text-white rounded-lg font-medium hover:bg-slate-800 transition">
                    Download PDF
                </a>
                <a href="{{ route('admin.invoices.index') }}" class="px-4 py-2 bg-gray-100 text-slate-700 rounded-lg font-medium hover:bg-gray-200 transition">
                    Back to Invoices
                </a>
            </div>
        </div>
    </div>
</x-layouts.admin>
