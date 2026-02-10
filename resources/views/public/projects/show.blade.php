<x-layouts.public>
    <x-slot:title>
        {{ $project->title }} - CIPHER
    </x-slot:title>

    <div class="max-w-7xl mx-auto px-6 py-20">
        
        <!-- Header / Hero Section -->
        <div class="relative bg-navy-900 rounded-3xl overflow-hidden shadow-2xl mb-12 animate-reveal-blur" style="animation-delay: 0.1s">
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0 bg-cover bg-center opacity-40 filter blur-sm transform scale-105" style="background-image: url('{{ $project->image_url ?? asset('images/project-placeholder.jpg') }}');"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 via-slate-900/80 to-transparent"></div>

            <div class="relative z-10 p-8 lg:p-12 text-white">
                <a href="{{ route('projects.index') }}" class="inline-flex items-center gap-2 mb-6 text-slate-300 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Projects
                </a>

                <div class="flex flex-wrap items-center gap-4 mb-4">
                    <span class="bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                        {{ $project->category ?? 'Investment' }}
                    </span>
                    <span class="flex items-center gap-1.5 {{ $project->status === 'active' ? 'text-emerald-400' : 'text-slate-400' }} font-semibold text-sm">
                        <span class="w-2 h-2 rounded-full {{ $project->status === 'active' ? 'bg-emerald-400' : 'bg-slate-400' }}"></span>
                        {{ ucfirst($project->status) }}
                    </span>
                </div>

                <h1 class="text-4xl lg:text-6xl font-black tracking-tight mb-4">{{ $project->title }}</h1>
                <p class="text-lg text-slate-300 max-w-2xl leading-relaxed">{{ Str::limit($project->description, 300) }}</p>

                <div class="mt-8 flex flex-wrap gap-8">
                    <div>
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-1">Fund Goal</p>
                        <p class="text-3xl font-black text-white">₹{{ number_format($project->fund_goal) }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-1">Raised So Far</p>
                        <p class="text-3xl font-black text-emerald-400">₹{{ number_format($project->current_fund) }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-1">Investors</p>
                        <p class="text-3xl font-black text-indigo-400">{{ $investorCount ?? 0 }}</p>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mt-8 w-full bg-slate-700/50 rounded-full h-4 max-w-xl overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-emerald-400 h-full rounded-full animate-width" style="width: {{ min(100, ($project->current_fund / $project->fund_goal) * 100) }}%"></div>
                </div>
            </div>
        </div>

        <div class="grid lg:grid-cols-3 gap-12">
            
            <!-- Left Column: Details -->
            <div class="lg:col-span-2 space-y-12 animate-reveal-blur" style="animation-delay: 0.2s">
                <!-- About -->
                <div class="card p-8 bg-slate-800 border-white/5 shadow-xl rounded-3xl">
                    <h3 class="text-2xl font-bold text-white mb-6 border-b border-white/5 pb-4">Detailed Analysis</h3>
                    <div class="prose prose-invert max-w-none text-slate-400 leading-relaxed">
                        {!! nl2br(e($project->description)) !!}
                    </div>

                    <!-- Additional Details (Mock) -->
                    <div class="mt-8 pt-8 border-t border-white/5 grid md:grid-cols-2 gap-8">
                        <div>
                            <h4 class="font-bold text-white mb-2">Location</h4>
                            <p class="text-slate-400">{{ $project->location ?? 'Global / Hybrid' }}</p>
                        </div>
                        <div>
                            <h4 class="font-bold text-white mb-2">Launch Date</h4>
                            <p class="text-slate-400">{{ $project->created_at->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <h4 class="font-bold text-white mb-2">Risk Factor</h4>
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full {{ $project->risk_level === 'High' ? 'bg-red-500' : ($project->risk_level === 'Medium' ? 'bg-amber-500' : 'bg-emerald-500') }}"></span>
                                <span class="text-slate-400">{{ $project->risk_level ?? 'Moderate' }}</span>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-bold text-white mb-2">Audit Status</h4>
                            <span class="text-emerald-400 font-medium">Verified by TechAudit</span>
                        </div>
                    </div>
                </div>

                <!-- Financial Model -->
                <div class="card p-8 bg-slate-800 border-white/5 shadow-xl rounded-3xl">
                    <h3 class="text-2xl font-bold text-white mb-6 border-b border-white/5 pb-4">Financial Projections</h3>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6">
                        <div class="p-5 bg-slate-900/50 rounded-2xl border border-white/5 hover:border-teal-500/30 transition-colors group">
                            <p class="text-xs text-slate-500 mb-2 uppercase font-bold tracking-wider">ROI</p>
                            <p class="text-2xl font-black text-teal-400 group-hover:scale-105 transition-transform">{{ $project->roi_percentage ?? '12-18' }}%</p>
                        </div>
                        <div class="p-5 bg-slate-900/50 rounded-2xl border border-white/5 hover:border-indigo-500/30 transition-colors group">
                            <p class="text-xs text-slate-500 mb-2 uppercase font-bold tracking-wider">Period</p>
                            <p class="text-2xl font-black text-white group-hover:scale-105 transition-transform">{{ $project->duration_months ?? 12 }} Mo</p>
                        </div>
                        <div class="p-5 bg-slate-900/50 rounded-2xl border border-white/5 hover:border-purple-500/30 transition-colors group">
                            <p class="text-xs text-slate-500 mb-2 uppercase font-bold tracking-wider">Min Entry</p>
                            <p class="text-2xl font-black text-white group-hover:scale-105 transition-transform">₹{{ number_format($project->min_investment ?? 5000) }}</p>
                        </div>
                        <div class="p-5 bg-slate-900/50 rounded-2xl border border-white/5 hover:border-amber-500/30 transition-colors group">
                            <p class="text-xs text-slate-500 mb-2 uppercase font-bold tracking-wider">Payout</p>
                            <p class="text-lg font-black text-white pt-1 group-hover:scale-105 transition-transform">Monthly</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Investment Action -->
            <div class="space-y-8 animate-reveal-blur" style="animation-delay: 0.3s">
                
                @if($project->status === 'active')
                <div class="bg-indigo-600 rounded-3xl p-8 shadow-2xl relative overflow-hidden group">
                    <div class="absolute inset-0 bg-gradient-to-br from-indigo-500 to-purple-600 opacity-90"></div>
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-2xl group-hover:bg-white/20 transition-colors duration-700"></div>
                    
                    <div class="relative z-10 text-center">
                        <h3 class="text-2xl font-black text-white mb-2">Invest Now</h3>
                        <p class="text-indigo-100 text-sm mb-8 leading-relaxed">Join {{ $investorCount }} other investors today.</p>

                        @auth
                            <a href="{{ route('subscriber.projects.show', $project->id) }}" class="block w-full py-4 bg-white text-indigo-600 font-bold rounded-xl hover:bg-indigo-50 transition-all hover:scale-105 shadow-xl active:scale-95 text-lg">
                                Proceed to Invest
                            </a>
                            <p class="text-xs text-indigo-200 mt-4 opacity-80">You are logged in.</p>
                        @else
                            <a href="{{ route('login') }}?returnUrl={{ url()->current() }}" class="block w-full py-4 bg-white text-indigo-600 font-bold rounded-xl hover:bg-indigo-50 transition-all hover:scale-105 shadow-xl active:scale-95 text-lg">
                                Login to Invest
                            </a>
                            <p class="text-xs text-indigo-200 mt-4 opacity-80">Secure Account Required</p>
                        @endauth
                    </div>
                </div>
                @else
                <div class="card p-8 bg-slate-800 text-center border-white/5 rounded-3xl">
                    <div class="w-16 h-16 bg-slate-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-200">Investment Closed</h3>
                    <p class="text-slate-500 text-sm mt-2">This project is fully funded or archived.</p>
                </div>
                @endif

                <!-- Recent Activity (Mock) -->
                @if(isset($recentInvestments) && count($recentInvestments) > 0)
                <div class="bg-slate-800 rounded-3xl p-6 border border-white/5 shadow-xl">
                    <h3 class="font-bold text-white mb-4 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        Recent Activity
                    </h3>
                    <div class="space-y-4">
                        @foreach($recentInvestments as $inv)
                        <div class="flex items-center gap-3 pb-3 border-b border-white/5 last:border-0 last:pb-0">
                            <div class="w-8 h-8 rounded-full bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold text-xs">
                                {{ substr($inv->user->name ?? 'U', 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-medium text-slate-300 truncate">{{ $inv->user->name ?? 'Investor' }}</div>
                                <div class="text-xs text-slate-500">{{ $inv->created_at->diffForHumans() }}</div>
                            </div>
                            <div class="text-sm font-bold text-emerald-400">+₹{{ number_format($inv->amount) }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.public>
