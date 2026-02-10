<x-layouts.subscriber>
    <x-slot:title>
        My Subscription
    </x-slot:title>

    <div class="max-w-4xl">
        @if($subscription)
            <div class="card p-8 mb-8">
                <h3 class="text-lg font-bold text-navy mb-6">Current Subscription Plan</h3>

                <div class="bg-slate-50 rounded-xl p-6 mb-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h5 class="font-bold text-navy text-xl">{{ $subscription->plan->name }}</h5>
                            <p class="text-slate-500 mt-1">Active Subscription</p>
                        </div>
                        <div class="text-right">
                            <span
                                class="text-2xl font-bold text-teal-600">₹{{ number_format($subscription->plan->price, 0) }}</span>
                            <span class="text-sm text-slate-500 block">per month</span>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @if(is_array($subscription->plan->features))
                            @foreach($subscription->plan->features as $feature)
                                <div class="flex items-center gap-2 text-sm text-slate-600">
                                    <svg class="w-4 h-4 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    {{ $feature }}
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                <div class="border-t border-slate-200 pt-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <p class="text-sm text-slate-500 mb-1">Due Date</p>
                            <p class="text-lg font-bold text-navy">
                                {{ $subscription->current_period_end ? $subscription->current_period_end->format('d M Y') : 'N/A' }}
                            </p>
                            <p class="text-xs text-slate-400 mt-1">
                                ({{ $subscription->current_period_end ? $subscription->current_period_end->diffForHumans() : 'N/A' }})
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500 mb-1">Renewal Date</p>
                            <p class="text-lg font-bold text-navy">
                                {{ $subscription->current_period_end ? $subscription->current_period_end->addDay()->format('d M Y') : 'N/A' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm text-slate-500 mb-1">Subscription Status</p>
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-emerald-100 text-emerald-800">
                                {{ ucfirst($subscription->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Change Plan Section -->
            <div class="card p-8 mb-8">
                <h3 class="text-lg font-bold text-navy mb-6">Change Subscription Plan</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($availablePlans as $plan)
                        <div
                            class="border rounded-lg p-6 hover:border-teal-500 transition-all {{ $subscription->plan->id === $plan->id ? 'border-teal-500 bg-teal-50' : 'border-slate-200' }}">
                            <div class="flex justify-between items-start mb-4">
                                <h4 class="font-bold text-navy text-lg">{{ $plan->name }}</h4>
                                @if($subscription->plan->id === $plan->id)
                                    <span class="bg-teal-100 text-teal-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                        Current Plan
                                    </span>
                                @endif
                            </div>

                            <div class="mb-4">
                                <span class="text-2xl font-bold text-navy">₹{{ number_format($plan->price, 0) }}</span>
                                <span class="text-sm text-slate-500">/month</span>
                            </div>

                            <div class="space-y-2 mb-6">
                                @if(is_array($plan->features))
                                    @foreach($plan->features as $feature)
                                        <div class="flex items-center gap-2 text-sm text-slate-600">
                                            <svg class="w-4 h-4 text-teal-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            {{ $feature }}
                                        </div>
                                    @endforeach
                                @endif
                            </div>

                            @if($subscription->plan->id !== $plan->id)
                                <form action="{{ route('subscriber.subscription.change-plan') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                    <button type="submit"
                                        class="w-full bg-navy text-white py-2 px-4 rounded-lg hover:bg-slate-800 transition">
                                        Switch to {{ $plan->name }}
                                    </button>
                                </form>
                            @else
                                <div class="w-full bg-slate-200 text-slate-600 py-2 px-4 rounded-lg text-center cursor-not-allowed">
                                    Current Plan
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

        @else
            <div class="card p-8 text-center">
                <div class="w-16 h-16 bg-slate-200 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-navy mb-2">No Active Subscription</h3>
                <p class="text-slate-500 mb-6">You don't have an active subscription plan. Invest in a project to get
                    started.</p>

                <a href="{{ route('subscriber.projects.index') }}"
                    class="inline-flex items-center px-6 py-3 bg-navy hover:bg-slate-800 text-white font-semibold rounded-lg transition">
                    Invest Now
                    <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Add smooth transition to plan cards
                const planCards = document.querySelectorAll('[data-plan]');
                planCards.forEach(card => {
                    card.addEventListener('mouseenter', function () {
                        this.style.transform = 'translateY(-2px)';
                        this.style.boxShadow = '0 10px 25px -5px rgba(0, 0, 0, 0.1)';
                    });

                    card.addEventListener('mouseleave', function () {
                        this.style.transform = 'translateY(0)';
                        this.style.boxShadow = '0 1px 3px 0 rgba(0, 0, 0, 0.1)';
                    });
                });
            });
        </script>
    @endpush
</x-layouts.subscriber>