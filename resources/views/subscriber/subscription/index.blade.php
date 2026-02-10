<x-layouts.subscriber>
    <x-slot:title>
        Add Funds
    </x-slot:title>

    <div class="max-w-4xl">
        <div class="card p-8 mb-8">
            <h3 class="text-lg font-bold text-navy mb-2">Purchase Investment Plan</h3>
            <p class="text-slate-500 mb-6">Select a plan to add funds to your wallet. You can purchase multiple times.</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($plans as $plan)
                    <div class="border rounded-xl p-6 border-gray-200 hover:border-teal-300 transition">
                        <div class="flex justify-between items-start mb-4">
                            <h5 class="font-bold text-navy text-lg">{{ $plan->name }}</h5>
                            <span class="text-xl font-bold text-teal-600">₹{{ number_format($plan->price, 0) }}</span>
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

                        <a href="{{ route('checkout.show', $plan->slug) }}" class="block w-full py-2 bg-navy hover:bg-slate-800 text-white text-center font-semibold rounded-lg transition">
                            Buy Now
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.subscriber>
