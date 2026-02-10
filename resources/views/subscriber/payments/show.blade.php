<x-layouts.subscriber>
    <x-slot:title>
        Payment Details
    </x-slot:title>

    <div class="max-w-2xl mx-auto">
        <div class="card">
            <div class="px-6 py-5 border-b border-gray-100 bg-white">
                <h3 class="text-lg font-bold text-navy">Payment #{{ $payment->id }}</h3>
            </div>
            
            <div class="p-6 space-y-6">
                <!-- Status Banner -->
                <div class="rounded-xl p-4 {{ $payment->status === 'succeeded' ? 'bg-green-50 border border-green-200' : ($payment->status === 'pending' ? 'bg-yellow-50 border border-yellow-200' : 'bg-red-50 border border-red-200') }}">
                    <div class="flex items-center gap-3">
                        @if($payment->status === 'succeeded')
                            <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span class="font-semibold text-green-800">Payment Successful</span>
                        @elseif($payment->status === 'pending')
                            <svg class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="font-semibold text-yellow-800">Payment Pending</span>
                        @else
                            <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            <span class="font-semibold text-red-800">Payment Failed</span>
                        @endif
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-sm text-slate-500">Amount</span>
                        <span class="font-semibold text-navy">₹{{ number_format($payment->amount - ($payment->invoice->tax ?? 0), 2) }}</span>
                    </div>
                    @if($payment->invoice && $payment->invoice->tax > 0)
                    <div class="flex justify-between items-center py-2 border-b border-gray-50">
                        <span class="text-sm text-slate-500">Tax</span>
                        <span class="font-semibold text-navy">₹{{ number_format($payment->invoice->tax, 2) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between items-center py-2">
                        <span class="text-base font-bold text-navy">Total Paid</span>
                        <span class="text-2xl font-bold text-navy">₹{{ number_format($payment->amount, 2) }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gray-100">
                    <div>
                        <p class="text-sm text-slate-500">Gateway</p>
                        <p class="text-lg font-semibold text-navy capitalize">{{ $payment->gateway ?? 'N/A' }}</p>
                    </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-slate-500">Date</p>
                        <p class="text-base text-navy">{{ $payment->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Transaction ID</p>
                        <p class="text-base font-mono text-slate-600">{{ $payment->gateway_transaction_id ?? 'N/A' }}</p>
                    </div>
                </div>

                @if($payment->subscription?->plan)
                <div class="pt-4 border-t border-gray-100">
                    <p class="text-sm text-slate-500 mb-2">Subscription Plan</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                            {{ strtoupper(substr($payment->subscription->plan->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-semibold text-navy">{{ $payment->subscription->plan->name }}</p>
                            <p class="text-sm text-slate-500">{{ $payment->subscription->plan->interval ?? 'Monthly' }} plan</p>
                        </div>
                    </div>
                </div>
                @endif

                @if($payment->invoice)
                <div class="pt-4 border-t border-gray-100">
                    <a href="{{ route('subscriber.invoices.download', $payment->invoice) }}" target="_blank" class="btn-primary inline-flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Download Invoice
                    </a>
                </div>
                @endif
            </div>

            <div class="px-6 py-4 border-t border-gray-100 bg-slate-50">
                <a href="{{ route('subscriber.payments.index') }}" class="text-indigo-600 hover:underline text-sm font-medium">
                    ← Back to Payment History
                </a>
            </div>
        </div>
    </div>
</x-layouts.subscriber>
