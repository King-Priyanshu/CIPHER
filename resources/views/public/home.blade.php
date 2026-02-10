<x-layouts.public>
    <x-slot:title>
        CIPHER - Invest Together, Grow Wealth
    </x-slot:title>

    <!-- Hero Section -->
    <div class="relative min-h-screen flex items-center justify-center overflow-hidden">
        <!-- Background Effects -->
        <div class="absolute inset-0 z-0">
            <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-slate-900 via-[#0F172A] to-[#0A2540]"></div>
            <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-teal-500/10 rounded-full blur-[100px] animate-float-premium"></div>
            <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-indigo-500/20 rounded-full blur-[100px] animate-float-premium" style="animation-delay: -2s"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center py-20">
            <!-- Text Content -->
            <div class="space-y-8 text-center lg:text-left animate-reveal-blur">
                <div>
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10 backdrop-blur-md mb-6">
                        <span class="w-2 h-2 rounded-full bg-teal-400 animate-pulse"></span>
                        <span class="text-teal-400 text-sm font-medium tracking-wide">Next-Gen Investment DAO</span>
                    </div>
                    
                    <h1 class="text-5xl lg:text-7xl font-black text-white leading-tight tracking-tight">
                        Invest <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-400 to-cyan-400">Together</span>,<br />
                        Grow <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">Wealth</span>
                    </h1>
                </div>

                <p class="text-xl text-slate-300 max-w-xl mx-auto lg:mx-0 leading-relaxed">
                    Join a vetted community of {{ count($projects) * 400 + 1200 }}+ members pooling resources into high-yield real-world projects. Transparency at every step.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="{{ route('register') }}" class="px-8 py-4 bg-teal-500 hover:bg-teal-400 text-slate-900 font-bold rounded-xl transition-all hover:scale-105 shadow-lg shadow-teal-500/20">
                        Get Started
                    </a>
                    <a href="#projects" class="px-8 py-4 bg-white/5 border border-white/10 text-white font-bold rounded-xl hover:bg-white/10 transition-all">
                        Browse Projects
                    </a>
                </div>
            </div>

            <!-- Visual / Card Stack -->
            <div class="hidden lg:block animate-reveal-3d">
                <div class="relative bg-slate-800/50 backdrop-blur-xl border border-white/10 p-8 rounded-3xl shadow-2xl">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <div class="text-slate-400 text-sm font-medium">Community Assets</div>
                            <div class="text-4xl font-black text-white mt-1">₹{{ number_format(1450000) }}</div>
                        </div>
                        <div class="w-12 h-12 bg-teal-500/20 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                        </div>
                    </div>
                    <!-- Mini Graph -->
                    <div class="h-32 flex items-end gap-2 mb-8">
                        @foreach([30, 50, 40, 70, 60, 90, 80, 100] as $h)
                            <div class="flex-1 bg-teal-500/20 hover:bg-teal-400 transition-all rounded-t-lg" style="height: {{ $h }}%"></div>
                        @endforeach
                    </div>
                    <div class="space-y-3">
                        <div class="p-4 bg-white/5 rounded-xl border border-white/5 flex justify-between">
                            <span class="text-slate-300">Active Projects</span>
                            <span class="text-white font-bold">12</span>
                        </div>
                        <div class="p-4 bg-white/5 rounded-xl border border-white/5 flex justify-between">
                            <span class="text-slate-300">Avg. Annual ROI</span>
                            <span class="text-teal-400 font-bold">14.2%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Projects -->
    <section id="projects" class="py-32 bg-slate-950">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-20 animate-reveal-blur">
                <span class="text-indigo-400 font-bold tracking-widest uppercase text-sm mb-2 block">Live Opportunities</span>
                <h2 class="text-4xl lg:text-5xl font-black text-white mb-6">Invest in Vetted Projects</h2>
                <div class="h-1.5 w-24 bg-gradient-to-r from-teal-400 to-indigo-500 mx-auto rounded-full"></div>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                @forelse($projects as $project)
                    <div class="bg-slate-900 rounded-3xl overflow-hidden border border-white/5 group hover:border-teal-500/30 transition-all duration-500 flex flex-col h-full">
                        <div class="relative h-56 overflow-hidden">
                            <img src="{{ $project->image_url ?? 'https://images.unsplash.com/photo-1560179707-f14e90ef3623?auto=format&fit=crop&w=800' }}" alt="{{ $project->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900 to-transparent"></div>
                            <div class="absolute bottom-4 left-4">
                                <span class="bg-teal-500 text-slate-900 text-[10px] font-black uppercase px-2 py-1 rounded">Featured</span>
                            </div>
                        </div>
                        <div class="p-6 flex-1 flex flex-col bg-slate-800/50">
                            <h3 class="text-xl font-bold text-white mb-2 group-hover:text-teal-400 transition-colors">{{ $project->title }}</h3>
                            <p class="text-slate-400 text-sm mb-6 line-clamp-2">{{ $project->description }}</p>
                            
                            <div class="grid grid-cols-2 gap-4 mb-6 mt-auto">
                                <div>
                                    <p class="text-[10px] text-slate-500 uppercase font-bold tracking-widest mb-1">Target ROI</p>
                                    <p class="text-lg font-black text-teal-400">{{ $project->roi_percentage ?? '12-15' }}%</p>
                                </div>
                                <div>
                                    <p class="text-[10px] text-slate-500 uppercase font-bold tracking-widest mb-1">Duration</p>
                                    <p class="text-lg font-black text-white">{{ $project->duration_months ?? 12 }} Mo</p>
                                </div>
                            </div>

                            <a href="{{ route('projects.show', $project->id) }}" class="w-full py-3 bg-white/5 hover:bg-white/10 text-white font-bold text-center rounded-xl border border-white/10 transition-all">
                                View Analysis
                            </a>
                        </div>
                    </div>
                @empty
                    <!-- Mock Data if none exists -->
                     @foreach([1,2,3] as $i)
                        <div class="bg-slate-900 rounded-3xl overflow-hidden border border-white/5 flex flex-col h-full opacity-60">
                             <div class="h-56 bg-slate-800 animate-pulse"></div>
                             <div class="p-6 space-y-4">
                                 <div class="h-6 w-2/3 bg-slate-800 rounded"></div>
                                 <div class="h-12 w-full bg-slate-800 rounded"></div>
                                 <div class="grid grid-cols-2 gap-4">
                                     <div class="h-8 bg-slate-800 rounded"></div>
                                     <div class="h-8 bg-slate-800 rounded"></div>
                                 </div>
                             </div>
                        </div>
                     @endforeach
                @endforelse
            </div>

            <div class="mt-16 text-center">
                <a href="{{ route('projects.index') }}" class="text-indigo-400 font-bold hover:text-indigo-300 transition flex items-center justify-center gap-2 group">
                    View All Active Opportunities 
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-32 bg-slate-900">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-20 animate-reveal-blur">
                <h2 class="text-3xl md:text-5xl font-black text-white mb-6">Choose Your Membership</h2>
                <p class="text-slate-400 max-w-2xl mx-auto">Join the pool that fits your growth goals.</p>
            </div>

            <div class="grid lg:grid-cols-3 gap-8 items-start">
                @foreach($plans as $plan)
                    <div class="bg-slate-800/50 backdrop-blur-md rounded-3xl p-8 border {{ $plan->slug === 'growth' ? 'border-teal-500 shadow-teal-500/10 shadow-2xl relative' : 'border-white/5' }}">
                        @if($plan->slug === 'growth')
                            <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-teal-500 text-slate-900 text-[10px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest">
                                Most Popular
                            </div>
                        @endif

                        <h3 class="text-2xl font-bold text-white mb-2">{{ $plan->name }}</h3>
                        <p class="text-slate-400 text-sm mb-8">{{ $plan->description }}</p>

                        <div class="flex items-baseline gap-1 mb-8">
                            <span class="text-5xl font-black text-white">₹{{ number_format($plan->price, 0) }}</span>
                            <span class="text-slate-500 text-sm font-bold">/{{ $plan->interval }}</span>
                        </div>

                        <a href="{{ route('checkout.show', $plan->slug) }}" class="block w-full py-4 rounded-2xl font-black text-center transition-all {{ $plan->slug === 'growth' ? 'bg-teal-500 text-slate-900 hover:bg-teal-400' : 'bg-white/5 text-white hover:bg-white/10' }}">
                            Get Started
                        </a>

                        <ul class="mt-10 space-y-4 text-sm text-slate-300">
                            @foreach(['Access to Dashboard', 'Community Pool Entry', 'Monthly Dividends'] as $feature)
                            <li class="flex items-center gap-3">
                                <svg class="w-5 h-5 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>{{ $feature }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

</x-layouts.public>
