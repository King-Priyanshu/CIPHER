@extends('components.layouts.admin')

@section('page_title', 'Projects')

@section('content')
<div class="card">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
        <h3 class="text-xl font-bold text-navy">All Projects</h3>
        <a href="{{ route('admin.projects.create') }}" class="btn-primary">
            + New Project
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
                    <th>Title</th>
                    <th>Status</th>
                    <th>Fund Goal</th>
                    <th>Current Fund</th>
                    <th>Dates</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @forelse($projects as $project)
                <tr>
                    <!-- TITLE -->
                    <td class="font-medium text-navy">
                        {{ $project->title }}
                    </td>

                    <!-- ✅ STATUS (FIXED: NOW INSIDE <td>) -->
                    <td>
                        @if($project->status === 'active')
                            <span class="badge badge-success">Active</span>
                        @elseif($project->status === 'paused')
                            <span class="badge badge-warning">Paused</span>
                        @elseif($project->status === 'completed')
                            <span class="badge badge-purple">Completed</span>
                        @elseif($project->status === 'cancelled')
                            <span class="badge badge-error">Cancelled</span>
                        @else
                            <span class="badge">Draft</span>
                        @endif
                    </td>

                    <!-- FUND GOAL -->
                    <td class="font-numbers text-slate">
                        ₹{{ number_format($project->fund_goal, 2) }}
                    </td>

                    <!-- CURRENT FUND -->
                    <td class="font-numbers text-teal font-semibold">
                        ₹{{ number_format($project->current_fund, 2) }}
                    </td>

                    <!-- DATES -->
                    <td class="text-slate text-sm">
                        {{ $project->starts_at?->format('M d, Y') ?? 'Not set' }}
                        @if($project->ends_at)
                            <br>
                            <span class="text-xs text-slate/70">
                                to {{ $project->ends_at->format('M d, Y') }}
                            </span>
                        @endif
                    </td>

                    <!-- ACTIONS -->
                    <td class="whitespace-nowrap">
                        <a href="{{ route('admin.projects.edit', $project) }}"
                           class="action-btn action-edit mr-2">
                            Edit
                        </a>

                        <form action="{{ route('admin.projects.destroy', $project) }}"
                              method="POST"
                              class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    onclick="return confirm('Are you sure?')"
                                    class="action-btn action-delete">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-slate">
                        No projects found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $projects->links() }}
    </div>
</div>
@endsection
