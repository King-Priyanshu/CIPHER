<x-layouts.app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Rewards') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Total Rewards Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200 text-center">
                    <h3 class="text-lg font-medium text-gray-900">Total Rewards Earned</h3>
                    <div class="mt-2 text-4xl font-extrabold text-indigo-600">${{ number_format($totalRewards, 2) }}</div>
                </div>
            </div>

            <!-- Reward History Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Reward History</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-gray-500">
                        <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
                            <tr>
                                <th class="px-6 py-3">Date</th>
                                <th class="px-6 py-3">Project / Pool</th>
                                <th class="px-6 py-3">Amount</th>
                                <th class="px-6 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($rewards as $reward)
                            <tr class="bg-white">
                                <td class="px-6 py-4">{{ $reward->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4">{{ $reward->rewardPool->project->title ?? 'General Pool' }}</td>
                                <td class="px-6 py-4 font-medium text-gray-900">${{ number_format($reward->amount, 2) }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $reward->status === 'distributed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                        {{ ucfirst($reward->status) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center">No rewards history found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
