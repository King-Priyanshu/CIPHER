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
                                <h3 class="text-xl font-bold text-navy">{{ $project->title }}</h3>
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
                        <div class="w-full md:w-64 bg-slate-50 p-4 rounded-xl border border-gray-100">
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-slate-500">Funded</span>
                                <span class="font-bold text-navy">{{ number_format(($project->current_fund / max($project->fund_goal, 1)) * 100, 0) }}%</span>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-2 mb-3">
                                <div class="bg-teal h-2 rounded-full" style="width: {{ min(($project->current_fund / max($project->fund_goal, 1)) * 100, 100) }}%"></div>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-500">Goal: ${{ number_format($project->fund_goal, 0) }}</span>
                                <span class="text-navy font-semibold">${{ number_format($project->current_fund, 0) }} raised</span>
                            </div>
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
