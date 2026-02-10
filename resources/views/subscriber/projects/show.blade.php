<x-layouts.subscriber>
    <x-slot:title>
        {{ $project->title }}
    </x-slot:title>

    <div class="max-w-5xl mx-auto space-y-8">
        
        <!-- Header / Hero Section -->
        <div class="relative bg-navy-900 rounded-3xl overflow-hidden shadow-2xl">
            <!-- Background Image with Overlay -->
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ $project->image_url ?? asset('images/project-placeholder.jpg') }}'); opacity: 0.3;"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-navy-900 via-navy-900/80 to-transparent"></div>

            <div class="relative z-10 p-8 lg:p-12 text-white">
                <div class="flex flex-wrap items-center gap-4 mb-4">
                    <span class="bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">
                        {{ $project->category ?? 'Investment' }}
                    </span>
                    <span class="flex items-center gap-1.5 {{ $project->status === 'active' ? 'text-emerald-400' : 'text-slate-400' }} font-semibold text-sm">
                        <span class="w-2 h-2 rounded-full {{ $project->status === 'active' ? 'bg-emerald-400' : 'bg-slate-400' }}"></span>
                        {{ ucfirst($project->status) }}
                    </span>
                </div>

                <h1 class="text-4xl lg:text-5xl font-black tracking-tight mb-4">{{ $project->title }}</h1>
                <p class="text-lg text-slate-300 max-w-2xl leading-relaxed">{{ Str::limit($project->description, 200) }}</p>

                <div class="mt-8 flex flex-wrap gap-6">
                    <div>
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Fund Goal</p>
                        <p class="text-2xl font-bold">₹{{ number_format($project->fund_goal) }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Raised So Far</p>
                        <p class="text-2xl font-bold text-emerald-400">₹{{ number_format($project->current_fund) }}</p>
                    </div>
                    <div>
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Investors</p>
                        <p class="text-2xl font-bold">{{ $investorCount ?? 0 }}</p>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="mt-8 w-full bg-slate-700/50 rounded-full h-3 max-w-xl">
                    <div class="bg-gradient-to-r from-indigo-500 to-emerald-400 h-3 rounded-full" style="width: {{ min(100, ($project->current_fund / $project->fund_goal) * 100) }}%"></div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Column: Details -->
            <div class="lg:col-span-2 space-y-8">
                <!-- About -->
                <div class="card p-8">
                    <h3 class="text-xl font-bold text-navy mb-4">About Project</h3>
                    <div class="prose prose-slate max-w-none text-slate-600">
                        {!! nl2br(e($project->description)) !!}
                    </div>
                </div>

                <!-- Financial Model -->
                <div class="card p-8">
                    <h3 class="text-xl font-bold text-navy mb-4">Financial Overview</h3>
                    <div class="grid grid-cols-2 gap-6">
                        <div class="p-4 bg-slate-50 rounded-xl">
                            <p class="text-sm text-slate-500 mb-1">Expected ROI</p>
                            <p class="text-lg font-bold text-navy">{{ $project->roi_percentage ?? '12-18' }}%</p>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-xl">
                            <p class="text-sm text-slate-500 mb-1">Duration</p>
                            <p class="text-lg font-bold text-navy">{{ $project->duration_months ?? 12 }} Months</p>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-xl">
                            <p class="text-sm text-slate-500 mb-1">Min Investment</p>
                            <p class="text-lg font-bold text-navy">₹{{ number_format($project->min_investment ?? 5000) }}</p>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-xl">
                            <p class="text-sm text-slate-500 mb-1">Risk Level</p>
                            <p class="text-lg font-bold text-navy">{{ $project->risk_level ?? 'Moderate' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Investment Action -->
            <div class="space-y-6">
                
                @if($project->status === 'active')
                <div class="card p-6 border-2 border-indigo-600 ring-4 ring-indigo-50">
                    <h3 class="text-xl font-bold text-navy mb-2">Invest Now</h3>
                    <p class="text-sm text-slate-500 mb-6">Secure your stake in this project today.</p>

                    <form action="{{ route('subscriber.projects.invest', $project->id) }}" method="POST" class="space-y-4">
                        @csrf
                        
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Investment Amount (₹)</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 font-bold">₹</span>
                                <input type="number" name="amount" min="{{ $project->min_investment ?? 1000 }}" 
                                       class="input pl-8 w-full font-bold text-lg" 
                                       placeholder="5000" required>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">Minimum: ₹{{ number_format($project->min_investment ?? 1000) }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Select Plan</label>
                            <select name="plan_id" class="input w-full" required>
                                <option value="">Select a Plan...</option>
                                @foreach($plans as $plan)
                                    <option value="{{ $plan->id }}">{{ $plan->name }} ({{ $plan->roi }}% ROI)</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="bg-indigo-50 p-3 rounded-lg text-sm text-indigo-700">
                            <div class="flex justify-between items-center mb-1">
                                <span>Wallet Balance:</span>
                                <span class="font-bold">₹{{ number_format(auth()->user()->wallet_balance ?? 0) }}</span>
                            </div>
                            @if((auth()->user()->wallet_balance ?? 0) < ($project->min_investment ?? 1000))
                                <p class="text-xs text-red-500 mt-1">Insufficient funds. <a href="{{ route('subscriber.deposit.create') }}" class="underline font-bold">Add Money</a></p>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-navy w-full py-3 text-lg shadow-lg shadow-indigo-500/30">
                            Confirm Investment
                        </button>
                    </form>
                </div>
                @else
                <div class="card p-6 bg-slate-50 text-center">
                    <div class="w-16 h-16 bg-slate-200 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-600">Investment Closed</h3>
                    <p class="text-slate-500 text-sm mt-2">This project is not currently accepting new investments.</p>
                </div>
                @endif

                <!-- Documents -->
                <div class="card p-6">
                    <h3 class="font-bold text-navy mb-4">Documents</h3>
                    <ul class="space-y-3">
                        @forelse($project->documents ?? [] as $doc)
                            <li>
                                <a href="{{ Storage::url($doc) }}" target="_blank" class="flex items-center gap-3 p-3 rounded-lg border border-slate-100 hover:bg-slate-50 transition group">
                                    <div class="w-10 h-10 rounded bg-red-50 flex items-center justify-center text-red-500 group-hover:bg-red-100 transition">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 2H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-slate-700 truncate">Document.pdf</p> <!-- Placeholder name -->
                                        <p class="text-xs text-slate-400">View File</p>
                                    </div>
                                </a>
                            </li>
                        @empty
                            <p class="text-sm text-slate-400 italic">No public documents available.</p>
                        @endforelse
                    </ul>
                </div>

            </div>
        </div>
    </div>
</x-layouts.subscriber>
