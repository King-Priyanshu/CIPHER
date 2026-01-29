<x-layouts.admin>
    <div class="mb-6 flex justify-between items-center">
        <h2 class="text-2xl font-bold text-white">Projects</h2>
        <a href="{{ route('admin.projects.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
            + New Project
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
                        <th class="px-6 py-3">Title</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3">Fund Goal</th>
                        <th class="px-6 py-3">Current Fund</th>
                        <th class="px-6 py-3">Dates</th>
                        <th class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($projects as $project)
                    <tr>
                        <td class="px-6 py-4 font-medium text-white">{{ $project->title }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded {{ $project->status == 'active' ? 'bg-green-900 text-green-300' : ($project->status == 'completed' ? 'bg-blue-900 text-blue-300' : 'bg-gray-600 text-gray-300') }}">
                                {{ ucfirst($project->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">${{ number_format($project->fund_goal, 2) }}</td>
                        <td class="px-6 py-4">${{ number_format($project->current_fund, 2) }}</td>
                        <td class="px-6 py-4 text-sm">
                            {{ $project->starts_at ? $project->starts_at->format('M d, Y') : 'Not set' }}
                            @if($project->ends_at)
                                <br><span class="text-gray-500">to {{ $project->ends_at->format('M d, Y') }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.projects.edit', $project) }}" class="text-indigo-400 hover:text-indigo-300 mr-3">Edit</a>
                            <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No projects found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $projects->links() }}
    </div>
</x-layouts.admin>
