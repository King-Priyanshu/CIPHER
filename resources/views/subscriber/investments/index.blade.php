<x-layouts.subscriber>
    <x-slot:title>My Contributions</x-slot:title>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="card p-6">
            <p class="text-sm font-medium text-slate-500">Total Contributed</p>
            <p class="text-3xl font-bold text-navy mt-1">₹{{ number_format($totalInvested, 0) }}</p>
        </div>
        <div class="card p-6">
            <p class="text-sm font-medium text-slate-500">Active Projects</p>
            <p class="text-3xl font-bold text-teal-600 mt-1">{{ $activeProjects }}</p>
        </div>
        <div class="card p-6">
            <p class="text-sm font-medium text-slate-500">Total Royalties</p>
            <p class="text-3xl font-bold text-green-600 mt-1">₹{{ number_format($totalProfits, 0) }}</p>
        </div>
    </div>

    <!-- Filter and Search Section -->
    <div class="card p-6 mb-8">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex-1">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400" width="18"
                        height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" placeholder="Search investments by project name..."
                        class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition"
                        id="investmentSearch" />
                </div>
            </div>
            <div class="flex gap-2">
                <select
                    class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition"
                    id="statusFilter">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="allocated">Allocated</option>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                </select>
                <select
                    class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition"
                    id="sortBy">
                    <option value="date-desc">Latest First</option>
                    <option value="date-asc">Oldest First</option>
                    <option value="amount-desc">Highest Amount</option>
                    <option value="amount-asc">Lowest Amount</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Contributions by Project -->
    <div class="card overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-navy">My Project Contributions</h3>
            <div class="text-sm text-slate-500">
                Showing {{ $investments->count() }} investments
            </div>
        </div>

        @if($investments->count() > 0)
            <div class="divide-y divide-gray-100" id="investmentTable">
                @foreach($investments as $investment)
                    <div class="p-6 hover:bg-slate-50/50 transition investment-row"
                        data-project="{{ $investment->project->title }}" data-status="{{ $investment->status }}"
                        data-amount="{{ $investment->amount }}" data-date="{{ $investment->created_at->timestamp }}">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h4 class="font-semibold text-navy">{{ $investment->project->title }}</h4>
                                <p class="text-sm text-slate-500 mt-1">{{ Str::limit($investment->project->description, 100) }}
                                </p>
                            </div>
                            <div class="text-right ml-6">
                                <p class="text-lg font-bold text-navy">₹{{ number_format($investment->amount, 0) }}</p>
                                <span
                                    class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $investment->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst($investment->status) }}
                                </span>

                                <!-- Withdrawal disabled: Admin managed -->
                                @if($investment->status === 'allocated')
                                    <span class="text-xs text-slate-400 mt-2 block">Pending Activation</span>
                                @endif
                            </div>
                        </div>
                        <div class="mt-4 flex items-center gap-4 text-sm text-slate-500">
                            <span>Allocated: {{ $investment->allocated_at?->format('M d, Y') }}</span>
                            <span class="text-slate-300">•</span>
                            <span>Share: {{ number_format($investment->share_percentage, 1) }}%</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($investments->lastPage() > 1)
                <div class="px-6 py-4 border-t border-gray-100">
                    <div class="flex items-center justify-center">
                        {{ $investments->links() }}
                    </div>
                </div>
            @endif

        @else
            <div class="p-12 text-center text-slate-400">
                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <p class="font-medium text-slate-600">No contributions yet</p>
                <p class="text-sm mt-1">Your subscription will be automatically allocated to active projects.</p>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('investmentSearch');
            const statusFilter = document.getElementById('statusFilter');
            const sortBy = document.getElementById('sortBy');
            const investmentRows = document.querySelectorAll('.investment-row');

            // Search functionality
            searchInput.addEventListener('input', function () {
                const searchTerm = this.value.toLowerCase();

                investmentRows.forEach(row => {
                    const projectTitle = row.dataset.project.toLowerCase();

                    if (projectTitle.includes(searchTerm)) {
                        row.style.display = '';
                        row.style.animation = 'fadeIn 0.3s ease-out';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            // Status filter
            statusFilter.addEventListener('change', function () {
                const status = this.value;

                investmentRows.forEach(row => {
                    if (status === '' || row.dataset.status === status) {
                        row.style.display = '';
                        row.style.animation = 'fadeIn 0.3s ease-out';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            // Sort functionality
            sortBy.addEventListener('change', function () {
                const sortValue = this.value;
                const container = document.getElementById('investmentTable');
                const rowsArray = Array.from(investmentRows);

                // Sort rows based on selected option
                rowsArray.sort((a, b) => {
                    switch (sortValue) {
                        case 'date-desc':
                            return parseInt(b.dataset.date) - parseInt(a.dataset.date);
                        case 'date-asc':
                            return parseInt(a.dataset.date) - parseInt(b.dataset.date);
                        case 'amount-desc':
                            return parseFloat(b.dataset.amount) - parseFloat(a.dataset.amount);
                        case 'amount-asc':
                            return parseFloat(a.dataset.amount) - parseFloat(b.dataset.amount);
                        default:
                            return 0;
                    }
                });

                // Reorder the DOM elements
                rowsArray.forEach(row => {
                    container.appendChild(row);
                });
            });
        });
    </script>
</x-layouts.subscriber>