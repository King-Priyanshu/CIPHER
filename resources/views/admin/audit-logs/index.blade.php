<x-layouts.admin>
    <x-slot:title>Audit Logs</x-slot:title>

    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-navy">Audit Logs</h1>
            <p class="text-slate-500 text-sm mt-1">View all system activity and user actions</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-6">
        <div class="p-4">
            <form method="GET" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-sm font-medium text-slate-600 mb-1">Action</label>
                    <select name="action" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">All Actions</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>{{ $action }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-sm font-medium text-slate-600 mb-1">Entity Type</label>
                    <select name="entity_type" class="w-full rounded-lg border-gray-300 text-sm">
                        <option value="">All Types</option>
                        @foreach($entityTypes as $type)
                            <option value="{{ $type }}" {{ request('entity_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-sm font-medium text-slate-600 mb-1">From Date</label>
                    <input type="date" name="from_date" value="{{ request('from_date') }}" class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <div class="flex-1 min-w-[150px]">
                    <label class="block text-sm font-medium text-slate-600 mb-1">To Date</label>
                    <input type="date" name="to_date" value="{{ request('to_date') }}" class="w-full rounded-lg border-gray-300 text-sm">
                </div>
                <button type="submit" class="px-4 py-2 bg-navy text-white rounded-lg text-sm font-medium hover:bg-slate-800 transition">
                    Filter
                </button>
                <a href="{{ route('admin.audit-logs.index') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-300 transition">
                    Reset
                </a>
            </form>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="card overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-bold text-navy">Activity Log</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-slate-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">ID</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">User</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Action</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Entity</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Description</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">IP Address</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-slate-500 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-6 py-4 text-sm font-mono text-slate-600">#{{ $log->id }}</td>
                        <td class="px-6 py-4">
                            @if($log->user)
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-sm">
                                        {{ substr($log->user->name, 0, 1) }}
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-semibold text-navy">{{ $log->user->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $log->user->email }}</p>
                                    </div>
                                </div>
                            @else
                                <span class="text-slate-400">System</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600">
                            @if($log->entity_type)
                                <span class="font-mono text-xs">{{ $log->entity_type }}#{{ $log->entity_id }}</span>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600 max-w-xs truncate">
                            {{ $log->description ?? '-' }}
                        </td>
                        <td class="px-6 py-4 text-sm font-mono text-slate-500">{{ $log->ip_address ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-slate-500">{{ $log->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                            No activity logs found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $logs->withQueryString()->links() }}
        </div>
        @endif
    </div>
</x-layouts.admin>
