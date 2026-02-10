@extends('components.layouts.subscriber')

@section('title', 'Request Refund')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Request Refund</h1>
        <p class="text-gray-600 mt-1">Submit a request for refund of your investment</p>
    </div>

    <!-- Subscription Details -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Subscription Details</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <span class="text-sm text-gray-600">Plan:</span>
                <p class="text-sm font-medium text-gray-900">{{ $subscription->plan->name }}</p>
            </div>
            <div>
                <span class="text-sm text-gray-600">Amount Paid:</span>
                <p class="text-sm font-medium text-gray-900">₹{{ number_format($subscription->amount, 2) }}</p>
            </div>
            <div>
                <span class="text-sm text-gray-600">Start Date:</span>
                <p class="text-sm font-medium text-gray-900">{{ $subscription->starts_at->format('M d, Y') }}</p>
            </div>
            <div>
                <span class="text-sm text-gray-600">End Date:</span>
                <p class="text-sm font-medium text-gray-900">{{ $subscription->ends_at->format('M d, Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Refund Request Form -->
    <form action="{{ route('subscriber.refunds.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Refund Reason -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Refund Reason</h2>
            <div>
                <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">
                    Why are you requesting a refund?
                </label>
                <textarea id="reason" name="reason" rows="4" required
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                          placeholder="Please provide a detailed reason for your refund request..."></textarea>
                @error('reason')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Terms and Conditions -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-start">
                <div class="flex items-center h-5">
                    <input type="checkbox" id="confirm" name="confirm" value="1" required
                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                </div>
                <div class="ml-3 text-sm">
                    <label for="confirm" class="font-medium text-gray-700">
                        I confirm that I have read and understand the refund policy. I acknowledge that:
                    </label>
                    <ul class="mt-2 text-sm text-gray-500 space-y-1 list-disc list-inside">
                        <li>Refund requests will be processed within 5-7 business days</li>
                        <li>Partial refunds may be issued based on the investment duration</li>
                        <li>Refunds will be credited to your original payment method</li>
                        <li>Processing fees may apply</li>
                    </ul>
                </div>
            </div>
            @error('confirm')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-between">
            <button type="button" onclick="window.history.back()" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                Submit Refund Request
            </button>
        </div>
    </form>
</div>
@endsection
