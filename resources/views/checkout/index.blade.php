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
                                @if(isset($project))
                                    <p class="text-xs text-teal-600 font-semibold mt-1">Project: {{ $project->title }}</p>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-xl text-teal-600">₹{{ number_format($plan->price, 0) }}</p>
                            </div>
                        </div>

                        <div class="flex justify-between items-center text-lg font-bold text-navy">
                            <span>Total</span>
                            <span class="text-teal-600">₹{{ number_format($plan->price, 0) }}</span>
                        </div>

                        <p class="text-xs text-slate-400 mt-4">
                            * One-time payment. No auto-renewal.
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
                            <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl"
                                role="alert">
                                <strong class="font-bold">Error!</strong>
                                <span class="block sm:inline">{{ session('error') }}</span>
                            </div>
                        @endif

                        <div id="error-message"
                            class="hidden mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl"></div>
                        <div id="success-message"
                            class="hidden mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
                        </div>

                        <!-- Gateway Selection -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-navy mb-3">Payment Method</label>
                            <div class="grid grid-cols-{{ count($enabledGateways) }} gap-3">
                                @if(in_array('razorpay', $enabledGateways))
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="gateway" value="razorpay" {{ $enabledGateways[0] === 'razorpay' ? 'checked' : '' }} class="sr-only peer">
                                        <div
                                            class="p-4 border-2 rounded-xl text-center transition-all peer-checked:border-teal-500 peer-checked:bg-teal-50 hover:border-slate-300">
                                            <div class="font-bold text-navy text-sm">Razorpay</div>
                                            <p class="text-xs text-slate-500 mt-1">UPI, Cards, Netbanking</p>
                                        </div>
                                    </label>
                                @endif
                                @if(in_array('stripe', $enabledGateways))
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="gateway" value="stripe" {{ $enabledGateways[0] === 'stripe' ? 'checked' : '' }} class="sr-only peer">
                                        <div
                                            class="p-4 border-2 rounded-xl text-center transition-all peer-checked:border-teal-500 peer-checked:bg-teal-50 hover:border-slate-300">
                                            <div class="font-bold text-navy text-sm">Stripe</div>
                                            <p class="text-xs text-slate-500 mt-1">International Cards</p>
                                        </div>
                                    </label>
                                @endif
                                @if(in_array('test_payment', $enabledGateways))
                                    <label class="relative cursor-pointer">
                                        <input type="radio" name="gateway" value="test_payment" {{ $enabledGateways[0] === 'test_payment' ? 'checked' : '' }} class="sr-only peer">
                                        <div
                                            class="p-4 border-2 rounded-xl text-center transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50 hover:border-slate-300">
                                            <div class="font-bold text-emerald-700 text-sm">Test Payment</div>
                                            <p class="text-xs text-slate-500 mt-1">Instant Success</p>
                                        </div>
                                    </label>
                                @endif
                            </div>
                        </div>

                        <form id="checkout-form">
                            @csrf
                            @if(isset($project))
                                <input type="hidden" id="project_id" name="project_id" value="{{ $project->id }}">
                            @endif
                            <div class="space-y-4">
                                <div>
                                    <label for="billing_name" class="block text-sm font-semibold text-navy mb-1.5">Full
                                        Name</label>
                                    <input type="text" id="billing_name" name="billing_name"
                                        value="{{ auth()->user()->name }}" required
                                        class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-navy placeholder-slate-400">
                                </div>

                                <div>
                                    <label for="billing_email"
                                        class="block text-sm font-semibold text-navy mb-1.5">Email Address</label>
                                    <input type="email" id="billing_email" name="billing_email"
                                        value="{{ auth()->user()->email }}" required
                                        class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-navy placeholder-slate-400">
                                </div>

                                <div>
                                    <label for="billing_phone"
                                        class="block text-sm font-semibold text-navy mb-1.5">Phone Number</label>
                                    <input type="tel" id="billing_phone" name="billing_phone"
                                        placeholder="+91 9876543210" required value="{{ auth()->user()->phone ?? '' }}"
                                        class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-navy placeholder-slate-400">
                                </div>
                            </div>
                        </form>

                        <div class="mt-6 p-4 bg-slate-50 rounded-lg border border-slate-200">
                            <label class="flex items-start cursor-pointer">
                                <input id="consent_checkbox" type="checkbox" required
                                    class="mt-1 w-5 h-5 rounded border-gray-300 text-navy focus:ring-navy">
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-navy">I authorize a one-time payment of
                                        ₹{{ number_format($plan->price, 0) }}</p>
                                    <p class="text-xs text-slate-500 mt-1">
                                        By checking this box, I agree to the <a href="#"
                                            class="text-teal-600 underline">Terms of Service</a>. This is a one-time
                                        payment and not a recurring subscription.
                                    </p>
                                </div>
                            </label>
                        </div>

                        <button id="pay-button" type="button"
                            class="w-full mt-6 py-4 bg-navy hover:bg-slate-800 text-white text-center font-bold rounded-xl transition shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                            Pay ₹{{ number_format($plan->price, 0) }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
        <script src="https://js.stripe.com/v3/"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const payButton = document.getElementById('pay-button');
                const errorDiv = document.getElementById('error-message');
                const successDiv = document.getElementById('success-message');
                const defaultButtonText = 'Pay ₹{{ number_format($plan->price, 0) }}';

                function getSelectedGateway() {
                    const checked = document.querySelector('input[name="gateway"]:checked');
                    return checked ? checked.value : 'razorpay';
                }

                function showError(message) {
                    errorDiv.textContent = message;
                    errorDiv.classList.remove('hidden');
                    successDiv.classList.add('hidden');
                }

                function showSuccess(message) {
                    successDiv.textContent = message;
                    successDiv.classList.remove('hidden');
                    errorDiv.classList.add('hidden');
                }

                function hideMessages() {
                    errorDiv.classList.add('hidden');
                    successDiv.classList.add('hidden');
                }

                function resetButton() {
                    payButton.disabled = false;
                    payButton.textContent = defaultButtonText;
                }

                payButton.onclick = async function (e) {
                    e.preventDefault();
                    hideMessages();

                    const name = document.getElementById('billing_name').value;
                    const email = document.getElementById('billing_email').value;
                    const phone = document.getElementById('billing_phone').value;
                    const projectId = document.getElementById('project_id')?.value;
                    const consent = document.getElementById('consent_checkbox').checked;
                    const gateway = getSelectedGateway();

                    if (!name || !email || !phone) {
                        showError('Please fill in all billing details.');
                        return;
                    }

                    if (!projectId) {
                        showError('Project information is missing. Please go back and select a project.');
                        return;
                    }

                    if (!consent) {
                        showError('You must agree to the terms to proceed.');
                        return;
                    }

                    payButton.disabled = true;
                    payButton.textContent = 'Processing...';

                    try {
                        // Step 1: Create order/intent on backend
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
                                project_id: projectId,
                                gateway: gateway,
                            }),
                        });

                        const data = await response.json();

                        if (!data.success) {
                            showError(data.message || 'Failed to initiate payment.');
                            resetButton();
                            return;
                        }

                        // Mock payment shortcut
                        if (data.mock_success) {
                            window.location.href = data.redirect_url;
                            return;
                        }

                        // Step 2: Open payment modal based on gateway
                        if (gateway === 'stripe') {
                            handleStripePayment(data);
                        } else {
                            handleRazorpayPayment(data);
                        }

                    } catch (error) {
                        console.error('Checkout error:', error);
                        showError('An error occurred. Please try again.');
                        resetButton();
                    }
                };

                // ----- RAZORPAY PAYMENT -----
                function handleRazorpayPayment(data) {
                    const options = {
                        key: data.key_id,
                        amount: data.amount,
                        currency: "INR",
                        name: data.name,
                        description: data.description,
                        image: "/logo.png",
                        order_id: data.order_id,
                        prefill: data.prefill,
                        notes: data.notes,
                        theme: { color: '#00BFA6' },
                        handler: function (response) {
                            // Payment successful → verify on server
                            const queryParams = new URLSearchParams({
                                razorpay_payment_id: response.razorpay_payment_id,
                                razorpay_order_id: response.razorpay_order_id,
                                razorpay_signature: response.razorpay_signature,
                                gateway: 'razorpay'
                            });
                            window.location.href = '{{ route("checkout.success") }}?' + queryParams.toString();
                        },
                        modal: {
                            ondismiss: function () {
                                resetButton();
                            }
                        }
                    };

                    const rzp = new Razorpay(options);

                    rzp.on('payment.failed', function (response) {
                        showError('Payment failed: ' + response.error.description);
                        // Record failure on backend
                        recordPaymentFailure('razorpay', data.order_id, response.error);
                        resetButton();
                    });

                    rzp.open();
                }

                // ----- STRIPE PAYMENT -----
                function handleStripePayment(data) {
                    const stripe = Stripe(data.key_id);

                    stripe.confirmCardPayment(data.client_secret, {
                        payment_method: {
                            card: {
                                // For Stripe Elements, you'd mount a card element.
                                // For now, redirect to Stripe Checkout:
                            }
                        }
                    }).then(function (result) {
                        if (result.error) {
                            showError('Payment failed: ' + result.error.message);
                            recordPaymentFailure('stripe', data.payment_intent_id, result.error);
                            resetButton();
                        } else {
                            if (result.paymentIntent.status === 'succeeded') {
                                const queryParams = new URLSearchParams({
                                    payment_intent: result.paymentIntent.id,
                                    redirect_status: 'succeeded',
                                    gateway: 'stripe'
                                });
                                window.location.href = '{{ route("checkout.success") }}?' + queryParams.toString();
                            }
                        }
                    });
                }

                // ----- RECORD FAILURE -----
                async function recordPaymentFailure(gateway, orderId, errorData) {
                    try {
                        await fetch('{{ route("checkout.status") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            body: JSON.stringify({
                                gateway: gateway,
                                order_id: orderId,
                                status: 'failed',
                                error_code: errorData?.code || errorData?.type || 'unknown',
                                error_description: errorData?.description || errorData?.message || 'Payment failed',
                            }),
                        });
                    } catch (e) {
                        console.error('Failed to record payment failure:', e);
                    }
                }
            });
        </script>
    @endpush
</x-layouts.subscriber>