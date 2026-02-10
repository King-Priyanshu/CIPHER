@extends('components.layouts.admin')

@section('page_title', 'Refund Requests')

@section('content')
    <div class="card">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            <h3 class="text-xl font-bold text-navy">Refund Requests</h3>
            <div class="flex gap-2">
                <a href="{{ route('admin.finance.refunds.export.csv') }}" class="btn-secondary text-sm">
                    Export CSV
                </a>
                <a href="{{ route('admin.finance.refunds.export.pdf') }}" class="btn-secondary text-sm">
                    Export PDF
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 rounded-lg bg-light-teal text-teal border border-teal/20">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-4 rounded-lg bg-red-100 text-error border border-red-200">
                {{ session('error') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="data-table w-full text-left">
                <thead>
                    <tr>
                        <th class="px-4 py-2 border-b">ID</th>
                        <th class="px-4 py-2 border-b">User</th>
                        <th class="px-4 py-2 border-b">Amount</th>
                        <th class="px-4 py-2 border-b">Context</th>
                        <th class="px-4 py-2 border-b">Status</th>
                        <th class="px-4 py-2 border-b">Date</th>
                        <th class="px-4 py-2 border-b">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($refunds as $refund)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 border-b">#{{ $refund->id }}</td>
                        <td class="px-4 py-3 border-b">
                            @if($refund->user)
                                <div class="font-medium text-navy">{{ $refund->user->name }}</div>
                                <div class="text-xs text-slate">{{ $refund->user->email }}</div>
                            @else
                                <span class="text-slate">Unknown User</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 border-b font-numbers text-slate">₹{{ number_format($refund->amount, 2) }}</td>
                        <td class="px-4 py-3 border-b text-sm">
                            @if($refund->investment)
                                <span class="text-blue-600 font-medium">Inv #{{ $refund->investment->id }}</span>
                                <div class="text-xs text-slate">{{ $refund->investment->project->title ?? 'Unknown Project' }}</div>
                            @elseif($refund->subscription)
                                <span class="text-purple-600 font-medium">Sub #{{ $refund->subscription->id }}</span>
                                <div class="text-xs text-slate">{{ $refund->subscription->plan->name ?? 'Unknown Plan' }}</div>
                            @else
                                <span class="text-slate">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 border-b">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold
                                @if($refund->status === 'approved') bg-green-100 text-green-800
                                @elseif($refund->status === 'rejected') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($refund->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 border-b text-sm text-slate">
                            {{ $refund->created_at->format('M d, Y H:i') }}
                        </td>
                        <td class="px-4 py-3 border-b">
                            @if($refund->status === 'pending')
                                <div class="flex gap-2">
                                    <form action="{{ route('admin.refunds.approve', $refund) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-teal hover:text-navy font-medium transition-colors" onclick="return confirm('Approve this refund?')">Approve</button>
                                    </form>
                                    <form action="{{ route('admin.refunds.reject', $refund) }}" method="POST" class="inline">
                                        @csrf
                                        <div class="relative group inline-block">
                                            <button type="button" onclick="document.getElementById('reject-form-{{ $refund->id }}').classList.toggle('hidden')" class="text-error hover:text-red-700 font-medium transition-colors">Reject</button>
                                            <!-- Simple inline reject note input -->
                                            <div id="reject-form-{{ $refund->id }}" class="hidden absolute right-0 mt-2 w-64 bg-white shadow-lg rounded p-4 z-10 border border-gray-200">
                                                <textarea name="admin_note" placeholder="Reason for rejection" class="w-full border rounded p-2 text-sm mb-2" required></textarea>
                                                <button type="submit" class="btn-primary w-full text-xs">Confirm Reject</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            @else
                                <span class="text-slate text-xs">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate">No refund requests found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $refunds->links() }}
        </div>
    </div>
@endsection
