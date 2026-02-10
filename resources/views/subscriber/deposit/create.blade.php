@extends('components.layouts.subscriber')

@section('title', 'Add Funds')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <div class="mb-6">
        <a href="{{ route('subscriber.dashboard') }}" class="text-slate-500 hover:text-navy text-sm flex items-center gap-1 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Dashboard
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-100">
        <div class="bg-gradient-to-r from-navy to-indigo-900 p-8 text-white">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white/10 flex items-center justify-center backdrop-blur-sm">
                    <svg class="w-6 h-6 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold">Add Funds to Wallet</h1>
                    <p class="text-indigo-200 text-sm mt-1">Securely deposit money for your investments.</p>
                </div>
            </div>
        </div>

        <div class="p-8">
            @if(session('error'))
                <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-lg border border-red-100 flex items-start gap-3">
                    <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="font-bold">Payment Error</p>
                        <p class="text-sm">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <form id="deposit-form">
                @csrf
                <div class="space-y-6">
                    <div>
                        <label for="amount" class="block text-sm font-bold text-navy mb-2">Amount to Deposit (₹)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <span class="text-slate-400 font-bold">₹</span>
                            </div>
                            <input type="number" name="amount" id="amount" 
                                class="w-full pl-10 pr-4 py-4 rounded-xl border border-slate-200 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all font-mono text-xl font-bold text-navy placeholder-slate-300" 
                                placeholder="0.00" min="100" max="500000" step="100" required autofocus>
                        </div>
                        <p class="text-xs text-slate-500 mt-2">Minimum deposit: ₹100. Maximum: ₹5,00,000.</p>
                    </div>

                    <!-- Preset Buttons -->
                    <div class="grid grid-cols-4 gap-3">
                        <button type="button" class="preset-btn py-2 px-3 border border-slate-200 rounded-lg hover:border-indigo-500 hover:text-indigo-600 hover:bg-indigo-50 transition-all font-medium text-slate-600" data-value="1000">₹1,000</button>
                        <button type="button" class="preset-btn py-2 px-3 border border-slate-200 rounded-lg hover:border-indigo-500 hover:text-indigo-600 hover:bg-indigo-50 transition-all font-medium text-slate-600" data-value="5000">₹5,000</button>
                        <button type="button" class="preset-btn py-2 px-3 border border-slate-200 rounded-lg hover:border-indigo-500 hover:text-indigo-600 hover:bg-indigo-50 transition-all font-medium text-slate-600" data-value="10000">₹10,000</button>
                        <button type="button" class="preset-btn py-2 px-3 border border-slate-200 rounded-lg hover:border-indigo-500 hover:text-indigo-600 hover:bg-indigo-50 transition-all font-medium text-slate-600" data-value="50000">₹50,000</button>
                    </div>

                    <button type="submit" id="pay-btn" class="w-full py-4 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-500/20 transition-all transform hover:scale-[1.01] active:scale-[0.99] flex items-center justify-center gap-2">
                        <span>Proceed to Pay</span>
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </button>
                </div>
            </form>
            
            <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                 <div class="flex items-center justify-center gap-2 text-slate-400 text-xs mb-2">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9v-2h2v2zm0-4H9V7h2v5z"/></svg>
                    <span>Secured by Razorpay. Your data is encrypted.</span>
                 </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden form for verification -->
<form id="verification-form" action="{{ route('subscriber.deposit.verify') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
    <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
    <input type="hidden" name="razorpay_signature" id="razorpay_signature">
</form>

@endsection

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Preset buttons
        document.querySelectorAll('.preset-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                document.getElementById('amount').value = btn.dataset.value;
            });
        });

        const form = document.getElementById('deposit-form');
        const payBtn = document.getElementById('pay-btn');

        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            payBtn.disabled = true;
            payBtn.innerHTML = '<span class="animate-spin inline-block w-5 h-5 border-2 border-white/30 border-t-white rounded-full mr-2"></span> Processing...';

            const amount = document.getElementById('amount').value;
            const token = document.querySelector('input[name="_token"]').value;

            try {
                // 1. Create Order
                const response = await fetch("{{ route('subscriber.deposit.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({ amount: amount })
                });

                const data = await response.json();

                if (!data.success) {
                    alert('Error: ' + data.message);
                    resetBtn();
                    return;
                }

                // 2. Open Razorpay Checkout
                var options = {
                    "key": data.key_id,
                    "amount": data.amount,
                    "currency": "INR",
                    "name": data.name,
                    "description": data.description,
                    "order_id": data.order_id,
                    "handler": function (response){
                        // 3. Verify Payment
                        document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id;
                        document.getElementById('razorpay_order_id').value = response.razorpay_order_id;
                        document.getElementById('razorpay_signature').value = response.razorpay_signature;
                        
                        document.getElementById('verification-form').submit();
                    },
                    "prefill": data.prefill,
                    "notes": data.notes,
                    "theme": {
                        "color": "#10B981" // Emerald-500
                    },
                    "modal": {
                        "ondismiss": function(){
                            resetBtn();
                        }
                    }
                };
                
                var rzp1 = new Razorpay(options);
                rzp1.open();
                
                rzp1.on('payment.failed', function (response){
                    alert('Payment Failed: ' + response.error.description);
                    resetBtn();
                });

            } catch (error) {
                console.error(error);
                alert('An unexpected error occurred.');
                resetBtn();
            }
        });

        function resetBtn() {
            payBtn.disabled = false;
            payBtn.innerHTML = `<span>Proceed to Pay</span><svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>`;
        }
    });
</script>
@endpush
