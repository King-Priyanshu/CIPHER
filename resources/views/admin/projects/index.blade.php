@extends('components.layouts.admin')

@section('page_title', 'Projects')

@section('content')
    <div class="card">
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
                        <td class="font-medium text-navy">{{ $project->title }}</td>
                        <td>
                            @if($project->status == 'active')
                                <span class="badge-success">Active</span>
                            @elseif($project->status == 'completed')
                                <span class="badge-purple">Completed</span>
                            @elseif($project->status == 'cancelled')
                                <span class="badge-error">Cancelled</span>
                            @else
                                <span class="badge">Draft</span>
                            @endif
                        </td>
                        <td class="font-numbers text-slate">${{ number_format($project->fund_goal, 2) }}</td>
                        <td class="font-numbers text-teal font-semibold">${{ number_format($project->current_fund, 2) }}</td>
                        <td class="text-slate text-sm">
                            {{ $project->starts_at ? $project->starts_at->format('M d, Y') : 'Not set' }}
                            @if($project->ends_at)
                                <br><span class="text-xs text-slate/70">to {{ $project->ends_at->format('M d, Y') }}</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.projects.edit', $project) }}" class="text-teal hover:text-navy font-medium mr-3 transition-colors">Edit</a>
                            <form action="{{ route('admin.projects.destroy', $project) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-error hover:text-red-700 font-medium transition-colors" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-slate">No projects found.</td>
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
