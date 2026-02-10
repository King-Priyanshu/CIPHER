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
                        <input 
                            type="text" 
                            value="{{ $referralLink }}" 
                            readonly 
                            class="flex-1 px-4 py-2 border border-slate-300 rounded-lg bg-slate-50 font-mono text-sm"
                        />
                        <button 
                            onclick="copyReferralLink('{{ $referralLink }}')"
                            class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
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

        <!-- Earnings Chart -->
        <div class="card p-6">
            <h3 class="text-lg font-bold text-navy mb-4">Earnings Overview</h3>
            <div class="h-80">
                <canvas id="earningsChart"></canvas>
            </div>
        </div>

        <!-- Recent Referrals -->
        <div class="card overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-white">
                <h3 class="text-lg font-bold text-navy">Recent Referrals</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Date Joined</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @if($recentReferrals->isNotEmpty())
                            @foreach($recentReferrals as $referral)
                                <tr class="hover:bg-slate-50/50 transition">
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
                                    <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <svg class="w-6 h-6 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </div>
                                    <p>No referrals yet.</p>
                                    <p class="text-sm text-slate-500 mt-1">Start sharing your referral link to earn bonuses</p>
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
        document.addEventListener('DOMContentLoaded', function() {
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
                                    label: function(context) {
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
                                    callback: function(value) {
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
        });
    </script>
</x-layouts.subscriber>
