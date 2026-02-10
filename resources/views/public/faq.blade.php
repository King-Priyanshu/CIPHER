<x-layouts.guest>
    <div class="bg-white font-sans text-slate-600">
        
        <!-- Hero Section -->
        <section class="relative pt-20 pb-16 overflow-hidden bg-slate-50">
            <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[300px] bg-gradient-to-r from-teal-50 to-blue-50 blur-3xl opacity-40 rounded-full pointer-events-none"></div>
            
            <div class="relative max-w-4xl mx-auto px-6 lg:px-8 text-center">
                <h1 class="text-4xl md:text-5xl font-bold text-navy tracking-tight mb-4">
                    Frequently Asked Questions
                </h1>
                <p class="text-xl text-slate-500 max-w-2xl mx-auto">
                    Everything you need to know about CIPHER and how we work.
                </p>
            </div>
        </section>

        <!-- FAQ Content -->
        <section class="py-16 bg-white">
            <div class="max-w-3xl mx-auto px-6 lg:px-8">
                
                <div class="space-y-6" x-data="{ openFaq: null }">
                    
                    <!-- FAQ Item 1 -->
                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        <button @click="openFaq = openFaq === 1 ? null : 1" class="w-full px-6 py-5 text-left flex items-center justify-between bg-white hover:bg-slate-50 transition">
                            <span class="font-semibold text-navy">What is CIPHER?</span>
                            <svg class="w-5 h-5 text-slate-400 transition-transform" :class="openFaq === 1 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="openFaq === 1" x-collapse class="px-6 pb-5 text-slate-600 leading-relaxed">
                            CIPHER is a community-driven investment platform that pools contributions from members to fund vetted real-world projects. When these projects generate returns, the profits are distributed back to the community as rewards based on each member's contribution level.
                        </div>
                    </div>

                    <!-- FAQ Item 2 -->
                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        <button @click="openFaq = openFaq === 2 ? null : 2" class="w-full px-6 py-5 text-left flex items-center justify-between bg-white hover:bg-slate-50 transition">
                            <span class="font-semibold text-navy">How do subscriptions work?</span>
                            <svg class="w-5 h-5 text-slate-400 transition-transform" :class="openFaq === 2 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="openFaq === 2" x-collapse class="px-6 pb-5 text-slate-600 leading-relaxed">
                            We offer three subscription tiers: Seed (₹29/mo), Growth (₹79/mo), and Visionary (₹199/mo). Your subscription fee goes into our community fund pools, which are then allocated to vetted projects. Higher tiers receive priority rewards and additional voting rights on project selection.
                        </div>
                    </div>

                    <!-- FAQ Item 3 -->
                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        <button @click="openFaq = openFaq === 3 ? null : 3" class="w-full px-6 py-5 text-left flex items-center justify-between bg-white hover:bg-slate-50 transition">
                            <span class="font-semibold text-navy">How are rewards calculated?</span>
                            <svg class="w-5 h-5 text-slate-400 transition-transform" :class="openFaq === 3 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="openFaq === 3" x-collapse class="px-6 pb-5 text-slate-600 leading-relaxed">
                            Rewards are distributed proportionally based on your subscription tier and how long you've been an active member. When a project generates returns, the profits flow into our reward pools and are distributed weekly to eligible members. You can track your rewards in real-time from your dashboard.
                        </div>
                    </div>

                    <!-- FAQ Item 4 -->
                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        <button @click="openFaq = openFaq === 4 ? null : 4" class="w-full px-6 py-5 text-left flex items-center justify-between bg-white hover:bg-slate-50 transition">
                            <span class="font-semibold text-navy">What payment methods do you accept?</span>
                            <svg class="w-5 h-5 text-slate-400 transition-transform" :class="openFaq === 4 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="openFaq === 4" x-collapse class="px-6 pb-5 text-slate-600 leading-relaxed">
                            We accept payments through Razorpay, which supports UPI, Credit/Debit Cards, Netbanking, and popular wallets like Paytm, PhonePe, and Google Pay. All transactions are processed securely with bank-level encryption.
                        </div>
                    </div>

                    <!-- FAQ Item 5 -->
                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        <button @click="openFaq = openFaq === 5 ? null : 5" class="w-full px-6 py-5 text-left flex items-center justify-between bg-white hover:bg-slate-50 transition">
                            <span class="font-semibold text-navy">Can I cancel my subscription?</span>
                            <svg class="w-5 h-5 text-slate-400 transition-transform" :class="openFaq === 5 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="openFaq === 5" x-collapse class="px-6 pb-5 text-slate-600 leading-relaxed">
                            Yes, you can cancel your subscription at any time from your dashboard. When you cancel, you'll retain access until the end of your current billing period. Any accrued rewards will still be distributed as scheduled.
                        </div>
                    </div>

                    <!-- FAQ Item 6 -->
                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        <button @click="openFaq = openFaq === 6 ? null : 6" class="w-full px-6 py-5 text-left flex items-center justify-between bg-white hover:bg-slate-50 transition">
                            <span class="font-semibold text-navy">How are projects selected?</span>
                            <svg class="w-5 h-5 text-slate-400 transition-transform" :class="openFaq === 6 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="openFaq === 6" x-collapse class="px-6 pb-5 text-slate-600 leading-relaxed">
                            Our team conducts rigorous due diligence on every project before it's added to the platform. We evaluate business viability, team experience, market potential, and risk factors. Growth and Visionary members also get voting rights to influence project selection.
                        </div>
                    </div>

                    <!-- FAQ Item 7 -->
                    <div class="border border-gray-200 rounded-xl overflow-hidden">
                        <button @click="openFaq = openFaq === 7 ? null : 7" class="w-full px-6 py-5 text-left flex items-center justify-between bg-white hover:bg-slate-50 transition">
                            <span class="font-semibold text-navy">Is my money safe?</span>
                            <svg class="w-5 h-5 text-slate-400 transition-transform" :class="openFaq === 7 ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="openFaq === 7" x-collapse class="px-6 pb-5 text-slate-600 leading-relaxed">
                            We prioritize transparency and security. All funds are held in audited accounts, and we provide detailed reports on fund allocation and project performance. However, like any investment, there are inherent risks. We encourage members to only contribute what they can afford.
                        </div>
                    </div>

                </div>

                <!-- Contact CTA -->
                <div class="mt-12 p-8 bg-slate-50 rounded-2xl border border-gray-100 text-center">
                    <h3 class="text-lg font-bold text-navy mb-2">Still have questions?</h3>
                    <p class="text-slate-500 mb-4">Our support team is here to help you.</p>
                    <a href="mailto:support@cipher.community" class="inline-flex items-center gap-2 px-6 py-3 bg-navy text-white font-semibold rounded-xl hover:bg-slate-800 transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Contact Support
                    </a>
                </div>

            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-navy text-slate-400 py-12 border-t border-slate-800">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-6">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-teal-500 flex items-center justify-center text-white font-bold text-sm">C</div>
                    <span class="text-white font-bold text-lg">CIPHER</span>
                </div>
                <div class="text-sm">
                    &copy; {{ date('Y') }} Cipher Community. All rights reserved.
                </div>
                <div class="flex gap-6 text-sm">
                    <a href="{{ route('page.show', 'privacy-policy') }}" class="hover:text-white transition">Privacy</a>
                    <a href="{{ route('page.show', 'terms-of-service') }}" class="hover:text-white transition">Terms</a>
                    <a href="#" class="hover:text-white transition">Contact</a>
                </div>
            </div>
        </footer>

    </div>
</x-layouts.guest>
