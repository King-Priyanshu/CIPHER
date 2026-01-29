@extends('components.layouts.admin')

@section('page_title', 'User Management')

@section('content')
<div class="card">

    <!-- HEADER + SEARCH -->
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
        <div>
            <h3 class="text-xl font-bold text-navy">All Users</h3>
            <p class="text-sm text-slate-500 mt-1">
                Manage all registered users and their roles.
            </p>
        </div>

        <form method="GET" action="{{ route('admin.users.index') }}"
              class="flex w-full sm:w-auto items-center gap-3">

            <div class="relative w-full sm:w-80">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search users..."
                    class="input-field h-11 pl-11 pr-4"
                >
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <button type="submit" class="btn-primary h-11 px-6 text-sm">
                Search
            </button>
        </form>
    </div>

    <!-- TABLE -->
    <div class="overflow-x-auto rounded-xl border border-gray-200">
        <table class="data-table">

            <thead>
            <tr>
                <th class="pl-6">User</th>
                <th>Email</th>
                <th>Role</th>
                <th>Joined</th>
                <th class="text-right pr-6">Actions</th>
            </tr>
            </thead>

            <tbody>
            @forelse ($users as $user)
                <tr>

                    <td class="pl-6">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-teal-500 text-white font-bold flex items-center justify-center shadow-sm">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-semibold text-sm text-navy">
                                    {{ $user->name }}
                                </div>
                                <div class="text-xs text-slate-500 lg:hidden">
                                    {{ $user->email }}
                                </div>
                            </div>
                        </div>
                    </td>

                    <td class="hidden lg:table-cell">
                        {{ $user->email }}
                    </td>

                    <td>
                        <div class="flex gap-2 flex-wrap">
                            @foreach($user->roles as $role)
                                <span class="badge {{ $role->slug === 'admin' ? 'bg-purple-100 text-purple-700' : 'badge-success' }}">
                                    {{ ucfirst($role->name) }}
                                </span>
                            @endforeach
                        </div>
                    </td>

                    <td class="whitespace-nowrap text-sm text-slate-600">
                        {{ $user->created_at->format('M d, Y') }}
                    </td>

                    <td class="text-right pr-6">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.users.show', $user) }}"
                               class="action-btn action-view">View</a>

                            <a href="{{ route('admin.users.edit', $user) }}"
                               class="action-btn action-edit">Edit</a>

                            @if($user->id !== auth()->id())
                                <form method="POST"
                                      action="{{ route('admin.users.destroy', $user) }}"
                                      onsubmit="return confirm('Delete this user?')"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="action-btn action-delete">
                                        Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>

                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-16 text-slate-400">
                        <div class="flex flex-col items-center justify-center gap-2">
                            <span class="text-3xl opacity-50">👥</span>
                             <span>No users found</span>
                        </div>
                    </td>
                </tr>
            @endforelse
            </tbody>

        </table>
    </div>

    <div class="mt-6">
        {{ $users->links() }}
    </div>

</div>
@endsection
