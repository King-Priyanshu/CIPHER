@extends('components.layouts.admin')

@section('page_title', 'Create Fund Pool')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="card">
            <h3 class="text-xl font-bold text-navy mb-6">Create New Fund Pool</h3>
            
            <form action="{{ route('admin.pools.store') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-navy mb-1.5">Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                            class="input-field" placeholder="Pool Name">
                        @error('name')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="total_amount" class="block text-sm font-medium text-navy mb-1.5">Total Amount (₹)</label>
                        <input type="number" name="total_amount" id="total_amount" value="{{ old('total_amount', 0) }}" step="0.01" min="0" required
                            class="input-field font-numbers" placeholder="0.00">
                        @error('total_amount')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="period_start" class="block text-sm font-medium text-navy mb-1.5">Period Start</label>
                        <input type="date" name="period_start" id="period_start" value="{{ old('period_start') }}" required
                            class="input-field">
                        @error('period_start')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="period_end" class="block text-sm font-medium text-navy mb-1.5">Period End</label>
                        <input type="date" name="period_end" id="period_end" value="{{ old('period_end') }}" required
                            class="input-field">
                        @error('period_end')
                            <p class="mt-1 text-sm text-error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-3 border-t border-gray-100 pt-6">
                    <a href="{{ route('admin.pools.index') }}" class="btn-ghost">Cancel</a>
                    <button type="submit" class="btn-primary">Create Pool</button>
                </div>
            </form>
        </div>
    </div>
@endsection
