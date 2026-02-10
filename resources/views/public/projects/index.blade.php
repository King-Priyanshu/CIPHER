<x-layouts.public>
    <x-slot:title>
        Latest Investment Projects - CIPHER
    </x-slot:title>

    <!-- Hero Section -->
    <div class="relative py-24 lg:py-32 px-6 overflow-hidden bg-slate-900 border-b border-white/5">
        <div class="absolute inset-0 bg-[url('/images/hero-bg.jpg')] bg-cover bg-center opacity-20 filter blur-xl"></div>
        <div class="relative z-10 max-w-7xl mx-auto text-center space-y-6 animate-reveal-blur" style="animation-delay: 0.1s">
            <span class="inline-block px-4 py-1.5 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-xs font-bold uppercase tracking-wider mb-2">
                Curated Opportunities
            </span>
            <h1 class="text-4xl lg:text-6xl font-black text-white tracking-tight leading-tight">
                Discover Premium <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-400 to-indigo-500">Investments</span>
            </h1>
            <p class="text-xl text-slate-400 max-w-2xl mx-auto leading-relaxed">
                Access exclusive high-yield projects vetted by our community experts. From real estate to DeFi, grow your portfolio with confidence.
            </p>
        </div>
    </div>

    <!-- Projects Grid -->
    <div class="max-w-7xl mx-auto px-6 py-20">
        
        <!-- Filters (Mock) -->
        <div class="flex flex-wrap gap-4 mb-12 justify-center animate-reveal-blur" style="animation-delay: 0.2s">
            <button class="px-5 py-2.5 bg-teal-500 text-slate-900 font-bold rounded-full shadow-lg shadow-teal-500/20 hover:scale-105 transition-transform active:scale-95">
                All Projects
            </button>
            <button class="px-5 py-2.5 bg-slate-800 text-slate-400 font-medium rounded-full hover:bg-slate-700 hover:text-white transition-colors">
                Real Estate
            </button>
            <button class="px-5 py-2.5 bg-slate-800 text-slate-400 font-medium rounded-full hover:bg-slate-700 hover:text-white transition-colors">
                Crypto / DeFi
            </button>
            <button class="px-5 py-2.5 bg-slate-800 text-slate-400 font-medium rounded-full hover:bg-slate-700 hover:text-white transition-colors">
                Startups
            </button>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 stagger-3d reveal-trigger">
            @forelse($projects as $project)
                <div class="group relative bg-slate-800 rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all hover:-translate-y-2 border border-white/5">
                    <!-- Image -->
                    <div class="relative h-64 overflow-hidden">
                        <img src="{{ $project->image_url ?? 'https://via.placeholder.com/800x600?text=Project+Image' }}" alt="{{ $project->title }}" class="w-full h-full object-cover transform group-hover:scale-110 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-transparent to-transparent opacity-80"></div>
                        
                        <!-- Badges -->
                        <div class="absolute top-4 left-4 flex gap-2">
                             <span class="px-3 py-1 bg-black/50 backdrop-blur-md rounded-full text-xs font-bold text-white border border-white/10 uppercase tracking-wider">
                                {{ $project->category ?? 'Invest' }}
                            </span>
                        </div>
                        
                        <div class="absolute bottom-4 left-4 right-4 flex justify-between items-end">
                            <div>
                                <h3 class="text-xl font-bold text-white mb-1 group-hover:text-teal-400 transition-colors">{{ $project->title }}</h3>
                                <p class="text-sm text-slate-300 truncate w-64">{{ $project->location ?? 'Global' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Details -->
                    <div class="p-6 space-y-6">
                        <p class="text-slate-400 text-sm line-clamp-2 leading-relaxed h-10">
                            {{ Str::limit($project->description, 100) }}
                        </p>

                        <!-- Progress -->
                        <div class="space-y-2">
                            <div class="flex justify-between text-xs font-bold text-slate-400 uppercase tracking-wider">
                                <span> funded</span>
                                <span class="text-white">{{ number_format(($project->current_fund / max($project->fund_goal, 1)) * 100, 0) }}%</span>
                            </div>
                            <div class="w-full h-2 bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-teal-500 to-emerald-400 rounded-full" style="width: {{ min(($project->current_fund / max($project->fund_goal, 1)) * 100, 100) }}%"></div>
                            </div>
                            <div class="flex justify-between text-xs font-medium text-slate-500 pt-1">
                                <span>Raised: <span class="text-white">₹{{ number_format($project->current_fund) }}</span></span>
                                <span>Goal: <span class="text-slate-400">₹{{ number_format($project->fund_goal) }}</span></span>
                            </div>
                        </div>

                        <!-- Stats Grid -->
                        <div class="grid grid-cols-2 gap-4 pt-4 border-t border-white/5">
                            <div>
                                <div class="text-xs text-slate-500 uppercase font-bold tracking-wider mb-1">ROI</div>
                                <div class="text-lg font-black text-teal-400">{{ $project->roi_percentage ?? '12-18' }}%</div>
                            </div>
                            <div>
                                <div class="text-xs text-slate-500 uppercase font-bold tracking-wider mb-1">Duration</div>
                                <div class="text-lg font-black text-white">{{ $project->duration_months ?? 12 }} Mo.</div>
                            </div>
                        </div>

                        <!-- Action -->
                        <a href="{{ route('projects.show', $project->id) }}" class="block w-full py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-center rounded-xl transition-colors shadow-lg shadow-indigo-500/20 group-hover:shadow-indigo-500/40">
                            View Details
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-20">
                    <div class="w-20 h-20 bg-slate-800 rounded-full flex items-center justify-center mx-auto mb-6 border border-white/5">
                        <svg class="w-10 h-10 text-slate-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-white mb-2">No Projects Yet</h3>
                    <p class="text-slate-400">Check back soon for new investment opportunities.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-12 text-center">
            {{ $projects->links() }}
        </div>
    </div>
</x-layouts.public>
