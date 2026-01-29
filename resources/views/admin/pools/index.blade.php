<x-layouts.admin>
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-white">Fund Pools</h2>
        <a href="{{ route('admin.pools.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
            + New Pool
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
                        <th class="px-6 py-3">Name</th>
                        <th class="px-6 py-3">Total Amount</th>
                        <th class="px-6 py-3">Allocated</th>
                        <th class="px-6 py-3">Period</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($pools as $pool)
                    <tr>
                        <td class="px-6 py-4 font-medium text-white">{{ $pool->name }}</td>
                        <td class="px-6 py-4">${{ number_format($pool->total_amount, 2) }}</td>
                        <td class="px-6 py-4">${{ number_format($pool->allocated_amount, 2) }}</td>
                        <td class="px-6 py-4 text-sm">
                            {{ $pool->period_start->format('M d, Y') }} - {{ $pool->period_end->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.pools.edit', $pool) }}" class="text-indigo-400 hover:text-indigo-300 mr-3">Edit</a>
                            <form action="{{ route('admin.pools.destroy', $pool) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No fund pools found.</td>
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
