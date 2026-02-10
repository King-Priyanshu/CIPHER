@extends('components.layouts.subscriber')

@section('title', 'Create SIP')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Create SIP</h1>
        <p class="text-gray-600 mt-1">Set up a systematic investment plan for regular investments</p>
    </div>

    <!-- SIP Enrollment Form -->
    <form action="{{ route('subscriber.sip.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Investment Plan -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Investment Plan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="investment_plan_id" class="block text-sm font-medium text-gray-700 mb-2">Select Plan</label>
                    <select id="investment_plan_id" name="investment_plan_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Choose an investment plan</option>
                        @foreach ($investmentPlans as $plan)
                        <option value="{{ $plan->id }}" data-min-amount="{{ $plan->min_investment }}" data-max-amount="{{ $plan->max_investment }}">
                            {{ $plan->name }} - {{ $plan->roi }}% ROI
                        </option>
                        @endforeach
                    </select>
                    @error('investment_plan_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- SIP Details -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">SIP Details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Monthly Investment Amount (₹)</label>
                    <input type="number" id="amount" name="amount" required min="100" step="100" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('amount')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="frequency" class="block text-sm font-medium text-gray-700 mb-2">Payment Frequency</label>
                    <select id="frequency" name="frequency" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="monthly">Monthly</option>
                        <option value="weekly">Weekly</option>
                    </select>
                    @error('frequency')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input type="date" id="start_date" name="start_date" required min="{{ now()->toDateString() }}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('start_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">Investment Duration (Months)</label>
                    <select id="duration" name="duration" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @for ($i = 3; $i <= 60; $i += 3)
                        <option value="{{ $i }}">{{ $i }} months</option>
                        @endfor
                    </select>
                    @error('duration')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Auto Pay Option -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment Settings</h2>
            <div class="flex items-center">
                <input type="checkbox" id="auto_pay" name="auto_pay" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="auto_pay" class="ml-2 block text-sm text-gray-700">
                    Auto-pay using saved payment method
                </label>
            </div>
        </div>

        <!-- Payment Schedule Preview -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment Schedule Preview</h2>
            <div id="payment-schedule-preview" class="h-64 overflow-y-auto border border-gray-200 rounded-md p-4">
                <p class="text-center text-gray-500">Select investment plan, amount, and duration to preview schedule</p>
            </div>
        </div>

        <!-- Investment Summary -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Investment Summary</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="text-center">
                    <p class="text-sm text-gray-600">Monthly Investment</p>
                    <p id="summary-monthly" class="text-xl font-bold text-gray-900">₹0</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-600">Total Investment</p>
                    <p id="summary-total" class="text-xl font-bold text-gray-900">₹0</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-600">Estimated Returns</p>
                    <p id="summary-returns" class="text-xl font-bold text-green-600">₹0</p>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-between">
            <button type="button" onclick="window.history.back()" class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                Create SIP
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form elements
    const investmentPlanSelect = document.getElementById('investment_plan_id');
    const amountInput = document.getElementById('amount');
    const frequencySelect = document.getElementById('frequency');
    const startDateInput = document.getElementById('start_date');
    const durationSelect = document.getElementById('duration');
    const paymentSchedulePreview = document.getElementById('payment-schedule-preview');
    const summaryMonthly = document.getElementById('summary-monthly');
    const summaryTotal = document.getElementById('summary-total');
    const summaryReturns = document.getElementById('summary-returns');

    // Calculate payment schedule and update UI
    function calculateAndUpdate() {
        const planId = investmentPlanSelect.value;
        const amount = parseInt(amountInput.value) || 0;
        const frequency = frequencySelect.value;
        const startDate = startDateInput.value;
        const duration = parseInt(durationSelect.value) || 0;

        if (!planId || !amount || !startDate || !duration) {
            paymentSchedulePreview.innerHTML = '<p class="text-center text-gray-500">Select investment plan, amount, and duration to preview schedule</p>';
            updateSummary(0, 0, 0);
            return;
        }

        // Generate payment schedule
        const payments = [];
        let currentDate = new Date(startDate);
        
        for (let i = 0; i < duration; i++) {
            payments.push({
                date: new Date(currentDate),
                amount: amount
            });

            if (frequency === 'weekly') {
                currentDate.setDate(currentDate.getDate() + 7);
            } else {
                currentDate.setMonth(currentDate.getMonth() + 1);
            }
        }

        // Display schedule
        renderPaymentSchedule(payments);

        // Calculate summary
        const totalInvestment = amount * duration;
        const roi = parseFloat(investmentPlanSelect.options[investmentPlanSelect.selectedIndex].text.match(/(\d+)%/)[1]) || 0;
        const estimatedReturns = calculateReturns(totalInvestment, roi, duration / 12);

        updateSummary(amount, totalInvestment, estimatedReturns);
    }

    // Render payment schedule
    function renderPaymentSchedule(payments) {
        const scheduleHTML = payments.map((payment, index) => `
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <div class="flex items-center">
                    <span class="inline-flex items-center px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 mr-2">
                        ${index + 1}
                    </span>
                    <span class="text-sm text-gray-900">${formatDate(payment.date)}</span>
                </div>
                <span class="text-sm font-medium text-gray-900">₹${payment.amount.toLocaleString()}</span>
            </div>
        `).join('');

        paymentSchedulePreview.innerHTML = `
            <div class="space-y-2">
                ${scheduleHTML}
            </div>
        `;
    }

    // Update summary
    function updateSummary(monthly, total, returns) {
        summaryMonthly.textContent = '₹' + monthly.toLocaleString();
        summaryTotal.textContent = '₹' + total.toLocaleString();
        summaryReturns.textContent = '₹' + returns.toLocaleString();
    }

    // Calculate compound returns
    function calculateReturns(principal, rate, years) {
        return principal * Math.pow(1 + rate / 100, years) - principal;
    }

    // Format date
    function formatDate(date) {
        return date.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        });
    }

    // Set minimum start date
    startDateInput.min = new Date().toISOString().split('T')[0];

    // Event listeners
    investmentPlanSelect.addEventListener('change', calculateAndUpdate);
    amountInput.addEventListener('input', calculateAndUpdate);
    frequencySelect.addEventListener('change', calculateAndUpdate);
    startDateInput.addEventListener('change', calculateAndUpdate);
    durationSelect.addEventListener('change', calculateAndUpdate);

    // Update plan constraints when plan is selected
    investmentPlanSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const minAmount = selectedOption.dataset.minAmount || 100;
        const maxAmount = selectedOption.dataset.maxAmount || 100000;
        
        amountInput.min = minAmount;
        amountInput.max = maxAmount;
    });
});
</script>
@endpush
