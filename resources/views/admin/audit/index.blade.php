<x-layouts.admin>
    <x-slot:title>
        Audit Logs
    </x-slot:title>

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-navy">Audit Logs</h1>
        <span class="text-sm text-slate-500">Read-Only Secure Ledger</span>
    </div>

    <!-- Filters -->
    <div class="card p-4 mb-6">
        <form action="{{ route('admin.audit-logs.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
            <div>
                <label class="block text-xs font-bold text-navy mb-1">User ID</label>
                <input type="text" name="user_id" value="{{ request('user_id') }}" class="input w-32" placeholder="ID...">
            </div>
            <div>
                <label class="block text-xs font-bold text-navy mb-1">Action</label>
                <input type="text" name="action" value="{{ request('action') }}" class="input w-48" placeholder="e.g. profit.distributed">
            </div>
            <div>
                <button type="submit" class="btn btn-navy">Filter</button>
                <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-ghost ml-2">Reset</a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="card overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-gray-100 text-xs text-slate-500 uppercase">
                    <th class="p-4">Timestamp</th>
                    <th class="p-4">User</th>
                    <th class="p-4">Action</th>
                    <th class="p-4">Description</th>
                    <th class="p-4">IP / Agent</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 text-sm md:text-base">
                @forelse ($logs as $log)
                    <tr class="hover:bg-slate-50/50">
                        <td class="p-4 whitespace-nowrap text-slate-500 text-xs">
                            {{ $log->created_at->format('M d, Y H:i:s') }}
                        </td>
                        <td class="p-4">
                            @if($log->user)
                                <a href="{{ route('admin.users.show', $log->user_id) }}" class="font-bold text-navy hover:text-teal-600">
                                    {{ $log->user->name }}
                                </a>
                                <div class="text-xs text-slate-400">ID: {{ $log->user_id }}</div>
                            @else
                                <span class="text-slate-400">System / Unknown</span>
                            @endif
                        </td>
                        <td class="p-4">
                            @php
                                $colors = [
                                    'info' => 'bg-slate-100 text-slate-600',
                                    'warning' => 'bg-amber-100 text-amber-700',
                                    'error' => 'bg-red-100 text-red-700',
                                    'critical' => 'bg-red-500 text-white',
                                ];
                                $color = $colors[$log->severity] ?? $colors['info'];
                            @endphp
                            <span class="px-2 py-1 rounded text-xs font-bold font-mono {{ $color }}">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="p-4 text-slate-600 max-w-md break-words">
                            {{ $log->description }}
                            @if($log->entity_type)
                                <div class="text-xs text-slate-400 mt-1">
                                    Entity: {{ class_basename($log->entity_type) }} #{{ $log->entity_id }}
                                </div>
                            @endif
                        </td>
                        <td class="p-4 text-xs text-slate-400">
                            <div>{{ $log->ip_address ?? '-' }}</div>
                            <div class="truncate max-w-[100px]" title="{{ $log->user_agent }}">{{ $log->user_agent ?? '-' }}</div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-400">
                            No logs found matching your criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $logs->links() }}
    </div>
</x-layouts.admin>
