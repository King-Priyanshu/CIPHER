<x-layouts.subscriber>
    <x-slot:title>
        Membership Card
    </x-slot:title>

    <div class="max-w-4xl mx-auto">
        
        <div class="mb-8 text-center lg:text-left">
            <h1 class="text-2xl font-bold text-navy">My Identity Card</h1>
            <p class="text-slate-500">Your official community membership and perks access.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
            
            <!-- Digital Card -->
            <div class="relative group perspective-1000">
                <!-- Card Container -->
                <div class="relative w-full aspect-[1.586/1] rounded-2xl shadow-2xl overflow-hidden transform transition-transform duration-500 hover:scale-[1.02]">
                    
                    <!-- Background -->
                    <div class="absolute inset-0 bg-gradient-to-br from-slate-900 via-navy to-slate-800"></div>
                    
                    <!-- Decorative Circles -->
                    <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-teal-500 opacity-10 blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-64 h-64 rounded-full bg-indigo-500 opacity-10 blur-3xl"></div>

                    <!-- Content -->
                    <div class="absolute inset-0 p-8 flex flex-col justify-between text-white">
                        
                        <!-- Header -->
                        <div class="flex justify-between items-start">
                            <div>
                                <h3 class="font-bold text-lg tracking-wider">CIPHER</h3>
                                <p class="text-[0.65rem] opacity-70 uppercase tracking-widest">Community Member</p>
                            </div>
                            <!-- Mock Chip -->
                            <div class="w-12 h-9 rounded bg-gradient-to-br from-yellow-200 to-yellow-500 border border-yellow-600 opacity-90 shadow-inner flex items-center justify-center">
                                <div class="w-full h-[1px] bg-yellow-700/30"></div>
                            </div>
                        </div>

                        <!-- Card Number -->
                        <div class="text-center">
                            <p class="font-mono text-xl sm:text-2xl tracking-widest text-shadow-sm">
                                {{ chunk_split($card->card_number, 4, ' ') }}
                            </p>
                        </div>

                        <!-- Footer -->
                        <div class="flex justify-between items-end">
                            <div>
                                <p class="text-[0.65rem] opacity-60 uppercase mb-1">Card Holder</p>
                                <p class="font-medium tracking-wide uppercase">{{ $user->name }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[0.65rem] opacity-60 uppercase mb-1">Member Since</p>
                                <p class="font-medium tracking-wide">{{ $card->issued_at->format('M Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex flex-col gap-3">
                    <button onclick="window.print()" class="inline-flex items-center justify-center gap-2 text-sm text-teal-600 font-bold hover:text-teal-700 transition px-4 py-2 border border-teal-200 rounded-lg hover:bg-teal-50 w-full">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download / Print Card
                    </button>
                    
                    <button onclick="shareCard()" class="inline-flex items-center justify-center gap-2 text-sm text-indigo-600 font-bold hover:text-indigo-700 transition px-4 py-2 border border-indigo-200 rounded-lg hover:bg-indigo-50 w-full">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z" />
                        </svg>
                        Share Digitally
                    </button>
                </div>
            </div>

            <!-- Perks List -->
            <div class="space-y-6">
                <h3 class="text-lg font-bold text-navy border-b border-gray-100 pb-2">Active Perks</h3>
                
                @if($card->perks->count() > 0)
                    <div class="space-y-4">
                        @foreach($card->perks as $perk)
                            <div class="flex items-center gap-4 p-4 bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition perk-item">
                                <div class="w-12 h-12 rounded-full flex items-center justify-center shrink-0
                                    {{ $perk->perk_type === 'food' ? 'bg-orange-100 text-orange-600' : '' }}
                                    {{ $perk->perk_type === 'travel' ? 'bg-blue-100 text-blue-600' : '' }}
                                    {{ $perk->perk_type === 'shopping' ? 'bg-purple-100 text-purple-600' : '' }}
                                    {{ $perk->perk_type === 'medical' ? 'bg-red-100 text-red-600' : '' }}
                                    {{ $perk->perk_type === 'education' ? 'bg-green-100 text-green-600' : '' }}
                                    {{ !in_array($perk->perk_type, ['food','travel','shopping','medical','education']) ? 'bg-gray-100 text-gray-600' : '' }}
                                ">
                                    <!-- Icons based on type -->
                                    @if($perk->perk_type === 'food')
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg> <!-- Placeholder icon -->
                                    @elseif($perk->perk_type === 'medical')
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                                    @else
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h4 class="font-bold text-navy">{{ ucfirst($perk->perk_type) }} Benefit</h4>
                                    <p class="text-sm text-slate-600">{{ $perk->description }}</p>
                                    <p class="text-xs font-bold text-teal-600 mt-1">{{ number_format($perk->discount_percentage, 0) }}% OFF</p>
                                </div>
                                <button onclick="copyPerkCode('{{ $perk->perk_type }}')" class="p-2 text-slate-400 hover:text-indigo-600 transition">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-slate-400 bg-slate-50 rounded-xl border border-dashed border-gray-200">
                        <p>No specific partner perks assigned yet.</p>
                    </div>
                @endif

                <!-- Default Community Benefits -->
                <h3 class="text-lg font-bold text-navy border-b border-gray-100 pb-2 mt-8">Community Benefits</h3>
                <p class="text-sm text-slate-500 mb-4">All CIPHER members enjoy these benefits:</p>
                
                <div class="grid grid-cols-1 gap-3">
                    <div class="flex items-center gap-3 p-3 bg-blue-50 rounded-lg border border-blue-100">
                        <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-navy text-sm">Bus & Train Travel</h4>
                            <p class="text-xs text-slate-500">Discounted tickets on partner transport</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3 p-3 bg-orange-50 rounded-lg border border-orange-100">
                        <div class="w-10 h-10 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-navy text-sm">Shopping Discounts</h4>
                            <p class="text-xs text-slate-500">Special rates at partner businesses</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3 p-3 bg-green-50 rounded-lg border border-green-100">
                        <div class="w-10 h-10 bg-green-100 text-green-600 rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.701 2.701 0 00-1.5-.454M9 6v2m3-2v2m3-2v2M9 3h.01M12 3h.01M15 3h.01M21 21v-7a2 2 0 00-2-2H5a2 2 0 00-2 2v7h18z" /></svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-navy text-sm">Food & Dining</h4>
                            <p class="text-xs text-slate-500">Exclusive offers at partner restaurants</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3 p-3 bg-purple-50 rounded-lg border border-purple-100">
                        <div class="w-10 h-10 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center shrink-0">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div>
                            <h4 class="font-semibold text-navy text-sm">Royalty Returns</h4>
                            <p class="text-xs text-slate-500">Profit share from invested projects</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Copy perk code to clipboard
            function copyPerkCode(perkType) {
                const code = 'CIPHER-' + perkType.toUpperCase() + '-' + Math.floor(Math.random() * 10000);
                navigator.clipboard.writeText(code).then(() => {
                    // Show success feedback
                    const button = event.target.closest('button');
                    const originalHTML = button.innerHTML;
                    button.innerHTML = `
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    `;
                    button.classList.add('text-green-600');
                    
                    setTimeout(() => {
                        button.innerHTML = originalHTML;
                        button.classList.remove('text-green-600');
                    }, 2000);
                });
            }

            // Share card digitally
            function shareCard() {
                const shareData = {
                    title: 'My CIPHER Membership Card',
                    text: 'Check out my CIPHER membership card with amazing perks!',
                    url: window.location.href
                };

                if (navigator.share) {
                    navigator.share(shareData)
                        .then(() => console.log('Successfully shared'))
                        .catch((error) => console.log('Error sharing', error));
                } else {
                    // Fallback: Copy to clipboard
                    navigator.clipboard.writeText(window.location.href);
                    alert('Link copied to clipboard');
                }
            }

            // Add interactive hover effects to perk items
            document.addEventListener('DOMContentLoaded', function() {
                const perkItems = document.querySelectorAll('.perk-item');
                perkItems.forEach(item => {
                    item.addEventListener('mouseenter', function() {
                        this.style.transform = 'translateX(4px)';
                        this.style.boxShadow = '0 10px 15px -3px rgba(0, 0, 0, 0.1)';
                    });
                    
                    item.addEventListener('mouseleave', function() {
                        this.style.transform = 'translateX(0)';
                        this.style.boxShadow = '0 1px 3px 0 rgba(0, 0, 0, 0.1)';
                    });
                });
            });
        </script>
    @endpush
</x-layouts.subscriber>
