@extends('components.layouts.admin')

@section('page_title', 'Subscription Plans')

@section('content')
    <div class="card">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            <h3 class="text-xl font-bold text-navy">All Plans</h3>
            <a href="{{ route('admin.plans.create') }}" class="btn-primary">
                + New Plan
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 rounded-lg bg-light-teal text-teal border border-teal/20">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Interval</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plans as $plan)
                    <tr>
                        <td class="font-medium text-navy">{{ $plan->name }}</td>
                        <td class="font-numbers text-slate">${{ number_format($plan->price, 2) }} <span class="text-xs uppercase">{{ $plan->currency }}</span></td>
                        <td>{{ ucfirst($plan->interval) }}</td>
                        <td>
                            <span class="{{ $plan->is_active ? 'badge-success' : 'badge-error' }}">
                                {{ $plan->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.plans.edit', $plan) }}" class="text-teal hover:text-navy font-medium mr-3 transition-colors">Edit</a>
                            <form action="{{ route('admin.plans.destroy', $plan) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-error hover:text-red-700 font-medium transition-colors" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate">No plans found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $plans->links() }}
        </div>
    </div>
@endsection
