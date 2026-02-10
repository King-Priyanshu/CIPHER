<x-layouts.subscriber>
    <x-slot:title>
        Notifications
    </x-slot:title>

    <div class="max-w-4xl">
        <!-- Filter and Search Section -->
        <div class="card p-6 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 text-slate-400" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input 
                            type="text" 
                            placeholder="Search notifications..." 
                            class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition"
                            id="notificationSearch"
                        />
                    </div>
                </div>
                <div class="flex gap-2">
                    <select class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition" id="statusFilter">
                        <option value="">All Notifications</option>
                        <option value="unread">Unread</option>
                        <option value="read">Read</option>
                    </select>
                    <select class="px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-transparent transition" id="typeFilter">
                        <option value="">All Types</option>
                        <option value="payment">Payments</option>
                        <option value="investment">Investments</option>
                        <option value="reward">Rewards</option>
                        <option value="system">System</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-white">
                <h3 class="text-lg font-bold text-navy">All Notifications</h3>
                <button onclick="markAllAsRead()" class="text-sm text-teal-600 font-semibold hover:text-teal-700 hover:underline transition">
                    Mark all as read
                </button>
            </div>
            
            <div class="divide-y divide-gray-100 bg-white" id="notificationsList">
                @forelse($notifications as $notification)
                    <div class="p-6 hover:bg-slate-50 transition notification-item {{ $notification->read_at ? 'read' : 'unread' }}"
                         data-status="{{ $notification->read_at ? 'read' : 'unread' }}"
                         data-type="{{ $notification->data['type'] ?? 'system' }}"
                         data-title="{{ $notification->data['title'] ?? 'System Notification' }}"
                         data-message="{{ $notification->data['message'] ?? '' }}">
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

    @push('scripts')
        <script>
            // Search functionality
            const searchInput = document.getElementById('notificationSearch');
            const statusFilter = document.getElementById('statusFilter');
            const typeFilter = document.getElementById('typeFilter');
            const notificationItems = document.querySelectorAll('.notification-item');

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                
                notificationItems.forEach(item => {
                    const title = item.dataset.title.toLowerCase();
                    const message = item.dataset.message.toLowerCase();
                    
                    if (title.includes(searchTerm) || message.includes(searchTerm)) {
                        item.style.display = '';
                        item.style.animation = 'fadeIn 0.3s ease-out';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });

            // Status filter
            statusFilter.addEventListener('change', function() {
                const status = this.value;
                
                notificationItems.forEach(item => {
                    if (status === '' || item.dataset.status === status) {
                        item.style.display = '';
                        item.style.animation = 'fadeIn 0.3s ease-out';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });

            // Type filter
            typeFilter.addEventListener('change', function() {
                const type = this.value;
                
                notificationItems.forEach(item => {
                    if (type === '' || item.dataset.type === type) {
                        item.style.display = '';
                        item.style.animation = 'fadeIn 0.3s ease-out';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });

            // Mark all as read
            function markAllAsRead() {
                // This would normally call an API endpoint
                const unreadItems = document.querySelectorAll('.notification-item.unread');
                unreadItems.forEach(item => {
                    item.classList.remove('unread');
                    item.classList.add('read');
                    item.style.animation = 'fadeIn 0.5s ease-out';
                });
                
                // Show success message
                const successMessage = document.createElement('div');
                successMessage.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg';
                successMessage.textContent = 'All notifications marked as read';
                document.body.appendChild(successMessage);
                
                setTimeout(() => {
                    successMessage.remove();
                }, 3000);
            }

            // Add interactive hover effects
            document.addEventListener('DOMContentLoaded', function() {
                const items = document.querySelectorAll('.notification-item');
                items.forEach(item => {
                    item.addEventListener('mouseenter', function() {
                        this.style.transform = 'translateX(4px)';
                        this.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1)';
                    });
                    
                    item.addEventListener('mouseleave', function() {
                        this.style.transform = 'translateX(0)';
                        this.style.boxShadow = 'none';
                    });
                });
            });
        </script>
    @endpush
</x-layouts.subscriber>
