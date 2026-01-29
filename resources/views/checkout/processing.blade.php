<x-layouts.subscriber>
    <x-slot:title>
        Processing Payment
    </x-slot:title>

    <div class="max-w-lg mx-auto">
        <div class="card p-8 text-center">
            <!-- Loading State -->
            <div id="loading-state">
                <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-teal-50 flex items-center justify-center">
                    <svg class="w-10 h-10 text-teal-500 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                
                <h2 class="text-2xl font-bold text-navy mb-3">Processing Your Subscription</h2>
                <p class="text-slate-500 mb-6">
                    Please wait while we confirm your payment. This usually takes a few seconds.
                </p>
                
                <div class="bg-slate-50 rounded-xl p-4 text-sm text-slate-600">
                    <p>🔒 Your payment is being securely processed by Razorpay.</p>
                </div>
            </div>

            <!-- Success State (hidden by default) -->
            <div id="success-state" class="hidden">
                <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-green-100 flex items-center justify-center">
                    <svg class="w-10 h-10 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                
                <h2 class="text-2xl font-bold text-navy mb-3">Subscription Activated!</h2>
                <p class="text-slate-500 mb-6">
                    Welcome to CIPHER! Your subscription is now active.
                </p>
                
                <a href="{{ route('subscriber.dashboard') }}" class="inline-block px-8 py-3 bg-navy text-white font-semibold rounded-xl hover:bg-slate-800 transition">
                    Go to Dashboard
                </a>
            </div>

            <!-- Error State (hidden by default) -->
            <div id="error-state" class="hidden">
                <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-red-100 flex items-center justify-center">
                    <svg class="w-10 h-10 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                
                <h2 class="text-2xl font-bold text-navy mb-3">Something Went Wrong</h2>
                <p class="text-slate-500 mb-6" id="error-message">
                    We couldn't confirm your payment. Please contact support if you were charged.
                </p>
                
                <div class="flex gap-4 justify-center">
                    <a href="{{ route('subscriber.subscription.index') }}" class="px-6 py-3 bg-slate-100 text-slate-700 font-semibold rounded-xl hover:bg-slate-200 transition">
                        View Subscriptions
                    </a>
                    <a href="mailto:support@cipher.community" class="px-6 py-3 bg-navy text-white font-semibold rounded-xl hover:bg-slate-800 transition">
                        Contact Support
                    </a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const subscriptionId = "{{ $subscription_id ?? '' }}";
            
            if (!subscriptionId) {
                showError('No subscription ID provided.');
                return;
            }

            let attempts = 0;
            const maxAttempts = 30; // 30 attempts x 2 seconds = 60 seconds max wait

            function checkStatus() {
                attempts++;

                fetch('/checkout/status', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ subscription_id: subscriptionId }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.activated) {
                        showSuccess();
                    } else if (data.status === 'failed' || data.status === 'cancelled') {
                        showError('Payment was not successful.');
                    } else if (attempts >= maxAttempts) {
                        showError('Payment confirmation is taking longer than expected. Please check your dashboard.');
                    } else {
                        // Keep polling
                        setTimeout(checkStatus, 2000);
                    }
                })
                .catch(error => {
                    console.error('Status check error:', error);
                    if (attempts >= maxAttempts) {
                        showError('Unable to verify payment status.');
                    } else {
                        setTimeout(checkStatus, 2000);
                    }
                });
            }

            function showSuccess() {
                document.getElementById('loading-state').classList.add('hidden');
                document.getElementById('success-state').classList.remove('hidden');
            }

            function showError(message) {
                document.getElementById('loading-state').classList.add('hidden');
                document.getElementById('error-state').classList.remove('hidden');
                if (message) {
                    document.getElementById('error-message').textContent = message;
                }
            }

            // Start polling
            setTimeout(checkStatus, 2000);
        });
    </script>
    @endpush
</x-layouts.subscriber>
