<x-layouts.subscriber>
    <x-slot:title>
        Notifications
    </x-slot:title>

    <div class="max-w-4xl">
        <div class="card overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-white">
                <h3 class="text-lg font-bold text-navy">All Notifications</h3>
                <button class="text-sm text-teal-600 font-semibold hover:text-teal-700 hover:underline transition">Mark all as read</button>
            </div>
            
            <div class="divide-y divide-gray-100 bg-white">
                @forelse($notifications as $notification)
                    <div class="p-6 hover:bg-slate-50 transition {{ $notification->read_at ? '' : 'bg-teal-50/30' }}">
                        <div class="flex justify-between items-start gap-4">
                            <div class="flex-1">
                                <h4 class="text-sm font-bold text-navy mb-1">{{ $notification->data['title'] ?? 'System Notification' }}</h4>
                                <p class="text-slate-600 text-sm leading-relaxed">{{ $notification->data['message'] ?? '' }}</p>
                            </div>
                            <span class="text-xs text-slate-400 whitespace-nowrap font-medium">
                                {{ $notification->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-slate-400">
                        <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-6 h-6 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        <p class="font-medium">No notifications yet.</p>
                        <p class="text-xs mt-1">We'll let you know when important things happen.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.subscriber>
