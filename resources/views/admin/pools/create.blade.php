<x-layouts.admin>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-white">Create Fund Pool</h2>
    </div>

    <div class="bg-gray-800 rounded-lg shadow border border-gray-700 p-6">
        <form action="{{ route('admin.pools.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('name')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="total_amount" class="block text-sm font-medium text-gray-300 mb-2">Total Amount ($)</label>
                    <input type="number" name="total_amount" id="total_amount" value="{{ old('total_amount', 0) }}" step="0.01" min="0" required
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('total_amount')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="period_start" class="block text-sm font-medium text-gray-300 mb-2">Period Start</label>
                    <input type="date" name="period_start" id="period_start" value="{{ old('period_start') }}" required
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('period_start')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="period_end" class="block text-sm font-medium text-gray-300 mb-2">Period End</label>
                    <input type="date" name="period_end" id="period_end" value="{{ old('period_end') }}" required
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-md text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    @error('period_end')
                        <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('admin.pools.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-500">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Create Pool</button>
            </div>
        </form>
    </div>
</x-layouts.admin>
