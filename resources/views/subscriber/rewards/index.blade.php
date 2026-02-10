<x-layouts.subscriber>
    <x-slot:title>
        Rewards
    </x-slot:title>

    <div class="space-y-6">

        <!-- Rewards Summary -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="card p-6">
                <p class="text-sm font-medium text-slate-500">Total Earned</p>
                <p class="text-3xl font-bold text-navy mt-1">₹{{ number_format($totalRewards ?? 0, 2) }}</p>
            </div>
            <div class="card p-6">
                <p class="text-sm font-medium text-slate-500">Last Payout</p>
                <p class="text-3xl font-bold text-navy mt-1">₹0.00</p>
                <!-- Placeholder for future logic -->
            </div>
            <div class="card p-6">
                <p class="text-sm font-medium text-slate-500">Next Projected</p>
                <div class="flex items-center gap-2 mt-1">
                    <p class="text-3xl font-bold text-navy">--</p>
                    <span class="text-xs text-slate-400">(Estimating)</span>
                </div>
            </div>
        </div>

        <!-- Earnings Chart -->
        <div class="card overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-white">
                <h3 class="text-lg font-bold text-navy">Earnings Overview</h3>
                <div class="flex gap-2">
                    <select
                        class="px-3 py-1 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition"
                        id="timeRange">
                        <option value="7d">7 Days</option>
                        <option value="30d" selected>30 Days</option>
                        <option value="90d">90 Days</option>
                        <option value="1y">1 Year</option>
                        <option value="all">All Time</option>
                    </select>
                </div>
            </div>
            <div class="p-6">
                <div class="h-64">
                    <canvas id="earningsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Rewards History Table -->
        <div class="card overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-white flex items-center justify-between">
                <h3 class="text-lg font-bold text-navy">Reward History</h3>
                <div class="flex gap-2">
                    <input type="text" placeholder="Search rewards..."
                        class="px-3 py-1 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition"
                        id="rewardsSearch" />
                    <select
                        class="px-3 py-1 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition"
                        id="sourceFilter">
                        <option value="">All Sources</option>
                        <option value="referral">Referral</option>
                        <option value="profit">Profit</option>
                        <option value="bonus">Bonus</option>
                        <option value="system">System</option>
                    </select>
                </div>
            </div>
            <div class="overflow-x-auto">
                {{-- Assuming $rewards is passed from controller. If not, we'll need empty state.
                I will handle both cases safeley. --}}
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                Date</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                Source</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                Description</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">
                                Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white" id="rewardsTable">
                        @if(isset($rewards) && $rewards->isNotEmpty())
                            @foreach($rewards as $reward)
                                <tr class="hover:bg-slate-50/50 transition reward-row"
                                    data-source="{{ $reward->source_type ?? 'System' }}"
                                    data-description="{{ $reward->description }}">
                                    <td class="px-6 py-4 text-sm text-slate-600">
                                        {{ $reward->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                                            {{ $reward->source_type ?? 'System' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-navy font-medium">
                                        {{ $reward->description }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-bold text-emerald-600">
                                        +₹{{ number_format($reward->amount, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                                    <div
                                        class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6 text-slate-300" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <p>No rewards received yet.</p>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Earnings Chart
            const ctx = document.getElementById('earningsChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        datasets: [
                            {
                                label: 'Rewards',
                                data: [1200, 1800, 2100, 2800, 3200, 3800],
                                borderColor: '#00BFA6',
                                backgroundColor: 'rgba(0, 191, 166, 0.1)',
                                tension: 0.4,
                                fill: true
                            },
                            {
                                label: 'Profits',
                                data: [800, 1200, 1500, 2000, 2300, 2700],
                                borderColor: '#10B981',
                                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                tension: 0.4,
                                fill: true
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: {
                                    font: {
                                        family: 'Inter',
                                        size: 12,
                                        weight: '600'
                                    },
                                    color: '#1A2F4B'
                                }
                            },
                            tooltip: {
                                backgroundColor: 'rgba(26, 47, 75, 0.9)',
                                titleFont: {
                                    family: 'Inter',
                                    size: 13,
                                    weight: '600'
                                },
                                bodyFont: {
                                    family: 'Inter',
                                    size: 12
                                },
                                callbacks: {
                                    label: function (context) {
                                        return '₹' + context.parsed.y.toFixed(0);
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: true,
                                    color: 'rgba(100, 116, 139, 0.1)'
                                },
                                ticks: {
                                    font: {
                                        family: 'Inter',
                                        size: 11
                                    },
                                    color: '#64748B'
                                }
                            },
                            y: {
                                beginAtZero: false,
                                grid: {
                                    display: true,
                                    color: 'rgba(100, 116, 139, 0.1)'
                                },
                                ticks: {
                                    font: {
                                        family: 'Inter',
                                        size: 11
                                    },
                                    color: '#64748B',
                                    callback: function (value) {
                                        return '₹' + value.toFixed(0);
                                    }
                                }
                            }
                        },
                        interaction: {
                            intersect: false,
                            mode: 'index'
                        }
                    }
                });
            }

            // Search functionality
            const searchInput = document.getElementById('rewardsSearch');
            const sourceFilter = document.getElementById('sourceFilter');
            const rewardRows = document.querySelectorAll('.reward-row');

            searchInput.addEventListener('input', function () {
                const searchTerm = this.value.toLowerCase();

                rewardRows.forEach(row => {
                    const description = row.dataset.description.toLowerCase();

                    if (description.includes(searchTerm)) {
                        row.style.display = '';
                        row.style.animation = 'fadeIn 0.3s ease-out';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            sourceFilter.addEventListener('change', function () {
                const source = this.value;

                rewardRows.forEach(row => {
                    if (source === '' || row.dataset.source.toLowerCase() === source.toLowerCase()) {
                        row.style.display = '';
                        row.style.animation = 'fadeIn 0.3s ease-out';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    </script>
</x-layouts.subscriber>