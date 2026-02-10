@extends('components.layouts.subscriber')

@section('title', 'ROI Simulator')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">ROI Simulator</h1>
        <p class="text-gray-600 mt-1">Calculate potential returns on your investments</p>
    </div>

    <!-- Simulator Form -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Investment Parameters</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Initial Investment -->
            <div>
                <label for="initial-investment" class="block text-sm font-medium text-gray-700 mb-2">
                    Initial Investment (₹)
                </label>
                <div class="flex items-center space-x-3">
                    <input type="range" id="initial-investment" min="1000" max="100000" step="5000" value="5000"
                           class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                           oninput="updateSimulator()">
                    <span id="initial-investment-value" class="text-sm font-medium text-gray-900 min-w-[80px]">
                        ₹5,000
                    </span>
                </div>
            </div>

            <!-- Monthly Investment -->
            <div>
                <label for="monthly-investment" class="block text-sm font-medium text-gray-700 mb-2">
                    Monthly SIP Amount (₹)
                </label>
                <div class="flex items-center space-x-3">
                    <input type="range" id="monthly-investment" min="100" max="10000" step="100" value="1000"
                           class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                           oninput="updateSimulator()">
                    <span id="monthly-investment-value" class="text-sm font-medium text-gray-900 min-w-[80px]">
                        ₹1,000
                    </span>
                </div>
            </div>

            <!-- Investment Duration -->
            <div>
                <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">
                    Investment Duration (Years)
                </label>
                <div class="flex items-center space-x-3">
                    <input type="range" id="duration" min="1" max="20" step="1" value="5"
                           class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                           oninput="updateSimulator()">
                    <span id="duration-value" class="text-sm font-medium text-gray-900 min-w-[80px]">
                        5 years
                    </span>
                </div>
            </div>

            <!-- Expected ROI -->
            <div>
                <label for="roi" class="block text-sm font-medium text-gray-700 mb-2">
                    Expected Annual ROI (%)
                </label>
                <div class="flex items-center space-x-3">
                    <input type="range" id="roi" min="5" max="30" step="0.5" value="12"
                           class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                           oninput="updateSimulator()">
                    <span id="roi-value" class="text-sm font-medium text-gray-900 min-w-[80px]">
                        12%
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600 mb-1">Total Investment</div>
            <div id="total-investment" class="text-2xl font-bold text-gray-900">₹65,000</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600 mb-1">Estimated Returns</div>
            <div id="estimated-returns" class="text-2xl font-bold text-green-600">₹28,741</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-600 mb-1">Total Value</div>
            <div id="total-value" class="text-2xl font-bold text-blue-600">₹93,741</div>
        </div>
    </div>

    <!-- Charts -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Investment Growth</h2>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Pie Chart -->
            <div>
                <h3 class="text-sm font-medium text-gray-700 mb-4">Investment Breakdown</h3>
                <div class="flex justify-center">
                    <canvas id="pieChart" width="400" height="400"></canvas>
                </div>
            </div>

            <!-- Line Chart -->
            <div>
                <h3 class="text-sm font-medium text-gray-700 mb-4">Growth Over Time</h3>
                <div class="flex justify-center">
                    <canvas id="lineChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Projection Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden mt-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Year-by-Year Projection</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Year
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Investment
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Returns
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Total Value
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="projection-table">
                    <!-- Data will be populated by JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize charts
    initCharts();
    
    // Update simulator with initial values
    updateSimulator();
    
    // Add event listeners to range inputs
    document.getElementById('initial-investment').addEventListener('input', updateSimulator);
    document.getElementById('monthly-investment').addEventListener('input', updateSimulator);
    document.getElementById('duration').addEventListener('input', updateSimulator);
    document.getElementById('roi').addEventListener('input', updateSimulator);
});

function updateSimulator() {
    // Get input values
    const initialInvestment = parseInt(document.getElementById('initial-investment').value);
    const monthlyInvestment = parseInt(document.getElementById('monthly-investment').value);
    const durationYears = parseInt(document.getElementById('duration').value);
    const annualRoi = parseFloat(document.getElementById('roi').value);
    
    // Update input value displays
    document.getElementById('initial-investment-value').textContent = `₹${initialInvestment.toLocaleString()}`;
    document.getElementById('monthly-investment-value').textContent = `₹${monthlyInvestment.toLocaleString()}`;
    document.getElementById('duration-value').textContent = `${durationYears} years`;
    document.getElementById('roi-value').textContent = `${annualRoi}%`;
    
    // Calculate investment projection
    const projection = calculateInvestmentProjection(initialInvestment, monthlyInvestment, durationYears, annualRoi);
    
    // Update results
    document.getElementById('total-investment').textContent = `₹${projection.totalInvestment.toLocaleString()}`;
    document.getElementById('estimated-returns').textContent = `₹${projection.totalReturns.toLocaleString()}`;
    document.getElementById('total-value').textContent = `₹${projection.finalAmount.toLocaleString()}`;
    
    // Update charts
    updateCharts(projection);
    
    // Update projection table
    updateProjectionTable(projection.yearlyData);
}

function calculateInvestmentProjection(initialInvestment, monthlyInvestment, durationYears, annualRoi) {
    const monthlyRoi = annualRoi / 100 / 12;
    const totalMonths = durationYears * 12;
    let currentAmount = initialInvestment;
    let totalInvested = initialInvestment;
    const yearlyData = [];
    
    for (let month = 1; month <= totalMonths; month++) {
        // Add monthly investment
        currentAmount += monthlyInvestment;
        totalInvested += monthlyInvestment;
        
        // Apply monthly ROI
        currentAmount *= (1 + monthlyRoi);
        
        // Record yearly data
        if (month % 12 === 0) {
            const year = month / 12;
            const yearInvestment = initialInvestment + (monthlyInvestment * month);
            const yearReturns = currentAmount - yearInvestment;
            
            yearlyData.push({
                year: year,
                investment: yearInvestment,
                returns: yearReturns,
                total: currentAmount
            });
        }
    }
    
    return {
        totalInvestment: totalInvested,
        totalReturns: currentAmount - totalInvested,
        finalAmount: currentAmount,
        yearlyData: yearlyData
    };
}

function initCharts() {
    // Pie Chart
    const pieCtx = document.getElementById('pieChart').getContext('2d');
    window.pieChart = new Chart(pieCtx, {
        type: 'doughnut',
        data: {
            labels: ['Investment', 'Returns'],
            datasets: [{
                data: [65000, 28741],
                backgroundColor: ['#1E40AF', '#10B981'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        font: {
                            size: 12
                        }
                    }
                }
            },
            cutout: '70%'
        }
    });
    
    // Line Chart
    const lineCtx = document.getElementById('lineChart').getContext('2d');
    window.lineChart = new Chart(lineCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [
                {
                    label: 'Investment',
                    data: [],
                    borderColor: '#1E40AF',
                    backgroundColor: 'rgba(30, 64, 175, 0.1)',
                    tension: 0.4
                },
                {
                    label: 'Total Value',
                    data: [],
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        padding: 20,
                        font: {
                            size: 12
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₹' + (value / 1000) + 'k';
                        }
                    }
                },
                x: {
                    ticks: {
                        maxTicksLimit: 10
                    }
                }
            }
        }
    });
}

function updateCharts(projection) {
    // Update pie chart
    window.pieChart.data.datasets[0].data = [projection.totalInvestment, projection.totalReturns];
    window.pieChart.update();
    
    // Update line chart
    const labels = projection.yearlyData.map(item => item.year);
    const investmentData = projection.yearlyData.map(item => item.investment);
    const totalValueData = projection.yearlyData.map(item => item.total);
    
    window.lineChart.data.labels = labels;
    window.lineChart.data.datasets[0].data = investmentData;
    window.lineChart.data.datasets[1].data = totalValueData;
    window.lineChart.update();
}

function updateProjectionTable(yearlyData) {
    const tableBody = document.getElementById('projection-table');
    tableBody.innerHTML = '';
    
    yearlyData.forEach(item => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${item.year}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₹${item.investment.toLocaleString()}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-green-600">₹${item.returns.toLocaleString()}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">₹${item.total.toLocaleString()}</td>
        `;
        tableBody.appendChild(row);
    });
}
</script>
@endpush
