@extends('components.layouts.base')

@section('title', 'CIPHER - Invest Together, Grow Together')
@section('body_class', 'bg-slate-50')

@section('root')
    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-teal-900 text-white">
        <div class="max-w-6xl mx-auto px-6 py-20">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <!-- Logo -->
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-12 h-12 bg-gradient-to-br from-teal-400 to-teal-600 rounded-xl flex items-center justify-center transform rotate-45">
                            <!-- Lock Icon -->
                            <svg class="w-6 h-6 text-white transform -rotate-45" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <span class="text-3xl font-bold">CIPHER</span>
                    </div>
                  
                    <h1 class="text-5xl font-bold mb-6 leading-tight">
                        Invest Together,<br />
                        <span class="text-teal-400">Grow Together</span>
                    </h1>
                    <p class="text-xl text-gray-300 mb-8">
                        Join a transparent community investment platform. Pool your subscriptions, invest in real projects, and share the profits—all with complete transparency.
                    </p>
                  
                    <div class="flex gap-4">
                        <a href="{{ route('register') }}" class="bg-gradient-to-r from-teal-500 to-teal-600 text-white px-8 py-4 rounded-lg font-semibold text-lg shadow-xl hover:shadow-2xl hover:scale-105 transition-all flex items-center gap-2">
                            Start Investing
                            <!-- ArrowRight -->
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                        <a href="#how-it-works" class="bg-white/10 backdrop-blur-sm border-2 border-white/30 text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-white/20 transition-all">
                            Learn More
                        </a>
                    </div>
                  
                    <!-- Trust Indicators -->
                    <div class="flex gap-8 mt-12 pt-8 border-t border-white/20">
                        <div>
                            <div class="text-3xl font-bold text-teal-400">$2.4M+</div>
                            <div class="text-sm text-gray-400">Total Invested</div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-teal-400">1,200+</div>
                            <div class="text-sm text-gray-400">Active Members</div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-teal-400">98%</div>
                            <div class="text-sm text-gray-400">Satisfaction</div>
                        </div>
                    </div>
                </div>
                
                <!-- Hero Illustration -->
                <div class="relative">
                    <div class="bg-white/10 backdrop-blur-md rounded-2xl p-8 border border-white/20">
                        <div class="grid grid-cols-3 gap-4 mb-6">
                            @foreach(range(1, 6) as $i)
                                <div class="aspect-square bg-gradient-to-br from-teal-500/20 to-teal-600/20 rounded-lg border border-teal-400/30 flex items-center justify-center">
                                    <!-- Users Icon -->
                                    <svg class="w-6 h-6 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                            @endforeach
                        </div>
                        <div class="h-2 bg-white/10 rounded-full overflow-hidden">
                            <div class="h-full w-3/4 bg-gradient-to-r from-teal-400 to-teal-600 rounded-full animate-pulse"></div>
                        </div>
                        <div class="text-sm text-gray-300 mt-3 text-center">Community Pool: $145,000</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <div class="max-w-6xl mx-auto px-6 py-20">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-slate-900 mb-4">How CIPHER Works</h2>
            <p class="text-xl text-gray-600">Simple, transparent, community-driven investing</p>
        </div>
        
        <div class="grid md:grid-cols-4 gap-8">
            <!-- Step 1: Subscribe -->
            <div class="relative">
                <div class="bg-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 border border-gray-100 h-full">
                    <div class="w-12 h-12 bg-gradient-to-br from-teal-100 to-teal-200 rounded-xl flex items-center justify-center mb-4">
                        <!-- DollarSign -->
                        <svg class="w-6 h-6 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="absolute -top-3 -right-3 w-8 h-8 bg-gradient-to-br from-teal-500 to-teal-600 text-white rounded-full flex items-center justify-center font-bold">
                        1
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Subscribe</h3>
                    <p class="text-gray-600">Choose a plan that fits your investment goals</p>
                </div>
                <div class="hidden md:block absolute top-1/2 -right-4 w-8 h-0.5 bg-gradient-to-r from-teal-300 to-transparent"></div>
            </div>

            <!-- Step 2: Pool Funds -->
            <div class="relative">
                <div class="bg-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 border border-gray-100 h-full">
                    <div class="w-12 h-12 bg-gradient-to-br from-teal-100 to-teal-200 rounded-xl flex items-center justify-center mb-4">
                        <!-- Users -->
                        <svg class="w-6 h-6 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="absolute -top-3 -right-3 w-8 h-8 bg-gradient-to-br from-teal-500 to-teal-600 text-white rounded-full flex items-center justify-center font-bold">
                        2
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Pool Funds</h3>
                    <p class="text-gray-600">Your subscription joins the community investment pool</p>
                </div>
                <div class="hidden md:block absolute top-1/2 -right-4 w-8 h-0.5 bg-gradient-to-r from-teal-300 to-transparent"></div>
            </div>

            <!-- Step 3: Invest Together -->
            <div class="relative">
                <div class="bg-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 border border-gray-100 h-full">
                    <div class="w-12 h-12 bg-gradient-to-br from-teal-100 to-teal-200 rounded-xl flex items-center justify-center mb-4">
                        <!-- TrendingUp -->
                        <svg class="w-6 h-6 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </div>
                    <div class="absolute -top-3 -right-3 w-8 h-8 bg-gradient-to-br from-teal-500 to-teal-600 text-white rounded-full flex items-center justify-center font-bold">
                        3
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Invest Together</h3>
                    <p class="text-gray-600">Funds are allocated to vetted projects</p>
                </div>
                <div class="hidden md:block absolute top-1/2 -right-4 w-8 h-0.5 bg-gradient-to-r from-teal-300 to-transparent"></div>
            </div>

            <!-- Step 4: Share Profits -->
            <div class="relative">
                <div class="bg-white rounded-xl p-6 shadow-lg hover:shadow-xl transition-all hover:-translate-y-1 border border-gray-100 h-full">
                    <div class="w-12 h-12 bg-gradient-to-br from-teal-100 to-teal-200 rounded-xl flex items-center justify-center mb-4">
                        <!-- PieChart -->
                        <svg class="w-6 h-6 text-teal-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                        </svg>
                    </div>
                    <div class="absolute -top-3 -right-3 w-8 h-8 bg-gradient-to-br from-teal-500 to-teal-600 text-white rounded-full flex items-center justify-center font-bold">
                        4
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Share Profits</h3>
                    <p class="text-gray-600">Returns distributed transparently to all members</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Trust Section -->
    <div class="bg-slate-50 py-20">
        <div class="max-w-6xl mx-auto px-6">
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Trust 1 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-teal-500 to-teal-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <!-- Shield -->
                        <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Fully Transparent</h3>
                    <p class="text-gray-600">Every transaction tracked and visible to all members</p>
                </div>

                <!-- Trust 2 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-teal-500 to-teal-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <!-- Lock -->
                        <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Secure & Safe</h3>
                    <p class="text-gray-600">Bank-level security protecting your investments</p>
                </div>

                <!-- Trust 3 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-teal-500 to-teal-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <!-- BarChart3 -->
                        <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Proven Returns</h3>
                    <p class="text-gray-600">Consistent profit distribution to our community</p>
                </div>
            </div>
        </div>
    </div>
@endsection
