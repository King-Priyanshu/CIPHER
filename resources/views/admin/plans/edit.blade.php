@extends('components.layouts.admin')

@section('page_title', 'Edit Plan')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="card">
            <h3 class="text-xl font-bold text-navy mb-6">Edit Plan: {{ $plan->name }}</h3>
            
            <form action="{{ route('admin.plans.update', $plan) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-navy mb-1.5">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $plan->name) }}" required
                            class="input-field">
                        @error('name')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="price" class="block text-sm font-medium text-navy mb-1.5">Price ($)</label>
                        <input type="number" name="price" id="price" value="{{ old('price', $plan->price) }}" step="0.01" min="0" required
                            class="input-field font-numbers">
                        @error('price')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="interval" class="block text-sm font-medium text-navy mb-1.5">Interval</label>
                        <select name="interval" id="interval" required class="input-field">
                            <option value="monthly" {{ old('interval', $plan->interval) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                            <option value="quarterly" {{ old('interval', $plan->interval) == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                            <option value="annual" {{ old('interval', $plan->interval) == 'annual' ? 'selected' : '' }}>Annual</option>
                        </select>
                        @error('interval')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="trial_days" class="block text-sm font-medium text-navy mb-1.5">Trial Days</label>
                        <input type="number" name="trial_days" id="trial_days" value="{{ old('trial_days', $plan->trial_days) }}" min="0"
                            class="input-field font-numbers">
                        @error('trial_days')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center mt-6">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $plan->is_active) ? 'checked' : '' }}
                            class="w-4 h-4 rounded border-gray-300 text-teal focus:ring-teal">
                        <label for="is_active" class="ml-2 block text-sm text-navy">Active</label>
                    </div>
                </div>

                <div class="mt-6">
                    <label for="description" class="block text-sm font-medium text-navy mb-1.5">Description</label>
                    <textarea name="description" id="description" rows="3"
                        class="input-field">{{ old('description', $plan->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-8 flex justify-end space-x-3 border-t border-gray-100 pt-6">
                    <a href="{{ route('admin.plans.index') }}" class="btn-ghost">Cancel</a>
                    <button type="submit" class="btn-primary">Update Plan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
