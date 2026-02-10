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

                    <hr class="my-8 border-gray-700">

                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-200">Wallet & Transactions</h3>
                        <button onclick="document.getElementById('adjust-wallet-modal').classList.remove('hidden')" class="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded transition">
                            Adjust Balance
                        </button>
                    </div>

                    <div class="bg-gray-750 p-4 rounded-md border border-gray-700 mb-6">
                        <div class="flex items-center justify-between">
                             <div>
                                <p class="text-sm font-medium text-gray-400">Current Balance</p>
                                <p class="text-2xl font-bold text-gray-100">₹{{ number_format($user->wallet_balance, 2) }}</p>
                             </div>
                             <div>
                                <p class="text-sm font-medium text-gray-400 text-right">Total Transactions</p>
                                <p class="text-xl font-medium text-gray-300 text-right">{{ $user->wallet?->transactions()->count() ?? 0 }}</p>
                             </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-gray-400">
                            <thead class="bg-gray-700 text-gray-200 uppercase font-medium">
                                <tr>
                                    <th class="px-4 py-3">Date</th>
                                    <th class="px-4 py-3">Type</th>
                                    <th class="px-4 py-3">Description</th>
                                    <th class="px-4 py-3 text-right">Amount</th>
                                    <th class="px-4 py-3 text-right">Balance</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                @forelse($user->wallet?->transactions()->latest()->take(10)->get() ?? [] as $transaction)
                                <tr class="hover:bg-gray-700/50">
                                    <td class="px-4 py-3">{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                            {{ $transaction->type === 'credit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($transaction->type) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">{{Str::limit($transaction->description, 50)}}</td>
                                    <td class="px-4 py-3 text-right font-mono {{ $transaction->type === 'credit' ? 'text-green-400' : 'text-red-400' }}">
                                        {{ $transaction->type === 'credit' ? '+' : '-' }}₹{{ number_format($transaction->amount, 2) }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-mono text-gray-300">₹{{ number_format($transaction->running_balance, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-500">No transactions found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <h3 class="text-lg font-medium text-gray-200 mt-8 mb-4">Participated Projects</h3>
                    <p class="text-gray-400 italic">Project participation details coming soon.</p>

                    <!-- Adjust Wallet Modal -->
                    <div id="adjust-wallet-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-75 hidden">
                        <div class="bg-gray-800 rounded-lg shadow-xl w-full max-w-md mx-4 overflow-hidden border border-gray-700">
                            <div class="px-6 py-4 border-b border-gray-700 flex justify-between items-center">
                                <h3 class="text-lg font-medium text-gray-100">Adjust Balance</h3>
                                <button onclick="document.getElementById('adjust-wallet-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-200">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </div>
                            <form action="{{ route('admin.users.wallet.adjust', $user) }}" method="POST" class="p-6">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-400 mb-1">Transaction Type</label>
                                    <select name="type" class="w-full bg-gray-700 border-gray-600 rounded-md text-gray-100 focus:ring-indigo-500 focus:border-indigo-500">
                                        <option value="credit">Credit (Add Funds)</option>
                                        <option value="debit">Debit (Deduct Funds)</option>
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-400 mb-1">Amount (₹)</label>
                                    <input type="number" name="amount" min="0.01" step="0.01" required class="w-full bg-gray-700 border-gray-600 rounded-md text-gray-100 focus:ring-indigo-500 focus:border-indigo-500 font-mono" placeholder="0.00">
                                </div>
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-400 mb-1">Reason / Description</label>
                                    <input type="text" name="description" required class="w-full bg-gray-700 border-gray-600 rounded-md text-gray-100 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. Bonus, Correction" maxlength="255">
                                </div>
                                <div class="flex justify-end gap-3">
                                    <button type="button" onclick="document.getElementById('adjust-wallet-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-200 rounded text-sm font-medium transition">Cancel</button>
                                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded text-sm font-medium transition">Confirm</button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
