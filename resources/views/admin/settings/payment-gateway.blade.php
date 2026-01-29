<x-layouts.admin>
    <x-slot:title>Payment Gateway Settings</x-slot:title>

    <div class="max-w-3xl mx-auto">
        @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl">
            {{ session('success') }}
        </div>
        @endif

        <!-- Razorpay Settings -->
        <div class="card mb-6">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-navy">Razorpay</h2>
                        <p class="text-sm text-slate-500">Indian payment gateway for subscriptions</p>
                    </div>
                    <div class="ml-auto">
                        @if($settings['razorpay_configured'])
                        <span class="inline-flex px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-medium">
                            ✓ Configured
                        </span>
                        @else
                        <span class="inline-flex px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm font-medium">
                            Not Configured
                        </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="p-6">
                <form action="{{ route('admin.settings.payment-gateway.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <div>
                            <label for="razorpay_key" class="block text-sm font-semibold text-navy mb-1.5">API Key ID</label>
                            <input type="text" name="razorpay_key" id="razorpay_key" 
                                   value="{{ $settings['razorpay_key'] }}"
                                   placeholder="rzp_test_xxxxxxxxxxxxx"
                                   class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 font-mono text-sm">
                            <p class="text-xs text-slate-400 mt-1">Get this from Razorpay Dashboard → Settings → API Keys</p>
                        </div>

                        <div>
                            <label for="razorpay_secret" class="block text-sm font-semibold text-navy mb-1.5">API Key Secret</label>
                            <input type="password" name="razorpay_secret" id="razorpay_secret" 
                                   placeholder="••••••••••••••••"
                                   class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 font-mono text-sm">
                            <p class="text-xs text-slate-400 mt-1">Leave blank to keep existing secret</p>
                        </div>

                        <div>
                            <label for="razorpay_webhook_secret" class="block text-sm font-semibold text-navy mb-1.5">Webhook Secret</label>
                            <input type="password" name="razorpay_webhook_secret" id="razorpay_webhook_secret" 
                                   placeholder="••••••••••••••••"
                                   class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 font-mono text-sm">
                            <p class="text-xs text-slate-400 mt-1">For verifying webhook signatures (optional but recommended)</p>
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="px-6 py-3 bg-navy text-white rounded-lg font-semibold hover:bg-slate-800 transition">
                                Save Settings
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Webhook URL -->
        <div class="card">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-navy">Webhook Configuration</h3>
            </div>
            <div class="p-6">
                <p class="text-sm text-slate-600 mb-4">
                    Add this URL in your Razorpay Dashboard → Webhooks → Add New Webhook
                </p>
                <div class="flex items-center gap-2">
                    <input type="text" value="{{ $settings['webhook_url'] }}" readonly
                           class="flex-1 rounded-lg border-gray-300 bg-slate-50 font-mono text-sm text-slate-600">
                    <button onclick="navigator.clipboard.writeText('{{ $settings['webhook_url'] }}')" 
                            class="px-4 py-2 bg-slate-100 text-slate-700 rounded-lg font-medium hover:bg-slate-200 transition">
                        Copy
                    </button>
                </div>

                <div class="mt-6">
                    <h4 class="text-sm font-semibold text-navy mb-2">Events to Enable:</h4>
                    <ul class="text-sm text-slate-600 space-y-1">
                        <li class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-teal-500 rounded-full"></span>
                            subscription.activated
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-teal-500 rounded-full"></span>
                            subscription.charged
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-teal-500 rounded-full"></span>
                            subscription.cancelled
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-teal-500 rounded-full"></span>
                            payment.captured
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-teal-500 rounded-full"></span>
                            payment.failed
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
