<x-layouts.admin>
    <x-slot:title>Declare Profit</x-slot:title>

    <div class="max-w-2xl mx-auto">
        <div class="card">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-bold text-navy">Declare New Profit</h2>
                <p class="text-sm text-slate-500 mt-1">Declare project profit for distribution to investors.</p>
            </div>
            <div class="p-6">
                <form action="{{ route('admin.profits.store') }}" method="POST">
                    @csrf

                    <div class="mb-6">
                        <label for="project_id" class="block text-sm font-semibold text-navy mb-2">Project</label>
                        <select name="project_id" id="project_id" required 
                                class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500">
                            <option value="">Select a project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->title }}</option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="amount" class="block text-sm font-semibold text-navy mb-2">Profit Amount (₹)</label>
                        <input type="number" name="amount" id="amount" step="0.01" min="0.01" required
                               class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500"
                               placeholder="10000.00">
                        @error('amount')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label for="notes" class="block text-sm font-semibold text-navy mb-2">Notes (Optional)</label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500"
                                  placeholder="Add any notes about this profit distribution..."></textarea>
                    </div>

                    <div class="flex gap-4">
                        <a href="{{ route('admin.profits.index') }}" class="px-6 py-3 bg-gray-100 text-slate-700 rounded-lg font-medium hover:bg-gray-200 transition">
                            Cancel
                        </a>
                        <button type="submit" class="flex-1 px-6 py-3 bg-teal-600 text-white rounded-lg font-semibold hover:bg-teal-700 transition">
                            Declare Profit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.admin>
