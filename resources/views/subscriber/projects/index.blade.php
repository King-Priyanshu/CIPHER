<x-layouts.subscriber>
    <x-slot:title>
        Projects
    </x-slot:title>

    <div class="space-y-8">

        {{-- Subscription Status Banner --}}
        @if($activeSubscription)
            <div class="bg-gradient-to-r from-navy to-slate-800 rounded-2xl p-6 text-white">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <div class="w-2.5 h-2.5 bg-emerald-400 rounded-full animate-pulse"></div>
                            <span class="text-emerald-300 text-sm font-semibold uppercase tracking-wider">Active
                                Subscriber</span>
                        </div>
                        <h2 class="text-2xl font-bold">{{ $activeSubscription->plan->name ?? 'Subscription' }}</h2>
                        <p class="text-slate-300 text-sm mt-1">
                            ₹{{ number_format((float) ($activeSubscription->amount ?? 0), 0) }}/month
                            · Next billing {{ $activeSubscription->ends_at?->format('M d, Y') ?? 'N/A' }}
                        </p>
                    </div>
                    <a href="{{ route('subscriber.subscription.index') }}"
                        class="px-5 py-2.5 bg-white/10 hover:bg-white/20 border border-white/20 rounded-xl text-sm font-semibold transition text-center">
                        Manage Plan
                    </a>
                </div>
            </div>

            {{-- Investment Statistics Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="card p-5">
                    <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Total Invested</p>
                    <p class="text-2xl font-bold text-navy">₹{{ number_format((float) $totalInvested, 0) }}</p>
                </div>
                <div class="card p-5">
                    <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Total Profits</p>
                    <p class="text-2xl font-bold text-emerald-600">₹{{ number_format((float) $totalProfits, 0) }}</p>
                </div>
                <div class="card p-5">
                    <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Live Projects</p>
                    <p class="text-2xl font-bold text-navy">{{ $liveProjectCount }}</p>
                </div>
                <div class="card p-5">
                    <p class="text-xs text-slate-400 uppercase tracking-wider font-semibold mb-1">Active Investments</p>
                    <p class="text-2xl font-bold text-navy">{{ $myInvestments->count() }}</p>
                </div>
            </div>

            {{-- Search and Filters --}}
            <div class="card p-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400" width="18"
                                height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" placeholder="Search projects by name or description..."
                                class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition"
                                id="searchInput" />
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <select
                            class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition"
                            id="categoryFilter">
                            <option value="">All Categories</option>
                            <option value="tech">Technology</option>
                            <option value="finance">Finance</option>
                            <option value="health">Healthcare</option>
                            <option value="education">Education</option>
                            <option value="sustainability">Sustainability</option>
                        </select>
                        <select
                            class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition"
                            id="statusFilter">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="upcoming">Upcoming</option>
                            <option value="completed">Completed</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- My Invested Projects (detailed) --}}
            @if($projectStats->isNotEmpty())
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-navy">My Investments</h3>
                        <a href="{{ route('subscriber.investments.index') }}"
                            class="text-sm font-semibold text-teal hover:underline">View All Transactions →</a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5" id="myInvestmentsGrid">
                        @foreach($projectStats as $stat)
                            <a href="{{ route('subscriber.projects.show', $stat->project) }}"
                                class="card p-6 group hover:border-teal/50 transition-all project-card"
                                data-title="{{ $stat->project->title }}" data-description="{{ $stat->project->description }}">
                                <div class="flex items-start gap-4">
                                    <div
                                        class="w-12 h-12 bg-teal/10 rounded-xl flex items-center justify-center text-teal group-hover:bg-teal group-hover:text-white transition shrink-0">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-bold text-navy text-lg leading-tight truncate">{{ $stat->project->title }}
                                        </h4>
                                        <p class="text-sm text-slate-500 line-clamp-1 mt-0.5">{{ $stat->project->description }}</p>

                                        <div class="grid grid-cols-3 gap-3 mt-4">
                                            <div>
                                                <p class="text-xs text-slate-400">Invested</p>
                                                <p class="font-bold text-navy">
                                                    ₹{{ number_format((float) $stat->total_invested, 0) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-400">Profits</p>
                                                <p class="font-bold text-emerald-600">
                                                    ₹{{ number_format((float) $stat->user_profit, 0) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-slate-400">Entries</p>
                                                <p class="font-bold text-navy">{{ $stat->investment_count }}</p>
                                            </div>
                                        </div>

                                        {{-- Funding progress --}}
                                        <div class="mt-3 space-y-1">
                                            <div class="flex justify-between text-xs">
                                                <span class="text-slate-500">Project Funded</span>
                                                <span
                                                    class="font-bold text-teal">{{ number_format(($stat->project->current_fund / max($stat->project->fund_goal, 1)) * 100, 0) }}%</span>
                                            </div>
                                            <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                                <div class="bg-teal h-full rounded-full transition-all"
                                                    style="width: {{ min(($stat->project->current_fund / max($stat->project->fund_goal, 1)) * 100, 100) }}%">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                {{-- Subscribed but no investments yet --}}
                <div class="card p-8 text-center border-dashed border-2 bg-slate-50">
                    <div class="w-16 h-16 bg-teal/10 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-navy mb-1">Start Your First Investment</h3>
                    <p class="text-slate-500 text-sm mb-4">You're subscribed! Choose a project below to begin investing.</p>
                </div>
            @endif

            {{-- Available Projects to invest in --}}
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-navy">Explore Projects</h3>
                    <p class="text-sm text-slate-500">Invest in new opportunities</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="availableProjectsGrid">
                    @forelse($availableProjects as $project)
                        <div class="card p-6 group hover:border-teal/50 transition-all project-card"
                            data-title="{{ $project->title }}" data-description="{{ $project->description }}">
                            <div class="flex items-center gap-3 mb-3">
                                <div
                                    class="w-10 h-10 bg-teal/10 rounded-lg flex items-center justify-center text-teal group-hover:bg-teal group-hover:text-white transition">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <h4 class="font-bold text-navy text-lg leading-tight">{{ $project->title }}</h4>
                            </div>

                            <div class="space-y-3 mb-6">
                                <p class="text-sm text-slate-500 line-clamp-2">{{ $project->description }}</p>

                                <div class="flex gap-4 text-xs">
                                    <div>
                                        <span class="text-slate-400 block">Goal</span>
                                        <span class="font-semibold">₹{{ number_format((float) $project->fund_goal, 0) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-slate-400 block">Min. Invest</span>
                                        <span
                                            class="font-semibold">₹{{ number_format((float) ($project->min_investment ?? 1), 0) }}</span>
                                    </div>
                                </div>

                                <div class="space-y-1">
                                    <div class="flex justify-between text-xs">
                                        <span class="text-slate-500">Funded</span>
                                        <span
                                            class="font-bold text-teal">{{ number_format(($project->current_fund / max($project->fund_goal, 1)) * 100, 0) }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                                        <div class="bg-teal h-full"
                                            style="width: {{ ($project->current_fund / max($project->fund_goal, 1)) * 100 }}%">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <a href="{{ route('subscriber.projects.show', $project) }}"
                                class="block w-full py-2 bg-navy hover:bg-slate-800 text-white text-center font-semibold rounded-lg transition">
                                Invest Now
                            </a>
                        </div>
                    @empty
                        <div class="col-span-full card p-8 text-center bg-slate-50 border-dashed border-2">
                            <p class="text-slate-500">No new projects available at this time.</p>
                        </div>
                    @endforelse
                </div>
            </div>

        @else
            {{-- NOT SUBSCRIBED — Show subscribe CTA + available projects --}}
            <div class="bg-gradient-to-r from-navy to-slate-800 rounded-2xl p-8 text-white text-center">
                <h2 class="text-2xl font-bold mb-2">Subscribe to Start Investing</h2>
                <p class="text-slate-300 mb-6">Choose a monthly plan to unlock project investments and start earning
                    returns.</p>
                <a href="{{ route('subscriber.subscription.index') }}"
                    class="inline-block px-8 py-3 bg-teal hover:bg-teal-600 text-white font-bold rounded-xl transition-all shadow-lg">
                    View Plans →
                </a>
            </div>

            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-navy">Available Projects</h3>
                    <p class="text-sm text-slate-500">Subscribe to invest in these projects</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($availableProjects as $project)
                        <div class="card p-6 opacity-80">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-10 h-10 bg-slate-100 rounded-lg flex items-center justify-center text-slate-400">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <h4 class="font-bold text-navy text-lg leading-tight">{{ $project->title }}</h4>
                            </div>

                            <div class="space-y-3 mb-6">
                                <p class="text-sm text-slate-500 line-clamp-2">{{ $project->description }}</p>

                                <div class="flex gap-4 text-xs">
                                    <div>
                                        <span class="text-slate-400 block">Goal</span>
                                        <span class="font-semibold">₹{{ number_format((float) $project->fund_goal, 0) }}</span>
                                    </div>
                                    <div>
                                        <span class="text-slate-400 block">Funded</span>
                                        <span
                                            class="font-semibold">{{ number_format(($project->current_fund / max($project->fund_goal, 1)) * 100, 0) }}%</span>
                                    </div>
                                </div>
                            </div>

                            <a href="{{ route('subscriber.subscription.index') }}"
                                class="block w-full py-2 bg-slate-200 text-slate-600 text-center font-semibold rounded-lg">
                                Subscribe to Invest
                            </a>
                        </div>
                    @empty
                        <div class="col-span-full card p-8 text-center bg-slate-50 border-dashed border-2">
                            <p class="text-slate-500">No projects available at this time.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const categoryFilter = document.getElementById('categoryFilter');
            const statusFilter = document.getElementById('statusFilter');
            const projectCards = document.querySelectorAll('.project-card');

            // Search functionality
            searchInput.addEventListener('input', function () {
                const searchTerm = this.value.toLowerCase();

                projectCards.forEach(card => {
                    const title = card.dataset.title.toLowerCase();
                    const description = card.dataset.description.toLowerCase();

                    if (title.includes(searchTerm) || description.includes(searchTerm)) {
                        card.style.display = '';
                        card.style.animation = 'fadeIn 0.3s ease-out';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });

            // Category filter
            categoryFilter.addEventListener('change', function () {
                const category = this.value;

                projectCards.forEach(card => {
                    if (category === '' || card.dataset.category === category) {
                        card.style.display = '';
                        card.style.animation = 'fadeIn 0.3s ease-out';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });

            // Status filter
            statusFilter.addEventListener('change', function () {
                const status = this.value;

                projectCards.forEach(card => {
                    if (status === '' || card.dataset.status === status) {
                        card.style.display = '';
                        card.style.animation = 'fadeIn 0.3s ease-out';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    </script>
</x-layouts.subscriber>