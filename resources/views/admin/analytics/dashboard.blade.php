<x-layouts.admin>
    <x-slot:title>
        Analytics Dashboard
    </x-slot:title>

    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-navy mb-2">Analytics Dashboard</h1>
        <p class="text-slate-600">Track your business performance and user metrics</p>
    </div>

    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Users -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Users</p>
                    <p class="text-3xl font-bold text-navy mt-1">{{ number_format($metrics['total_users'] ?? 0) }}</p>
                    <p class="text-xs text-green-600 mt-1">{{ $metrics['user_growth'] ?? 0 }}% from last month</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center shadow-sm">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- New Users -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">New Users</p>
                    <p class="text-3xl font-bold text-navy mt-1">{{ number_format($metrics['new_users'] ?? 0) }}</p>
                    <p class="text-xs text-green-600 mt-1">{{ $metrics['new_users_growth'] ?? 0 }}% from last month</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-teal-100 flex items-center justify-center shadow-sm">
                    <svg class="w-6 h-6 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Revenue -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Revenue</p>
                    <p class="text-3xl font-bold text-navy mt-1">₹{{ number_format($metrics['total_revenue'] ?? 0, 0) }}</p>
                    <p class="text-xs text-green-600 mt-1">{{ $metrics['revenue_growth'] ?? 0 }}% from last month</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center shadow-sm">
                    <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Conversion Rate -->
        <div class="card p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Conversion Rate</p>
                    <p class="text-3xl font-bold text-navy mt-1">{{ $metrics['conversion_rate'] ?? 0 }}%</p>
                    <p class="text-xs text-red-600 mt-1">{{ $metrics['conversion_rate_change'] ?? 0 }}% from last month</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center shadow-sm">
                    <svg class="w-6 h-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Revenue Trends -->
        <div class="card p-6">
            <h2 class="text-lg font-semibold text-navy mb-4">Revenue Trends</h2>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- User Acquisition -->
        <div class="card p-6">
            <h2 class="text-lg font-semibold text-navy mb-4">User Acquisition</h2>
            <div class="h-64">
                <canvas id="userAcquisitionChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Secondary Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Investment Distribution -->
        <div class="card p-6">
            <h2 class="text-lg font-semibold text-navy mb-4">Investment Distribution</h2>
            <div class="h-64">
                <canvas id="investmentDistributionChart"></canvas>
            </div>
        </div>

        <!-- User Activity -->
        <div class="card p-6">
            <h2 class="text-lg font-semibold text-navy mb-4">User Activity</h2>
            <div class="h-64">
                <canvas id="userActivityChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Detailed Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Subscription Plans -->
        <div class="card p-6">
            <h3 class="text-sm font-medium text-slate-700 mb-4">Subscription Plans</h3>
            <div class="space-y-4">
                @foreach($subscriptionData as $plan)
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-slate-900">{{ $plan['name'] }}</span>
                            <span class="text-sm font-semibold text-navy">{{ $plan['count'] }} users</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $plan['percentage'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="card p-6">
            <h3 class="text-sm font-medium text-slate-700 mb-4">Payment Methods</h3>
            <div class="space-y-4">
                @foreach($paymentMethodsData as $method)
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-slate-900">{{ $method['name'] }}</span>
                            <span class="text-sm font-semibold text-navy">{{ $method['count'] }} transactions</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full" style="width: {{ $method['percentage'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- User Demographics -->
        <div class="card p-6">
            <h3 class="text-sm font-medium text-slate-700 mb-4">User Demographics</h3>
            <div class="space-y-4">
                @foreach($demographicsData as $demo)
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-medium text-slate-900">{{ $demo['category'] }}</span>
                            <span class="text-sm font-semibold text-navy">{{ $demo['count'] }} users</span>
                        </div>
                        <div class="w-full bg-slate-200 rounded-full h-2">
                            <div class="bg-purple-600 h-2 rounded-full" style="width: {{ $demo['percentage'] }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Chart.js Integration -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        const revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: @json($revenueData['labels']),
                datasets: [{
                    label: 'Revenue',
                    data: @json($revenueData['data']),
                    borderColor: '#1e40af',
                    backgroundColor: 'rgba(30, 64, 175, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: ₹' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₹' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // User Acquisition Chart
        const userAcquisitionCtx = document.getElementById('userAcquisitionChart').getContext('2d');
        const userAcquisitionChart = new Chart(userAcquisitionCtx, {
            type: 'bar',
            data: {
                labels: @json($userAcquisitionData['labels']),
                datasets: [{
                    label: 'New Users',
                    data: @json($userAcquisitionData['data']),
                    backgroundColor: 'rgba(99, 102, 241, 0.8)',
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 13
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
            }
        });

        // Investment Distribution Chart
        const investmentCtx = document.getElementById('investmentDistributionChart').getContext('2d');
        const investmentChart = new Chart(investmentCtx, {
            type: 'doughnut',
            data: {
                labels: @json($investmentDistributionData['labels']),
                datasets: [{
                    data: @json($investmentDistributionData['data']),
                    backgroundColor: [
                        '#3b82f6',
                        '#10b981',
                        '#f59e0b',
                        '#ef4444',
                        '#8b5cf6'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom',
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = Math.round((value / total) * 100);
                                return label + ': ' + percentage + '%';
                            }
                        }
                    }
                }
            }
        });

        // User Activity Chart
        const userActivityCtx = document.getElementById('userActivityChart').getContext('2d');
        const userActivityChart = new Chart(userActivityCtx, {
            type: 'radar',
            data: {
                labels: @json($userActivityData['labels']),
                datasets: [{
                    label: 'Active Users',
                    data: @json($userActivityData['data']),
                    fill: true,
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    borderColor: 'rgba(59, 130, 246, 0.8)',
                    pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(59, 130, 246, 1)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 13
                        }
                    }
                },
                scales: {
                    r: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            stepSize: 20
                        }
                    }
                }
            }
        });
    </script>
</x-layouts.admin>
