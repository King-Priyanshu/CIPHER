<x-layouts.app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Projects') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-6">
                <!-- Project Card -->
                @forelse($projects as $project)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">{{ $project->title }}</h3>
                                <p class="text-sm text-gray-500 mt-1">Started {{ $project->starts_at ? $project->starts_at->format('M d, Y') : 'Pending' }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $project->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($project->status) }}
                            </span>
                        </div>
                        
                        <p class="mt-4 text-gray-600">{{ $project->description }}</p>

                        <div class="mt-6">
                            <div class="flex justify-between text-sm font-medium text-gray-900 mb-1">
                                <span>Fund Goal: ${{ number_format($project->fund_goal, 0) }}</span>
                                <span>{{ number_format(($project->current_fund / $project->fund_goal) * 100, 0) }}% Funded</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-indigo-600 h-2.5 rounded-full" style="width: {{ min(($project->current_fund / $project->fund_goal) * 100, 100) }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-white p-6 rounded-lg shadow text-center text-gray-500">
                    No active projects available to view.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>
