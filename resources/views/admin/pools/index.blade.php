@extends('components.layouts.admin')

@section('page_title', 'Fund Pools')

@section('content')
    <div class="card">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            <h3 class="text-xl font-bold text-navy">Fund Pools</h3>
            <a href="{{ route('admin.pools.create') }}" class="btn-primary">
                + New Pool
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
                        <th>Total Amount</th>
                        <th>Allocated</th>
                        <th>Period</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pools as $pool)
                    <tr>
                        <td class="font-medium text-navy">{{ $pool->name }}</td>
                        <td class="font-numbers text-teal font-semibold">₹{{ number_format($pool->total_amount, 2) }}</td>
                        <td class="font-numbers text-slate">₹{{ number_format($pool->allocated_amount, 2) }}</td>
                        <td class="text-slate text-sm">
                            {{ $pool->period_start->format('M d, Y') }} - {{ $pool->period_end->format('M d, Y') }}
                        </td>
                        <td>
                            <a href="{{ route('admin.pools.edit', $pool) }}" class="text-teal hover:text-navy font-medium mr-3 transition-colors">Edit</a>
                            <form action="{{ route('admin.pools.destroy', $pool) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-error hover:text-red-700 font-medium transition-colors" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-slate">No fund pools found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $pools->links() }}
        </div>
    </div>
@endsection
