<x-layouts.admin>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-white">Edit Plan: {{ $plan->name }}</h2>
    </div>

    <div class="bg-gray-800 rounded-lg shadow border border-gray-700 p-6">
        <form action="{{ route('admin.plans.update', $plan) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $plan->name) }}" required
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('name')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="price" class="block text-sm font-medium text-gray-300 mb-2">Price ($)</label>
                    <input type="number" name="price" id="price" value="{{ old('price', $plan->price) }}" step="0.01" min="0" required
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('price')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="interval" class="block text-sm font-medium text-gray-300 mb-2">Interval</label>
                    <select name="interval" id="interval" required
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="monthly" {{ old('interval', $plan->interval) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="quarterly" {{ old('interval', $plan->interval) == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                        <option value="annual" {{ old('interval', $plan->interval) == 'annual' ? 'selected' : '' }}>Annual</option>
                    </select>
                    @error('interval')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="trial_days" class="block text-sm font-medium text-gray-300 mb-2">Trial Days</label>
                    <input type="number" name="trial_days" id="trial_days" value="{{ old('trial_days', $plan->trial_days) }}" min="0"
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('trial_days')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $plan->is_active) ? 'checked' : '' }}
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-600 rounded bg-gray-700">
                    <label for="is_active" class="ml-2 block text-sm text-gray-300">Active</label>
                </div>
            </div>

            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                <textarea name="description" id="description" rows="3"
                    class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ old('description', $plan->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('admin.plans.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-500">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Update Plan</button>
            </div>
        </form>
    </div>
</x-layouts.admin>
