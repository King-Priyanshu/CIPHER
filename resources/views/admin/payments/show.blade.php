<x-layouts.admin>
    <x-slot:title>Payment Details</x-slot:title>

    <div class="max-w-3xl mx-auto">
        <div class="card">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-navy">Payment #{{ $payment->id }}</h2>
                    <p class="text-sm text-slate-500">{{ $payment->created_at->format('M d, Y H:i') }}</p>
                </div>
                <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium
                    {{ $payment->status === 'succeeded' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $payment->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $payment->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                    {{ ucfirst($payment->status) }}
                </span>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-slate-500">Amount</p>
                        <p class="text-2xl font-bold text-navy">₹{{ number_format($payment->amount, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Currency</p>
                        <p class="text-lg font-semibold text-navy">INR</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Gateway</p>
                        <p class="text-lg font-semibold text-navy capitalize">{{ $payment->gateway ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-slate-500">Transaction ID</p>
                        <p class="text-sm font-mono text-navy">{{ $payment->gateway_transaction_id ?? 'N/A' }}</p>
                    </div>
                </div>

                <hr class="my-6">

                <h3 class="text-sm font-bold text-slate-500 mb-3">USER DETAILS</h3>
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold">
                        {{ substr($payment->user->name ?? 'U', 0, 1) }}
                    </div>
                    <div class="ml-3">
                        <p class="font-semibold text-navy">{{ $payment->user->name ?? 'Unknown' }}</p>
                        <p class="text-sm text-slate-500">{{ $payment->user->email ?? '' }}</p>
                    </div>
                </div>

                @if($payment->invoice)
                <hr class="my-6">
                <h3 class="text-sm font-bold text-slate-500 mb-3">INVOICE</h3>
                <a href="{{ route('admin.invoices.show', $payment->invoice) }}" class="inline-flex items-center gap-2 text-teal-600 hover:underline font-medium">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    {{ $payment->invoice->invoice_number }}
                </a>
                @else
                <hr class="my-6">
                <form action="{{ route('admin.invoices.generate', $payment) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-teal-600 text-white rounded-lg font-medium hover:bg-teal-700 transition">
                        Generate Invoice
                    </button>
                </form>
                @endif
            </div>
        </div>

        <div class="mt-4">
            <a href="{{ route('admin.payments.index') }}" class="text-slate-500 hover:text-slate-700 text-sm">← Back to Payments</a>
        </div>
    </div>
</x-layouts.admin>
