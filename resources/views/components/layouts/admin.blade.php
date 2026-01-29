@extends('components.layouts.base')

@section('title', 'Admin Dashboard - CIPHER')
@section('body_class', 'bg-gray-900 text-gray-100')

@section('content')
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-gray-800 border-r border-gray-700">
            <div class="flex items-center justify-center h-16 border-b border-gray-700">
                <span class="text-xl font-bold text-white">CIPHER Admin</span>
            </div>
            <nav class="mt-5 px-4 space-y-2">
                <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 rounded {{ request()->routeIs('admin.dashboard') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 text-gray-300' }}">Dashboard</a>
                <a href="{{ route('admin.users.index') }}" class="block px-4 py-2 rounded {{ request()->routeIs('admin.users.*') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 text-gray-300' }}">Users</a>
                <a href="{{ route('admin.projects.index') }}" class="block px-4 py-2 rounded {{ request()->routeIs('admin.projects.*') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 text-gray-300' }}">Projects</a>
                <a href="{{ route('admin.plans.index') }}" class="block px-4 py-2 rounded {{ request()->routeIs('admin.plans.*') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 text-gray-300' }}">Subscription Plans</a>
                <a href="{{ route('admin.pools.index') }}" class="block px-4 py-2 rounded {{ request()->routeIs('admin.pools.*') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 text-gray-300' }}">Fund Pools</a>
                <a href="{{ route('admin.reward-pools.index') }}" class="block px-4 py-2 rounded {{ request()->routeIs('admin.reward-pools.*') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 text-gray-300' }}">Reward Pools</a>
                <a href="{{ route('admin.pages.index') }}" class="block px-4 py-2 rounded {{ request()->routeIs('admin.pages.*') ? 'bg-gray-900 text-white' : 'hover:bg-gray-700 text-gray-300' }}">Content Pages</a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <header class="h-16 bg-gray-800 border-b border-gray-700 flex items-center justify-between px-6">
                <h1 class="text-lg font-semibold">Dashboard</h1>
                <div class="flex items-center">
                    <span class="mr-4">{{ Auth::user()->name ?? 'Admin' }}</span>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-900 p-6">
                {{ $slot }}
            </main>
        </div>
    </div>
@endsection
