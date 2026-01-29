<x-layouts.admin>
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-white">Reward Pools</h2>
        <a href="{{ route('admin.reward-pools.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
            + New Reward Pool
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-900 border border-green-700 text-green-300 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-gray-800 rounded-lg shadow border border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-gray-300">
                <thead class="bg-gray-900 text-gray-400 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3">Project</th>
                        <th class="px-6 py-3">Total Amount</th>
                        <th class="px-6 py-3">Distributed</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Distribution Date</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($pools as $pool)
                    <tr>
                        <td class="px-6 py-4 font-medium text-white">{{ $pool->project->title ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-green-400">${{ number_format($pool->total_amount, 2) }}</td>
                        <td class="px-6 py-4">${{ number_format($pool->distributed_amount, 2) }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded {{ $pool->status == 'distributed' ? 'bg-green-900 text-green-300' : ($pool->status == 'pending' ? 'bg-yellow-900 text-yellow-300' : 'bg-gray-600 text-gray-300') }}">
                                {{ ucfirst($pool->status ?? 'pending') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            {{ $pool->distribution_date ? $pool->distribution_date->format('M d, Y') : 'Not set' }}
                        </td>
                        <td class="px-6 py-4">
                            @if($pool->status != 'distributed')
                                <form action="{{ route('admin.reward-pools.distribute', $pool) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-green-400 hover:text-green-300 mr-3" onclick="return confirm('Distribute rewards?')">Distribute</button>
                                </form>
                            @endif
                            <a href="{{ route('admin.reward-pools.edit', $pool) }}" class="text-indigo-400 hover:text-indigo-300 mr-3">Edit</a>
                            <form action="{{ route('admin.reward-pools.destroy', $pool) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No reward pools found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $pools->links() }}
    </div>
</x-layouts.admin>
