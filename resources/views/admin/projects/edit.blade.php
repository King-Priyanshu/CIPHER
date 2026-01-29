<x-layouts.admin>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-white">Edit Project: {{ $project->title }}</h2>
    </div>

    <div class="bg-gray-800 rounded-lg shadow border border-gray-700 p-6">
        <form action="{{ route('admin.projects.update', $project) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-300 mb-2">Title</label>
                    <input type="text" name="title" id="title" value="{{ old('title', $project->title) }}" required
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('title')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-300 mb-2">Status</label>
                    <select name="status" id="status" required
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="draft" {{ old('status', $project->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ old('status', $project->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ old('status', $project->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="fund_goal" class="block text-sm font-medium text-gray-300 mb-2">Fund Goal ($)</label>
                    <input type="number" name="fund_goal" id="fund_goal" value="{{ old('fund_goal', $project->fund_goal) }}" step="0.01" min="0" required
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('fund_goal')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="current_fund" class="block text-sm font-medium text-gray-300 mb-2">Current Fund ($)</label>
                    <input type="number" name="current_fund" id="current_fund" value="{{ old('current_fund', $project->current_fund) }}" step="0.01" min="0"
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('current_fund')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="starts_at" class="block text-sm font-medium text-gray-300 mb-2">Start Date</label>
                    <input type="date" name="starts_at" id="starts_at" value="{{ old('starts_at', $project->starts_at?->format('Y-m-d')) }}"
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('starts_at')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="ends_at" class="block text-sm font-medium text-gray-300 mb-2">End Date</label>
                    <input type="date" name="ends_at" id="ends_at" value="{{ old('ends_at', $project->ends_at?->format('Y-m-d')) }}"
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('ends_at')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                <textarea name="description" id="description" rows="4" required
                    class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ old('description', $project->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('admin.projects.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-500">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Update Project</button>
            </div>
        </form>
    </div>
</x-layouts.admin>
