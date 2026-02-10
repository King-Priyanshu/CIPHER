@extends('components.layouts.subscriber')

@section('title', 'SIP Details')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900">SIP Details</h1>
            <p class="text-gray-600 mt-1">{{ $sip->investmentPlan->name }}</p>
        </div>

        <!-- SIP Status Badge -->
        <div class="mb-6">
            <span
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $sip->status === 'active' ? 'green' : ($sip->status === 'cancelled' ? 'red' : 'blue') }}-100 text-{{ $sip->status === 'active' ? 'green' : ($sip->status === 'cancelled' ? 'red' : 'blue') }}-800">
                {{ ucfirst($sip->status) }}
            </span>
        </div>

        <!-- SIP Info Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">SIP Details</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Investment Plan</span>
                        <span class="text-sm font-medium text-gray-900">{{ $sip->investmentPlan->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Amount per Installment</span>
                        <span class="text-sm font-medium text-gray-900">₹{{ number_format($sip->amount) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Frequency</span>
                        <span class="text-sm font-medium text-gray-900">{{ ucfirst($sip->frequency) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Duration</span>
                        <span class="text-sm font-medium text-gray-900">{{ $sip->duration }}
                            {{ $sip->frequency === 'weekly' ? 'weeks' : 'months' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Start Date</span>
                        <span class="text-sm font-medium text-gray-900">{{ $sip->start_date->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">End Date</span>
                        <span
                            class="text-sm font-medium text-gray-900">{{ $sip->start_date->addMonths($sip->duration)->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Auto Pay</span>
                        <span class="text-sm font-medium text-gray-900">{{ $sip->auto_pay ? 'Enabled' : 'Disabled' }}</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Investment Summary</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Total Installments</span>
                        <span class="text-sm font-medium text-gray-900">{{ $sip->duration }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Completed Installments</span>
                        <span class="text-sm font-medium text-gray-900">{{ $sip->completed_payments_count }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Pending Installments</span>
                        <span class="text-sm font-medium text-gray-900">{{ $sip->pending_payments_count }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Total Investment</span>
                        <span class="text-sm font-medium text-gray-900">₹{{ number_format($sip->total_investment) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Invested So Far</span>
                        <span
                            class="text-sm font-medium text-gray-900">₹{{ number_format($sip->completed_payments_count * $sip->amount) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Next Payment</span>
                        <span class="text-sm font-medium text-gray-900">
                            {{ $sip->next_payment_date ? $sip->next_payment_date->format('M d, Y') : 'N/A' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Schedule Calendar -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Schedule</h3>
            <div id="payment-calendar" class="h-96"></div>
        </div>

        <!-- Payment History Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Payment History</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Installment
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Amount
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Action
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($sip->paymentSchedule as $payment)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $payment->payment_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    ₹{{ number_format($payment->amount) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $payment->status === 'paid' ? 'green' : ($payment->status === 'failed' ? 'red' : 'yellow') }}-100 text-{{ $payment->status === 'paid' ? 'green' : ($payment->status === 'failed' ? 'red' : 'yellow') }}-800">
                                        {{ $payment->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if ($payment->status === 'pending' && $payment->payment_date->isFuture())
                                        <button class="text-blue-600 hover:text-blue-900">Pay Now</button>
                                    @elseif ($payment->status === 'failed')
                                        <button class="text-blue-600 hover:text-blue-900">Retry</button>
                                    @elseif ($payment->status === 'paid' && $payment->payment)
                                        <a href="{{ route('subscriber.payments.show', $payment->payment) }}"
                                            class="text-blue-600 hover:text-blue-900">
                                            View Receipt
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-8 flex items-center space-x-4">
            @if ($sip->status === 'active')
                <a href="{{ route('subscriber.sip.edit', $sip) }}"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 transition-colors">
                    Edit SIP
                </a>
                <form action="{{ route('subscriber.sip.cancel', $sip) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                        Cancel SIP
                    </button>
                </form>
            @endif
            <a href="{{ route('subscriber.sip.index') }}"
                class="inline-flex items-center px-4 py-2 text-gray-600 text-sm font-medium hover:text-gray-900 transition-colors">
                Back to SIPs
            </a>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('payment-calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                plugins: [window.FullCalendar.dayGridPlugin, window.FullCalendar.interactionPlugin],
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,dayGridWeek'
                },
                events: @json($sip->paymentSchedule->map(function ($payment) {
                    return [
                        'title' => '₹' . $payment->amount,
                        'start' => $payment->payment_date->toDateString(),
                        'extendedProps' => [
                            'status' => $payment->status,
                            'amount' => $payment->amount
                        ]
                    ];
                })),
                eventClick: function (info) {
                    alert('SIP Payment: ₹' + info.event.extendedProps.amount + ' - Status: ' + info.event.extendedProps.status);
                },
                eventDidMount: function (info) {
                    const eventElement = info.el;
                    if (info.event.extendedProps.status === 'pending') {
                        eventElement.classList.add('bg-yellow-100', 'text-yellow-800');
                    } else if (info.event.extendedProps.status === 'paid') {
                        eventElement.classList.add('bg-green-100', 'text-green-800');
                    } else if (info.event.extendedProps.status === 'failed') {
                        eventElement.classList.add('bg-red-100', 'text-red-800');
                    }
                }
            });
            calendar.render();
        });
    </script>
@endpush