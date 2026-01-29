<x-layouts.subscriber>
    <x-slot:title>
        Checkout
    </x-slot:title>

    <div class="max-w-4xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            
            <!-- Plan Summary -->
            <div class="md:col-span-1">
                <div class="card sticky top-6">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-navy">Order Summary</h3>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-100">
                            <div>
                                <p class="font-bold text-xl text-navy">{{ $plan->name }}</p>
                                <p class="text-sm text-slate-500 capitalize">{{ $plan->interval }} Plan</p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-xl text-teal-600">₹{{ number_format($plan->price * 83, 0) }}</p>
                            </div>
                        </div>
                        
                        <div class="flex justify-between items-center text-lg font-bold text-navy">
                            <span>Total</span>
                            <span class="text-teal-600">₹{{ number_format($plan->price * 83, 0) }}</span>
                        </div>

                        <p class="text-xs text-slate-400 mt-4">
                            * Recurring subscription. Cancel anytime.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="md:col-span-2">
                <div class="card">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-navy">Billing Information</h3>
                    </div>
                    <div class="p-6">

                        @if (session('error'))
                            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl" role="alert">
                                <strong class="font-bold">Error!</strong>
                                <span class="block sm:inline">{{ session('error') }}</span>
                            </div>
                        @endif

                        <div id="error-message" class="hidden mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl"></div>

                        <!-- Razorpay Info -->
                        <div class="flex items-center p-4 bg-teal-50 border border-teal-200 rounded-xl mb-6">
                            <div class="w-10 h-10 bg-teal-100 rounded-full flex items-center justify-center mr-4">
                                <svg class="w-5 h-5 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-semibold text-navy">Secure Payment via Razorpay</p>
                                <p class="text-sm text-slate-500">UPI, Credit/Debit Card, Netbanking, Wallets</p>
                            </div>
                        </div>

                        <form id="checkout-form">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label for="billing_name" class="block text-sm font-semibold text-navy mb-1.5">Full Name</label>
                                    <input type="text" id="billing_name" name="billing_name" value="{{ auth()->user()->name }}" required 
                                           class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-navy placeholder-slate-400">
                                </div>

                                <div>
                                    <label for="billing_email" class="block text-sm font-semibold text-navy mb-1.5">Email Address</label>
                                    <input type="email" id="billing_email" name="billing_email" value="{{ auth()->user()->email }}" required 
                                           class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-navy placeholder-slate-400">
                                </div>

                                <div>
                                    <label for="billing_phone" class="block text-sm font-semibold text-navy mb-1.5">Phone Number</label>
                                    <input type="tel" id="billing_phone" name="billing_phone" placeholder="+91 9876543210" required 
                                           value="{{ auth()->user()->phone ?? '' }}"
                                           class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-navy placeholder-slate-400">
                                </div>
                            </div>
                        </form>

                        <button id="pay-button" type="button" class="w-full mt-6 py-4 bg-navy hover:bg-slate-800 text-white text-center font-bold rounded-xl transition shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                            Subscribe for ₹{{ number_format($plan->price * 83, 0) }}/{{ $plan->interval }}
                        </button>

                        <p class="text-center text-xs text-slate-400 mt-4">
                            By subscribing, you agree to our Terms of Service and Privacy Policy.
                            Your subscription will auto-renew {{ $plan->interval }}ly.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const payButton = document.getElementById('pay-button');
            const errorDiv = document.getElementById('error-message');

            function showError(message) {
                errorDiv.textContent = message;
                errorDiv.classList.remove('hidden');
            }

            function hideError() {
                errorDiv.classList.add('hidden');
            }

            payButton.onclick = async function(e) {
                e.preventDefault();
                hideError();

                // Validate form
                const name = document.getElementById('billing_name').value;
                const email = document.getElementById('billing_email').value;
                const phone = document.getElementById('billing_phone').value;

                if (!name || !email || !phone) {
                    showError('Please fill in all billing details.');
                    return;
                }

                // Disable button
                payButton.disabled = true;
                payButton.textContent = 'Processing...';

                try {
                    // Step 1: Create subscription on backend
                    const response = await fetch('{{ route("checkout.create", $plan->slug) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify({
                            billing_name: name,
                            billing_email: email,
                            billing_phone: phone,
                        }),
                    });

                    const data = await response.json();

                    if (!data.success) {
                        showError(data.message || 'Failed to create subscription.');
                        payButton.disabled = false;
                        payButton.textContent = 'Subscribe for ₹{{ number_format($plan->price * 83, 0) }}/{{ $plan->interval }}';
                        return;
                    }

                    // Step 2: Open Razorpay checkout with subscription
                    const options = {
                        key: data.key_id,
                        subscription_id: data.subscription_id,
                        name: 'CIPHER',
                        description: '{{ $plan->name }} Subscription',
                        prefill: {
                            name: name,
                            email: email,
                            contact: phone,
                        },
                        notes: {
                            plan_name: '{{ $plan->name }}',
                        },
                        theme: {
                            color: '#00BFA6',
                        },
                        handler: function(response) {
                            // Payment successful - redirect to processing page
                            window.location.href = '{{ route("checkout.success") }}?subscription_id=' + data.subscription_id;
                        },
                        modal: {
                            ondismiss: function() {
                                payButton.disabled = false;
                                payButton.textContent = 'Subscribe for ₹{{ number_format($plan->price * 83, 0) }}/{{ $plan->interval }}';
                            }
                        }
                    };

                    const rzp = new Razorpay(options);

                    rzp.on('payment.failed', function(response) {
                        showError('Payment failed: ' + response.error.description);
                        payButton.disabled = false;
                        payButton.textContent = 'Subscribe for ₹{{ number_format($plan->price * 83, 0) }}/{{ $plan->interval }}';
                    });

                    rzp.open();

                } catch (error) {
                    console.error('Checkout error:', error);
                    showError('An error occurred. Please try again.');
                    payButton.disabled = false;
                    payButton.textContent = 'Subscribe for ₹{{ number_format($plan->price * 83, 0) }}/{{ $plan->interval }}';
                }
            };
        });
    </script>
    @endpush
</x-layouts.subscriber>
