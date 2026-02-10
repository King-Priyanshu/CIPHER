<x-layouts.subscriber>
    <x-slot:title>
        Referral Dashboard
    </x-slot:title>

    <div class="space-y-6">
        <!-- Referral Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="card p-6" role="region" aria-label="Total Referrals">
                <p class="text-sm font-medium text-slate-500">Total Referrals</p>
                <p class="text-3xl font-bold text-navy mt-1">{{ $totalReferrals ?? 0 }}</p>
            </div>
            <div class="card p-6" role="region" aria-label="Active Referrals">
                <p class="text-sm font-medium text-slate-500">Active Referrals</p>
                <p class="text-3xl font-bold text-navy mt-1">{{ $activeReferrals ?? 0 }}</p>
            </div>
            <div class="card p-6" role="region" aria-label="Total Earnings">
                <p class="text-sm font-medium text-slate-500">Total Earnings</p>
                <p class="text-3xl font-bold text-navy mt-1">₹{{ number_format($totalEarnings ?? 0, 2) }}</p>
            </div>
        </div>

        <!-- Referral Link & QR Code -->
        <div class="card p-6">
            <div class="flex flex-col md:flex-row gap-8 items-center">
                <!-- Referral Link -->
                <div class="flex-1 w-full">
                    <h3 class="text-lg font-bold text-navy mb-3">Your Referral Link</h3>
                    <div class="flex items-center gap-2">
                        <input type="text" value="{{ $referralLink }}" readonly
                            class="flex-1 px-4 py-2 border border-slate-300 rounded-lg bg-slate-50 font-mono text-sm" />
                        <button onclick="copyReferralLink('{{ $referralLink }}')"
                            class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </button>
                    </div>
                    <p class="text-sm text-slate-500 mt-2">Share this link with friends to earn referral bonuses</p>
                </div>

                <!-- QR Code -->
                <div class="w-full md:w-auto">
                    <h3 class="text-lg font-bold text-navy mb-3">QR Code</h3>
                    <div class="bg-white p-4 rounded-lg shadow-md border border-slate-200">
                        <div id="qrcode" class="w-48 h-48"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Social Sharing -->
        <div class="card p-6">
            <h3 class="text-lg font-bold text-navy mb-4">Share on Social Media</h3>
            <div class="flex flex-wrap gap-4">
                <a href="#"
                    class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                    </svg>
                    Share on Facebook
                </a>
                <a href="#"
                    class="flex items-center gap-2 px-4 py-2 bg-sky-500 text-white rounded-lg hover:bg-sky-600 transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                    </svg>
                    Share on Twitter
                </a>
                <a href="#"
                    class="flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                    </svg>
                    Share on YouTube
                </a>
            </div>
        </div>

        <!-- Earnings Chart -->
        <div class="card overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-white flex items-center justify-between">
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
                <div class="h-80">
                    <canvas id="earningsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Referrals -->
        <div class="card overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-white flex items-center justify-between">
                <h3 class="text-lg font-bold text-navy">Recent Referrals</h3>
                <div class="flex gap-2">
                    <input type="text" placeholder="Search referrals..."
                        class="px-3 py-1 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition"
                        id="referralSearch" />
                    <select
                        class="px-3 py-1 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition"
                        id="statusFilter">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="pending">Pending</option>
                    </select>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                Name</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                Email</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                Date Joined</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">
                                Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white" id="referralsTable">
                        @if($recentReferrals->isNotEmpty())
                            @foreach($recentReferrals as $referral)
                                            <tr class="hover:bg-slate-50/50 transition referral-row" data-name="{{ $referral->name }}"
                                                data-email="{{ $referral->email }}"
                                                data-status="{{ $referral->subscription && $referral->subscription->status === 'active' ? 'active' : 'pending' }}">
                                                <td class="px-6 py-4 text-sm font-medium text-navy">
                                                    {{ $referral->name }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-slate-600">
                                                    {{ $referral->email }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-slate-600">
                                                    {{ $referral->created_at->format('M d, Y') }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                                                {{ $referral->subscription && $referral->subscription->status === 'active'
                                ? 'bg-green-50 text-green-700 border border-green-100'
                                : 'bg-amber-50 text-amber-700 border border-amber-100' }}">
                                                        {{ $referral->subscription && $referral->subscription->status === 'active' ? 'Active' : 'Pending' }}
                                                    </span>
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
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                    <p>No referrals yet.</p>
                                    <p class="text-sm text-slate-500 mt-1">Start sharing your referral link to earn bonuses
                                    </p>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Copy referral link to clipboard
        function copyReferralLink(link) {
            navigator.clipboard.writeText(link).then(() => {
                // Show success message
                const button = event.target.closest('button');
                const originalHTML = button.innerHTML;
                button.innerHTML = `
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                `;
                button.classList.remove('bg-teal-600', 'hover:bg-teal-700');
                button.classList.add('bg-green-600', 'hover:bg-green-700');

                setTimeout(() => {
                    button.innerHTML = originalHTML;
                    button.classList.remove('bg-green-600', 'hover:bg-green-700');
                    button.classList.add('bg-teal-600', 'hover:bg-teal-700');
                }, 2000);
            });
        }

        // Generate QR code and earnings chart
        document.addEventListener('DOMContentLoaded', function () {
            // Load QR code library
            import('qrcode').then(QRCode => {
                const qrCode = new QRCode.default('qrcode', {
                    text: '{{ $referralLink }}',
                    width: 192,
                    height: 192,
                    colorDark: '#1A2F4B',
                    colorLight: '#FFFFFF',
                    correctLevel: QRCode.default.CorrectLevel.H
                });
            });

            // Render earnings chart
            const chartData = @json($chartData);
            const ctx = document.getElementById('earningsChart');
            if (ctx) {
                new Chart(ctx, {
                    type: 'line',
                    data: chartData,
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
                                        return 'Earnings: ₹' + context.parsed.y.toFixed(2);
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                type: 'time',
                                time: {
                                    unit: 'day',
                                    displayFormats: {
                                        day: 'MMM d'
                                    }
                                },
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
                                beginAtZero: true,
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
            const searchInput = document.getElementById('referralSearch');
            const statusFilter = document.getElementById('statusFilter');
            const referralRows = document.querySelectorAll('.referral-row');

            searchInput.addEventListener('input', function () {
                const searchTerm = this.value.toLowerCase();

                referralRows.forEach(row => {
                    const name = row.dataset.name.toLowerCase();
                    const email = row.dataset.email.toLowerCase();

                    if (name.includes(searchTerm) || email.includes(searchTerm)) {
                        row.style.display = '';
                        row.style.animation = 'fadeIn 0.3s ease-out';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            statusFilter.addEventListener('change', function () {
                const status = this.value;

                referralRows.forEach(row => {
                    if (status === '' || row.dataset.status === status) {
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