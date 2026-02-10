@extends('components.layouts.admin')

@section('page_title', 'Reward Pools')

@section('content')
    <div class="card">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            <h3 class="text-xl font-bold text-navy">Reward Pools</h3>
            <a href="{{ route('admin.reward-pools.create') }}" class="btn-primary">
                + New Reward Pool
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
                        <th>Project</th>
                        <th>Total Amount</th>
                        <th>Distributed</th>
                        <th>Status</th>
                        <th>Distribution Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pools as $pool)
                    <tr>
                        <td class="font-medium text-navy">{{ $pool->project->title ?? 'N/A' }}</td>
                        <td class="font-numbers text-teal font-semibold">₹{{ number_format($pool->total_amount, 2) }}</td>
                        <td class="font-numbers text-slate">₹{{ number_format($pool->distributed_amount, 2) }}</td>
                        <td>
                            @if($pool->status == 'distributed')
                                <span class="badge-success">Distributed</span>
                            @elseif($pool->status == 'pending')
                                <span class="badge-warning">Pending</span>
                            @else
                                <span class="badge">Draft</span>
                            @endif
                        </td>
                        <td class="text-slate text-sm">
                            {{ $pool->distribution_date ? $pool->distribution_date->format('M d, Y') : 'Not set' }}
                        </td>
                        <td>
                            @if($pool->status != 'distributed')
                                <form action="{{ route('admin.reward-pools.distribute', $pool) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-success hover:text-green-700 font-medium mr-3 transition-colors" onclick="return confirm('Distribute rewards?')">Distribute</button>
                                </form>
                            @endif
                            <a href="{{ route('admin.reward-pools.edit', $pool) }}" class="text-teal hover:text-navy font-medium mr-3 transition-colors">Edit</a>
                            <form action="{{ route('admin.reward-pools.destroy', $pool) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-error hover:text-red-700 font-medium transition-colors" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate">No reward pools found.</td>
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
