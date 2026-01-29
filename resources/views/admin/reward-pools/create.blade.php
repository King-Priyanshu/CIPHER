@extends('components.layouts.admin')

@section('page_title', 'Create Reward Pool')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="card">
            <h3 class="text-xl font-bold text-navy mb-6">Create New Reward Pool</h3>
            
            <form action="{{ route('admin.reward-pools.store') }}" method="POST">
                @csrf
                
                <div class="space-y-6">
                    <div>
                        <label for="project_id" class="block text-sm font-medium text-navy mb-1.5">Project</label>
                        <select name="project_id" id="project_id" required class="input-field">
                            <option value="">Select Project</option>
                            @foreach($projects as $project)
                                <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                    {{ $project->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('project_id')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="total_amount" class="block text-sm font-medium text-navy mb-1.5">Total Reward Amount ($)</label>
                        <input type="number" name="total_amount" id="total_amount" value="{{ old('total_amount') }}" step="0.01" min="0" required
                            class="input-field font-numbers" placeholder="0.00">
                        @error('total_amount')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="distribution_date" class="block text-sm font-medium text-navy mb-1.5">Distribution Date</label>
                        <input type="date" name="distribution_date" id="distribution_date" value="{{ old('distribution_date') }}"
                            class="input-field">
                        @error('distribution_date')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>
                
                    <div class="flex justify-end space-x-3 pt-6 border-t border-gray-100">
                        <a href="{{ route('admin.reward-pools.index') }}" class="btn-ghost">Cancel</a>
                        <button type="submit" class="btn-primary">Create Reward Pool</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
