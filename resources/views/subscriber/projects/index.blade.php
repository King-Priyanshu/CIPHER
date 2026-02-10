<x-layouts.subscriber>
    <x-slot:title>
        Projects
    </x-slot:title>

    <div class="space-y-6">
        <!-- Header Actions if needed -->
        <!-- <div class="flex justify-end">...</div> -->

        <div class="grid grid-cols-1 gap-6">
            @forelse($projects as $project)
                <div class="card p-6">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-xl font-bold text-navy hover:text-teal-600 transition">
                                    <a href="{{ route('subscriber.projects.show', $project) }}">{{ $project->title }}</a>
                                </h3>
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $project->status === 'active' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-800' }}">
                                    {{ ucfirst($project->status) }}
                                </span>
                            </div>
                            <p class="text-slate-600 mb-4 max-w-2xl">{{ $project->description }}</p>
                            <p class="text-sm text-slate-400">
                                Started on {{ $project->starts_at ? $project->starts_at->format('M d, Y') : 'Pending' }}
                            </p>
                        </div>
                        
                        <!-- Funding Progress -->
                        <div class="w-full md:w-64">
                            <div class="bg-slate-50 p-4 rounded-xl border border-gray-100 mb-4">
                                <div class="flex justify-between text-sm mb-2">
                                    <span class="text-slate-500">Funded</span>
                                    <span class="font-bold text-navy">{{ number_format(($project->current_fund / max($project->fund_goal, 1)) * 100, 0) }}%</span>
                                </div>
                                <div class="w-full bg-slate-200 rounded-full h-2 mb-3">
                                    <div class="bg-teal h-2 rounded-full" style="width: {{ min(($project->current_fund / max($project->fund_goal, 1)) * 100, 100) }}%"></div>
                                </div>
                                <div class="flex justify-between text-xs mb-3">
                                    <span class="text-slate-500">Goal: ₹{{ number_format($project->fund_goal, 0) }}</span>
                                    <span class="text-navy font-semibold">₹{{ number_format($project->current_fund, 0) }} raised</span>
                                </div>

                                {{-- User Allocation Display --}}
                                @php
                                    $userInvestment = $project->investments->sum('amount');
                                @endphp
                                @if($userInvestment > 0)
                                    <div class="pt-3 border-t border-slate-100 flex justify-between items-center">
                                        <span class="text-xs text-slate-500">Your Allocation</span>
                                        <span class="text-sm font-bold text-teal-600">₹{{ number_format($userInvestment, 0) }}</span>
                                    </div>
                                @endif
                            </div>

                            @if($user->participation_mode === 'manual')
                                @if($availableBalance > 0)
                                    <form action="{{ route('subscriber.investments.store') }}" method="POST" class="mt-2 text-center">
                                        @csrf
                                        <input type="hidden" name="project_id" value="{{ $project->id }}">
                                        <!-- For simplicity, allowing to contribute full balance or split? 
                                             Let's make it simple for now: "Contribute ₹X" button or input. 
                                             Given "low-literacy", simpler is better. Maybe preset amounts or "Contribute All Available"?
                                             Let's show an input for flexibility but defaulted to available.
                                        -->
                                        <div class="flex gap-2">
                                            <input type="number" name="amount" min="1" max="{{ $availableBalance }}" value="{{ floor($availableBalance) }}" class="w-full text-sm border-gray-200 rounded-lg" placeholder="Amount">
                                            <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-3 py-2 rounded-lg text-sm font-bold transition">
                                                Contribute
                                            </button>
                                        </div>
                                    </form>
                                @else
                                    <button disabled class="w-full bg-slate-100 text-slate-400 px-4 py-2 rounded-lg text-sm font-medium cursor-not-allowed">
                                        No funds available
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="card p-12 text-center">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-navy">No Projects Found</h3>
                    <p class="text-slate-500 mt-1">Check back later for new investment opportunities.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.subscriber>
