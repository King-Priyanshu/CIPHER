<x-layouts.subscriber>
    <x-slot:title>My Investments</x-slot:title>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="card p-6">
            <p class="text-sm font-medium text-slate-500">Total Invested</p>
            <p class="text-3xl font-bold text-navy mt-1">₹{{ number_format($totalInvested, 0) }}</p>
        </div>
        <div class="card p-6">
            <p class="text-sm font-medium text-slate-500">Active Projects</p>
            <p class="text-3xl font-bold text-teal-600 mt-1">{{ $activeProjects }}</p>
        </div>
        <div class="card p-6">
            <p class="text-sm font-medium text-slate-500">Total Profits</p>
            <p class="text-3xl font-bold text-green-600 mt-1">₹{{ number_format($totalProfits, 0) }}</p>
        </div>
    </div>

    <!-- Investments by Project -->
    <div class="card overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-navy">My Project Investments</h3>
        </div>
        
        @if($investments->count() > 0)
        <div class="divide-y divide-gray-100">
            @foreach($investments as $investment)
            <div class="p-6 hover:bg-slate-50/50 transition">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <h4 class="font-semibold text-navy">{{ $investment->project->title }}</h4>
                        <p class="text-sm text-slate-500 mt-1">{{ Str::limit($investment->project->description, 100) }}</p>
                    </div>
                    <div class="text-right ml-6">
                        <p class="text-lg font-bold text-navy">₹{{ number_format($investment->amount, 0) }}</p>
                        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium
                            {{ $investment->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ ucfirst($investment->status) }}
                        </span>
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
        @else
        <div class="p-12 text-center text-slate-400">
            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <p class="font-medium text-slate-600">No investments yet</p>
            <p class="text-sm mt-1">Your subscription will be automatically invested in active projects.</p>
        </div>
        @endif
    </div>
</x-layouts.subscriber>
