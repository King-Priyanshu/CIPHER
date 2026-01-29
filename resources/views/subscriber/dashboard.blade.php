<x-layouts.app>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Subscription Status -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">My Subscription</h3>
                        @if($subscription)
                            <div class="text-2xl font-bold text-indigo-600 mb-1">
                                {{ $subscription->plan->name }}
                            </div>
                            <p class="text-sm text-gray-500 mb-4">
                                {{ ucfirst($subscription->status) }} • Renews {{ $subscription->ends_at ? $subscription->ends_at->format('M d, Y') : 'N/A' }}
                            </p>
                            <a href="#" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Manage Subscription &rarr;</a>
                        @else
                            <p class="text-gray-500 mb-4">You don't have an active subscription.</p>
                            <a href="#" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                Subscribe Now
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Rewards Placeholder -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">My Rewards</h3>
                        <div class="text-2xl font-bold text-gray-800 mb-1">$0.00</div>
                        <p class="text-sm text-gray-500 mb-4">Total distributed rewards</p>
                        <span class="text-xs text-gray-400">Rewards are distributed based on active projects.</span>
                    </div>
                </div>

                <!-- Projects Quick View -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Active Projects</h3>
                        @if(count($activeProjects) > 0)
                            <ul class="space-y-3">
                                @foreach($activeProjects as $project)
                                    <li class="flex justify-between text-sm">
                                        <span>{{ $project->title }}</span>
                                        <span class="text-green-600">Active</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-gray-500 text-sm">No active projects at the moment.</p>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-layouts.app>
