@extends('components.layouts.admin')

@section('page_title', 'Content Pages')

@section('content')
    <div class="card">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
            <h3 class="text-xl font-bold text-navy">Content Pages</h3>
            <a href="{{ route('admin.pages.create') }}" class="btn-primary">
                + New Page
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
                        <th>Slug</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pages as $page)
                    <tr>
                        <td class="font-medium text-navy">{{ $page->title }}</td>
                        <td class="text-slate text-sm font-mono">/page/{{ $page->slug }}</td>
                        <td>
                            <span class="{{ $page->is_published ? 'badge-success' : 'badge-warning' }}">
                                {{ $page->is_published ? 'Published' : 'Draft' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('page.show', $page->slug) }}" target="_blank" class="text-teal-blue hover:text-navy font-medium mr-3 transition-colors">View</a>
                            <a href="{{ route('admin.pages.edit', $page) }}" class="text-teal hover:text-navy font-medium mr-3 transition-colors">Edit</a>
                            <form action="{{ route('admin.pages.destroy', $page) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-error hover:text-red-700 font-medium transition-colors" onclick="return confirm('Are you sure?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-slate">No pages found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $pages->links() }}
        </div>
    </div>
@endsection
