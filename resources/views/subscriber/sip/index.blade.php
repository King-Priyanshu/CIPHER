@extends('components.layouts.subscriber')

@section('title', 'SIP Management')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">SIP Management</h1>
            <a href="{{ route('subscriber.sip.create') }}"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New SIP
            </a>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-blue-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Active SIPs</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $sips->where('status', 'active')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-green-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Investment</p>
                        <p class="text-2xl font-bold text-gray-900">₹{{ number_format($sips->sum('total_investment')) }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-purple-100 p-3 rounded-full">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Upcoming Payments</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $upcomingPayments }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upcoming Payments Calendar -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Upcoming Payments</h2>
            <div id="sip-calendar" class="h-96"></div>
        </div>

        <!-- SIP List -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Your SIPs</h2>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach ($sips as $sip)
                    <div class="px-6 py-4 hover:bg-gray-50">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <h3 class="text-lg font-medium text-gray-900">{{ $sip->investmentPlan->name }}</h3>
                                    <span
                                        class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $sip->status === 'active' ? 'green' : ($sip->status === 'cancelled' ? 'red' : 'blue') }}-100 text-{{ $sip->status === 'active' ? 'green' : ($sip->status === 'cancelled' ? 'red' : 'blue') }}-800">
                                        {{ $sip->status }}
                                    </span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">
                                    ₹{{ number_format($sip->amount) }} / {{ ucfirst($sip->frequency) }} for {{ $sip->duration }}
                                    {{ $sip->frequency === 'weekly' ? 'weeks' : 'months' }}
                                </p>
                                <p class="text-sm text-gray-500 mt-1">
                                    Starts on {{ $sip->start_date->format('M d, Y') }}
                                </p>
                            </div>
                            <div class="flex items-center space-x-3 ml-6">
                                <a href="{{ route('subscriber.sip.show', $sip) }}"
                                    class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                    View Details
                                </a>
                                @if ($sip->status === 'active')
                                    <form action="{{ route('subscriber.sip.cancel', $sip) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                            Cancel
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('sip-calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                plugins: [window.FullCalendar.dayGridPlugin, window.FullCalendar.interactionPlugin],
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,dayGridWeek'
                },
                events: @json($calendarEvents),
                eventClick: function (info) {
                    alert('SIP Payment: ₹' + info.event.extendedProps.amount + ' on ' + info.event.start.toLocaleDateString());
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