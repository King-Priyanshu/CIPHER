@extends('components.layouts.admin')

@section('page_title', 'Create Project')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="card">
            <h3 class="text-xl font-bold text-navy mb-6">Project Details</h3>
            
            <form action="{{ route('admin.projects.store') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-navy mb-1.5">Title</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                            class="input-field" placeholder="Project Name">
                        @error('title')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="block text-sm font-medium text-navy mb-1.5">Status</label>
                        <select name="status" id="status" required class="input-field">
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="fund_goal" class="block text-sm font-medium text-navy mb-1.5">Fund Goal ($)</label>
                        <input type="number" name="fund_goal" id="fund_goal" value="{{ old('fund_goal', 0) }}" step="0.01" min="0" required
                            class="input-field font-numbers" placeholder="0.00">
                        @error('fund_goal')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="starts_at" class="block text-sm font-medium text-navy mb-1.5">Start Date</label>
                        <input type="date" name="starts_at" id="starts_at" value="{{ old('starts_at') }}"
                            class="input-field">
                        @error('starts_at')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="ends_at" class="block text-sm font-medium text-navy mb-1.5">End Date</label>
                        <input type="date" name="ends_at" id="ends_at" value="{{ old('ends_at') }}"
                            class="input-field">
                        @error('ends_at')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6">
                    <label for="description" class="block text-sm font-medium text-navy mb-1.5">Description</label>
                    <textarea name="description" id="description" rows="4" required
                        class="input-field">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-8 flex justify-end space-x-3 border-t border-gray-100 pt-6">
                    <a href="{{ route('admin.projects.index') }}" class="btn-ghost">Cancel</a>
                    <button type="submit" class="btn-primary">Create Project</button>
                </div>
            </form>
        </div>
    </div>
@endsection
