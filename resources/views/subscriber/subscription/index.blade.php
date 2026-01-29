<x-layouts.subscriber>
    <x-slot:title>
        Subscription
    </x-slot:title>

    <div class="max-w-4xl">
        <!-- Current Plan -->
        <div class="card p-8 mb-8">
            <h3 class="text-lg font-bold text-navy mb-6">Current Subscription</h3>
            
            @if($subscription && $subscription->isActive())
                <div class="flex items-center justify-between p-4 bg-teal-50 border border-teal-100 rounded-xl mb-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center text-teal-600">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-lg font-bold text-navy">{{ $subscription->plan->name }} Plan</p>
                            <p class="text-slate-600 text-sm">Valid until {{ $subscription->ends_at ? $subscription->ends_at->format('F j, Y') : 'forever' }}</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 bg-teal-600 text-white text-xs font-bold uppercase tracking-wider rounded-full">Active</span>
                </div>
            @else
                <div class="bg-slate-50 p-6 rounded-xl border border-gray-100 text-center mb-6">
                    <p class="text-slate-500">You are currently on the <span class="font-bold text-navy">Free Tier</span>.</p>
                </div>
            @endif

            <h4 class="font-bold text-navy mb-4">Available Plans</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($plans as $plan)
                    <div class="border rounded-xl p-6 {{ ($subscription && $subscription->plan_id == $plan->id) ? 'border-teal-500 ring-1 ring-teal-500 bg-teal-50/10' : 'border-gray-200 hover:border-teal-300' }} transition">
                        <div class="flex justify-between items-start mb-4">
                            <h5 class="font-bold text-navy text-lg">{{ $plan->name }}</h5>
                            <span class="text-xl font-bold text-teal-600">${{ number_format($plan->price, 2) }}<span class="text-sm text-slate-400 font-normal">/mo</span></span>
                        </div>
                        <ul class="space-y-2 mb-6 text-sm text-slate-600">
                            @if(is_array($plan->features))
                                @foreach($plan->features as $feature)
                                    <li class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        {{ $feature }}
                                    </li>
                                @endforeach
                            @endif
                        </ul>

                        @if($subscription && $subscription->plan_id == $plan->id)
                            <button disabled class="w-full py-2 bg-slate-100 text-slate-400 font-semibold rounded-lg cursor-not-allowed">Current Plan</button>
                        @else
                            <a href="{{ route('checkout.show', $plan->slug) }}" class="block w-full py-2 bg-navy hover:bg-slate-800 text-white text-center font-semibold rounded-lg transition">
                                {{ $subscription ? 'Switch to this Plan' : 'Subscribe Now' }}
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.subscriber>
