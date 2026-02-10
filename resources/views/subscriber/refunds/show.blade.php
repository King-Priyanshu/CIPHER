@extends('components.layouts.subscriber')

@section('title', 'Refund Details')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Refund Details</h1>
        <p class="text-gray-600 mt-1">View the details of your refund request</p>
    </div>

    <!-- Refund Status Badge -->
    <div class="mb-6">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ 
            $refund->status === 'pending' ? 'yellow' : 
            $refund->status === 'approved' ? 'green' : 
            $refund->status === 'processed' ? 'blue' : 'red' 
        }}-100 text-{{ 
            $refund->status === 'pending' ? 'yellow' : 
            $refund->status === 'approved' ? 'green' : 
            $refund->status === 'processed' ? 'blue' : 'red' 
        }}-800">
            {{ ucfirst($refund->status) }}
        </span>
    </div>

    <!-- Refund Information -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Refund Details</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Refund ID</span>
                    <span class="text-sm font-medium text-gray-900">#{{ str_pad($refund->id, 6, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Date Requested</span>
                    <span class="text-sm font-medium text-gray-900">{{ $refund->created_at->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Amount Requested</span>
                    <span class="text-sm font-medium text-gray-900">₹{{ number_format($refund->amount, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Processing Fee</span>
                    <span class="text-sm font-medium text-gray-900">₹{{ number_format($refund->amount * 0.02, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Net Amount</span>
                    <span class="text-sm font-medium text-gray-900">₹{{ number_format($refund->amount * 0.98, 2) }}</span>
                </div>
                @if ($refund->processed_at)
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Processed Date</span>
                    <span class="text-sm font-medium text-gray-900">{{ $refund->processed_at->format('M d, Y') }}</span>
                </div>
                @endif
                @if ($refund->transaction_id)
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Transaction ID</span>
                    <span class="text-sm font-medium text-gray-900">{{ $refund->transaction_id }}</span>
                </div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Subscription Details</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Plan Name</span>
                    <span class="text-sm font-medium text-gray-900">{{ $refund->subscription->plan->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Subscription ID</span>
                    <span class="text-sm font-medium text-gray-900">#{{ str_pad($refund->subscription->id, 6, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Start Date</span>
                    <span class="text-sm font-medium text-gray-900">{{ $refund->subscription->starts_at->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">End Date</span>
                    <span class="text-sm font-medium text-gray-900">{{ $refund->subscription->ends_at->format('M d, Y') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600">Duration</span>
                    <span class="text-sm font-medium text-gray-900">{{ $refund->subscription->starts_at->diffInMonths($refund->subscription->ends_at) }} months</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Refund Timeline -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Refund Timeline</h3>
        <div class="space-y-4">
            <!-- Requested -->
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-3 h-3 bg-blue-400 rounded-full mt-2"></div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">Refund Requested</p>
                    <p class="text-sm text-gray-500">{{ $refund->created_at->format('M d, Y H:i') }}</p>
                </div>
            </div>

            <!-- Pending/Approved/Rejected -->
            @if ($refund->status === 'pending')
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-3 h-3 bg-yellow-400 rounded-full mt-2"></div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">Under Review</p>
                    <p class="text-sm text-gray-500">Your refund request is being processed. We typically respond within 24-48 hours.</p>
                </div>
            </div>
            @elseif ($refund->status === 'approved')
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-3 h-3 bg-green-400 rounded-full mt-2"></div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">Refund Approved</p>
                    <p class="text-sm text-gray-500">{{ $refund->updated_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-3 h-3 bg-blue-400 rounded-full mt-2"></div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">Processing Refund</p>
                    <p class="text-sm text-gray-500">Your refund is being processed. It will be credited to your account within 3-5 business days.</p>
                </div>
            </div>
            @elseif ($refund->status === 'processed')
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-3 h-3 bg-green-400 rounded-full mt-2"></div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">Refund Approved</p>
                    <p class="text-sm text-gray-500">{{ $refund->updated_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-3 h-3 bg-green-400 rounded-full mt-2"></div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">Refund Processed</p>
                    <p class="text-sm text-gray-500">{{ $refund->processed_at->format('M d, Y H:i') }}</p>
                    <p class="text-sm text-gray-500">Amount credited to your account: ₹{{ number_format($refund->amount * 0.98, 2) }}</p>
                </div>
            </div>
            @elseif ($refund->status === 'rejected')
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-3 h-3 bg-red-400 rounded-full mt-2"></div>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-900">Refund Rejected</p>
                    <p class="text-sm text-gray-500">{{ $refund->updated_at->format('M d, Y H:i') }}</p>
                    @if ($refund->admin_note)
                    <p class="text-sm text-gray-500 mt-1">Reason: {{ $refund->admin_note }}</p>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Refund Reason -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Refund Reason</h3>
        <div class="bg-gray-50 rounded-md p-4">
            <p class="text-sm text-gray-700">{{ $refund->reason }}</p>
        </div>
        @if ($refund->admin_note)
        <div class="mt-4 bg-gray-50 rounded-md p-4">
            <p class="text-sm text-gray-600 font-medium">Admin Note:</p>
            <p class="text-sm text-gray-700 mt-1">{{ $refund->admin_note }}</p>
        </div>
        @endif
    </div>

    <!-- Action Buttons -->
    <div class="flex items-center space-x-4">
        @if ($refund->status === 'pending')
        <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
            Cancel Request
        </button>
        @endif
        <a href="{{ route('subscriber.refunds.index') }}" class="inline-flex items-center px-4 py-2 text-gray-600 text-sm font-medium hover:text-gray-900 transition-colors">
            Back to Refunds
        </a>
    </div>
</div>
@endsection
