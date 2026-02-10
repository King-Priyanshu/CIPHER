<x-layouts.subscriber>
    <x-slot:title>
        Complete Your Investment
    </x-slot:title>

    <div class="max-w-2xl mx-auto py-8 px-4">
        <div class="card overflow-hidden">
            <div class="bg-navy p-6 text-white text-center">
                <div
                    class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center mx-auto mb-4 border border-white/20">
                    <svg class="w-8 h-8 text-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-2xl font-bold">Secure Checkout</h2>
                <p class="text-slate-300">Complete your investment securely</p>
            </div>

            <div class="p-8 space-y-6">
                {{-- Investment Summary --}}
                <div class="space-y-4">
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider">Investment Summary</h3>

                    <div class="bg-slate-50 rounded-2xl p-5 space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500">Allocation Type</span>
                            <span class="font-bold text-navy capitalize">{{ $investment->allocation_type }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500">Project / Portfolio</span>
                            <span
                                class="font-bold text-navy">{{ $investment->project->title ?? 'Automatic Portfolio' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-slate-500">Plan</span>
                            <span class="font-bold text-navy">{{ $investment->investmentPlan->name ?? 'N/A' }}</span>
                        </div>
                        <div class="pt-3 border-t border-slate-200 flex justify-between items-center">
                            <span class="text-lg font-bold text-navy">Total Payable</span>
                            <span
                                class="text-2xl font-bold text-teal">₹{{ number_format($investment->amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Gateway Selection --}}
                <div>
                    <h3 class="text-sm font-bold text-slate-400 uppercase tracking-wider mb-3">Payment Method</h3>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="relative cursor-pointer">
                            <input type="radio" name="pay_gateway" value="razorpay" checked class="sr-only peer">
                            <div
                                class="p-4 border-2 rounded-xl text-center transition-all peer-checked:border-teal-500 peer-checked:bg-teal-50 hover:border-slate-300">
                                <div class="font-bold text-navy text-sm">Razorpay</div>
                                <p class="text-xs text-slate-500 mt-1">UPI, Cards, Netbanking</p>
                            </div>
                        </label>
                        <label class="relative cursor-pointer">
                            <input type="radio" name="pay_gateway" value="stripe" class="sr-only peer">
                            <div
                                class="p-4 border-2 rounded-xl text-center transition-all peer-checked:border-teal-500 peer-checked:bg-teal-50 hover:border-slate-300">
                                <div class="font-bold text-navy text-sm">Stripe</div>
                                <p class="text-xs text-slate-500 mt-1">International Cards</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Payment Button --}}
                <div class="pt-4">
                    <div id="pay-error"
                        class="hidden mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                    </div>

                    <button id="pay-button" type="button"
                        class="w-full bg-navy hover:bg-slate-800 text-white font-bold py-4 rounded-2xl shadow-xl transition-all flex items-center justify-center gap-3">
                        <span>Pay ₹{{ number_format($investment->amount, 2) }}</span>
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 8l4 4m0 0l-4 4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>

                    <div class="mt-6 flex items-center justify-center gap-4 grayscale opacity-50">
                        <span class="text-xs text-slate-400">Secured by</span>
                        <div class="flex gap-2">
                            <div class="w-8 h-5 bg-navy/20 rounded-sm"></div>
                            <div class="w-8 h-5 bg-navy/20 rounded-sm"></div>
                            <div class="w-8 h-5 bg-navy/20 rounded-sm"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <p class="text-center text-slate-400 text-xs mt-8">
            Your payment is secure and encrypted. By proceeding, you agree to our Terms of Service and Investment
            Policies.
        </p>
    </div>

    @push('scripts')
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
        <script src="https://js.stripe.com/v3/"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const payButton = document.getElementById('pay-button');
                const errorDiv = document.getElementById('pay-error');
                const defaultButtonText = payButton.innerHTML;

                function getSelectedGateway() {
                    const checked = document.querySelector('input[name="pay_gateway"]:checked');
                    return checked ? checked.value : 'razorpay';
                }

                function showError(msg) {
                    errorDiv.textContent = msg;
                    errorDiv.classList.remove('hidden');
                }

                function resetButton() {
                    payButton.disabled = false;
                    payButton.innerHTML = defaultButtonText;
                }

                function setLoading() {
                    payButton.disabled = true;
                    payButton.innerHTML = '<svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Processing...';
                }

                payButton.onclick = async function () {
                    errorDiv.classList.add('hidden');
                    const gateway = getSelectedGateway();
                    setLoading();

                    try {
                        const response = await fetch('{{ route("checkout.investment.create", $investment) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ gateway: gateway })
                        });

                        const data = await response.json();

                        if (!data.success) {
                            showError(data.message || 'Failed to create payment.');
                            resetButton();
                            return;
                        }

                        if (gateway === 'stripe') {
                            handleStripePayment(data);
                        } else {
                            handleRazorpayPayment(data);
                        }
                    } catch (err) {
                        console.error('Payment error:', err);
                        showError('An error occurred. Please try again.');
                        resetButton();
                    }
                };

                function handleRazorpayPayment(data) {
                    const options = {
                        key: data.key_id,
                        amount: data.amount,
                        currency: 'INR',
                        name: data.name,
                        description: data.description,
                        order_id: data.order_id,
                        prefill: data.prefill,
                        notes: data.notes,
                        theme: { color: '#00BFA6' },
                        handler: function (response) {
                            const params = new URLSearchParams({
                                razorpay_payment_id: response.razorpay_payment_id,
                                razorpay_order_id: response.razorpay_order_id,
                                razorpay_signature: response.razorpay_signature,
                                gateway: 'razorpay'
                            });
                            window.location.href = '{{ route("checkout.success") }}?' + params.toString();
                        },
                        modal: {
                            ondismiss: function () { resetButton(); }
                        }
                    };

                    const rzp = new Razorpay(options);
                    rzp.on('payment.failed', function (response) {
                        showError('Payment failed: ' + response.error.description);
                        recordFailure('razorpay', data.order_id, response.error);
                        resetButton();
                    });
                    rzp.open();
                }

                function handleStripePayment(data) {
                    const stripe = Stripe(data.key_id);
                    stripe.confirmCardPayment(data.client_secret).then(function (result) {
                        if (result.error) {
                            showError('Payment failed: ' + result.error.message);
                            recordFailure('stripe', data.payment_intent_id, result.error);
                            resetButton();
                        } else if (result.paymentIntent.status === 'succeeded') {
                            const params = new URLSearchParams({
                                payment_intent: result.paymentIntent.id,
                                redirect_status: 'succeeded',
                                gateway: 'stripe'
                            });
                            window.location.href = '{{ route("checkout.success") }}?' + params.toString();
                        }
                    });
                }

                async function recordFailure(gateway, orderId, errorData) {
                    try {
                        await fetch('{{ route("checkout.status") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                gateway: gateway,
                                order_id: orderId,
                                status: 'failed',
                                error_code: errorData?.code || errorData?.type || 'unknown',
                                error_description: errorData?.description || errorData?.message || 'Payment failed'
                            })
                        });
                    } catch (e) {
                        console.error('Failed to record failure:', e);
                    }
                }
            });
        </script>
    @endpush
</x-layouts.subscriber>