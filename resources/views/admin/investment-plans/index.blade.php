@extends('admin.layout')

@section('page_title', 'Investment Plans')

@section('content')
    <div class="card">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            <h3 class="text-xl font-bold text-navy">Investment Plans</h3>
            <a href="{{ route('admin.investment-plans.create') }}" class="btn-primary">
                + New Investment Plan
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 rounded-lg bg-light-teal text-teal border border-teal/20">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="data-table w-full text-left">
                <thead>
                    <tr>
                        <th class="px-4 py-2 border-b">Name</th>
                        <th class="px-4 py-2 border-b">Project</th>
                        <th class="px-4 py-2 border-b">Type</th>
                        <th class="px-4 py-2 border-b">Min Investment</th>
                        <th class="px-4 py-2 border-b">ROI</th>
                        <th class="px-4 py-2 border-b">Duration</th>
                        <th class="px-4 py-2 border-b">Status</th>
                        <th class="px-4 py-2 border-b">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plans as $plan)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 border-b font-medium text-navy">{{ $plan->name }}</td>
                        <td class="px-4 py-3 border-b">{{ $plan->project->title ?? $plan->project->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 border-b">
                            <span class="px-2 py-1 rounded text-xs font-semibold {{ $plan->type === 'sip' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ $plan->type === 'sip' ? 'SIP' : 'One-time' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 border-b font-numbers text-slate">₹{{ number_format($plan->min_investment, 2) }}</td>
                        <td class="px-4 py-3 border-b font-numbers text-green-600">{{ $plan->expected_return_percentage }}%</td>
                        <td class="px-4 py-3 border-b">{{ $plan->duration_months ?? '-' }} Months</td>
                        <td class="px-4 py-3 border-b">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $plan->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $plan->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 border-b">
                            <div class="flex items-center gap-3">
                                <a href="{{ route('admin.investment-plans.edit', $plan) }}" class="text-teal hover:text-navy font-medium transition-colors">Edit</a>
                                <form action="{{ route('admin.investment-plans.destroy', $plan) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-error hover:text-red-700 font-medium transition-colors" onclick="return confirm('Are you sure you want to delete this plan?')">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-slate">No investment plans found.</td>
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
