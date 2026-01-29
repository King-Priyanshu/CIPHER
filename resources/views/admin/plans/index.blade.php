<x-layouts.admin>
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-white">Subscription Plans</h2>
        <a href="{{ route('admin.plans.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
            + New Plan
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
                        <th class="px-6 py-3">Price</th>
                        <th class="px-6 py-3">Interval</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($plans as $plan)
                    <tr>
                        <td class="px-6 py-4 font-medium text-white">{{ $plan->name }}</td>
                        <td class="px-6 py-4">${{ number_format($plan->price, 2) }} {{ $plan->currency }}</td>
                        <td class="px-6 py-4">{{ ucfirst($plan->interval) }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded {{ $plan->is_active ? 'bg-green-900 text-green-300' : 'bg-red-900 text-red-300' }}">
                                {{ $plan->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.plans.edit', $plan) }}" class="text-indigo-400 hover:text-indigo-300 mr-3">Edit</a>
                            <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No plans found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $plans->links() }}
    </div>
</x-layouts.admin>
