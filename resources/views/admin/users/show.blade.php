<x-layouts.admin>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-200 leading-tight">
            {{ __('User Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-100">
                    <div class="mb-6 flex justify-between">
                        <h3 class="text-lg font-medium text-gray-200">Profile Information</h3>
                        <a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-400 hover:text-indigo-300">Edit User</a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <p class="text-sm font-medium text-gray-400">Name</p>
                            <p class="mt-1 text-lg text-gray-100">{{ $user->name }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-400">Email</p>
                            <p class="mt-1 text-lg text-gray-100">{{ $user->email }}</p>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-400">Roles</p>
                            <div class="mt-1">
                                @foreach($user->roles as $role)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                        {{ ucfirst($role->name) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <p class="text-sm font-medium text-gray-400">Joined</p>
                            <p class="mt-1 text-lg text-gray-100">{{ $user->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>

                    <hr class="my-8 border-gray-700">

                    <h3 class="text-lg font-medium text-gray-200 mb-4">Subscription Status</h3>
                    
                    @if($user->subscription)
                        <div class="bg-gray-750 p-4 rounded-md border border-gray-700">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-400">Plan</p>
                                    <p class="mt-1 text-gray-100">{{ $user->subscription->plan->name ?? 'Unknown Plan' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-400">Status</p>
                                    <p class="mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $user->subscription->isActive() ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                            {{ ucfirst($user->subscription->status) }}
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-400">Ends At</p>
                                    <p class="mt-1 text-gray-100">{{ $user->subscription->ends_at ? $user->subscription->ends_at->format('M d, Y') : 'Auto-renewing' }}</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-gray-400">User has no active subscription.</p>
                    @endif

                    <hr class="my-8 border-gray-700">

                    <h3 class="text-lg font-medium text-gray-200 mb-4">Participated Projects</h3>
                     <!-- Placeholder for projects list, maybe a small table or list -->
                    <p class="text-gray-400 italic">Project participation details coming soon.</p>

                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
