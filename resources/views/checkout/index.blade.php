<x-layouts.app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            {{ __('Checkout') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Plan Summary -->
                <div class="md:col-span-1">
                    <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg sticky top-6">
                        <div class="p-6 text-gray-100">
                            <h3 class="text-lg font-medium text-gray-200 mb-4">Order Summary</h3>
                            
                            <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-700">
                                <div>
                                    <p class="font-bold text-xl">{{ $plan->name }}</p>
                                    <p class="text-sm text-gray-400 capitalize">{{ $plan->interval }} Plan</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-bold text-xl">${{ number_format($plan->price, 2) }}</p>
                                </div>
                            </div>
                            
                            <div class="flex justify-between items-center text-lg font-bold">
                                <span>Total</span>
                                <span>${{ number_format($plan->price, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="md:col-span-2">
                    <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-100">
                            <h3 class="text-lg font-medium text-gray-200 mb-6">Payment Details</h3>

                            @if (session('error'))
                                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                                    <strong class="font-bold">Error!</strong>
                                    <span class="block sm:inline">{{ session('error') }}</span>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('checkout.process', $plan) }}">
                                @csrf

                                <!-- Payment Method Selection -->
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-400 mb-2">Select Payment Method</label>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <label class="relative flex items-center justify-between p-4 border border-gray-700 rounded-lg cursor-pointer hover:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500 transition">
                                            <div class="flex items-center">
                                                <input type="radio" name="payment_method" value="stripe" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300" checked>
                                                <span class="ml-3 font-medium text-gray-200">Credit Card (Stripe)</span>
                                            </div>
                                            <!-- Icon placeholder -->
                                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                            </svg>
                                        </label>

                                        <label class="relative flex items-center justify-between p-4 border border-gray-700 rounded-lg cursor-pointer hover:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500 transition">
                                            <div class="flex items-center">
                                                <input type="radio" name="payment_method" value="razorpay" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                                <span class="ml-3 font-medium text-gray-200">UPI / Netbanking (Razorpay)</span>
                                            </div>
                                            <!-- Icon placeholder -->
                                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </label>
                                    </div>
                                    <x-input-error :messages="$errors->get('payment_method')" class="mt-2" />
                                </div>

                                <!-- Card Details (Simulated) -->
                                <div class="mb-6">
                                    <h4 class="text-sm font-medium text-gray-300 mb-4 uppercase tracking-wider">Card Information</h4>
                                    
                                    <div class="mb-4">
                                        <x-input-label for="card_holder_name" :value="__('Cardholder Name')" />
                                        <x-text-input id="card_holder_name" class="block mt-1 w-full" type="text" name="card_holder_name" required placeholder="John Doe" />
                                        <x-input-error :messages="$errors->get('card_holder_name')" class="mt-2" />
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                        <div class="sm:col-span-2">
                                            <x-input-label for="card_number" :value="__('Card Number')" />
                                            <x-text-input id="card_number" class="block mt-1 w-full" type="text" name="card_number" placeholder="4242 4242 4242 4242" />
                                        </div>
                                        <div>
                                            <x-input-label for="expiry" :value="__('Expiry')" />
                                            <x-text-input id="expiry" class="block mt-1 w-full" type="text" name="expiry" placeholder="MM/YY" />
                                        </div>
                                        <div>
                                            <x-input-label for="cvc" :value="__('CVC')" />
                                            <x-text-input id="cvc" class="block mt-1 w-full" type="text" name="cvc" placeholder="123" />
                                        </div>
                                    </div>
                                    <p class="mt-2 text-xs text-gray-500">
                                        * This is a secure payment simulation. No actual money will be deducted.
                                    </p>
                                </div>

                                <div class="flex items-center justify-end mt-4">
                                    <x-primary-button class="w-full justify-center py-3 text-base">
                                        {{ __('Subscribe Now') }}
                                    </x-primary-button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
