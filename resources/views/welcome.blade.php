@extends('components.layouts.base')

@section('title', 'CIPHER - Premium Investment Platform')
@section('body_class', 'bg-slate-900 overflow-x-hidden')

@section('root')
    <!-- Scroll Progress Bar -->
    <div class="fixed top-0 left-0 w-full h-1.5 z-50 bg-slate-900/0">
        <div id="scroll-progress" class="h-full bg-gradient-to-r from-teal-400 via-emerald-400 to-indigo-500 w-0 shadow-[0_0_15px_rgba(45,212,191,0.7)] transition-all duration-100 ease-out rounded-r-full"></div>
    </div>

    <!-- Hero Section -->
    <div class="relative min-h-screen flex items-center justify-center overflow-hidden perspective-container">
        <!-- Animated Background -->
        <div class="absolute inset-0 z-0">
            <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-slate-900 via-[#0F172A] to-[#0A2540] animate-gradient"></div>
            <!-- Floating 3D Blobs -->
            <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-teal-500/20 rounded-full blur-[100px] animate-float-premium" style="animation-delay: 0s"></div>
            <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-indigo-500/20 rounded-full blur-[100px] animate-float-premium" style="animation-delay: -2s"></div>
            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-slate-800/30 rounded-full blur-[120px] mix-blend-overlay"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-6 grid lg:grid-cols-2 gap-16 items-center">
            <!-- Text Content -->
            <div class="space-y-8 text-center lg:text-left">
                <div class="animate-reveal-blur" style="animation-delay: 0.2s">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/5 border border-white/10 backdrop-blur-md mb-6 hover:bg-white/10 transition-colors cursor-default">
                        <span class="w-2 h-2 rounded-full bg-teal-400 animate-pulse"></span>
                        <span class="text-teal-400 text-sm font-medium tracking-wide">Community Investment Pool</span>
                    </div>
                    
                    <h1 class="text-5xl lg:text-7xl font-black text-white leading-tight tracking-tight">
                        Invest <span class="text-transparent bg-clip-text bg-gradient-to-r from-teal-400 to-cyan-400">Together</span>,<br />
                        Grow <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-purple-400">Wealth</span>
                    </h1>
                </div>

                <p class="text-xl text-slate-300 max-w-xl mx-auto lg:mx-0 leading-relaxed animate-reveal-blur" style="animation-delay: 0.4s">
                    Join the world's first transparent community investment DAO. Pool resources, access vetted high-yield projects, and track every cent on the blockchain.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start animate-reveal-3d" style="animation-delay: 0.6s">
                    <a href="{{ route('register') }}" class="group relative px-8 py-4 bg-teal-500 hover:bg-teal-400 text-slate-900 font-bold rounded-xl transition-all hover:scale-105 active:scale-95 shadow-[0_0_30px_rgba(45,212,191,0.3)] hover:shadow-[0_0_50px_rgba(45,212,191,0.5)] overflow-hidden">
                        <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300 skew-y-12"></div>
                        <span class="relative flex items-center gap-2">
                            Start Investing
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                        </span>
                    </a>
                    <a href="#how-it-works" class="px-8 py-4 bg-white/5 border border-white/10 text-white font-bold rounded-xl hover:bg-white/10 transition-all hover:scale-105 active:scale-95 backdrop-blur-md">
                        How it Works
                    </a>
                </div>

                <!-- Stats Preview -->
                <div class="pt-8 flex gap-12 justify-center lg:justify-start animate-reveal-3d" style="animation-delay: 0.8s">
                    <div>
                        <div class="text-3xl font-black text-white mb-1"><span class="count-up" data-target="2">0</span>.4M+</div>
                        <div class="text-sm text-slate-400 font-medium uppercase tracking-wider">Total Value</div>
                    </div>
                    <div>
                        <div class="text-3xl font-black text-white mb-1"><span class="count-up" data-target="1200">0</span>+</div>
                        <div class="text-sm text-slate-400 font-medium uppercase tracking-wider">Members</div>
                    </div>
                </div>
            </div>

            <!-- 3D Illustration -->
            <div class="relative hidden lg:block animate-slide-right-premium" style="animation-delay: 0.4s">
                <div class="relative z-10 bg-gradient-to-br from-slate-800/80 to-slate-900/80 backdrop-blur-xl border border-white/10 p-8 rounded-3xl shadow-2xl transform rotate-y-12 hover:rotate-y-0 transition-transform duration-700 perspective-container group">
                    <!-- Glass Shine -->
                    <div class="absolute inset-0 bg-gradient-to-tr from-white/5 to-transparent rounded-3xl pointer-events-none"></div>
                    
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <div class="text-slate-400 text-sm font-medium">Community Balance</div>
                            <div class="text-4xl font-black text-white mt-1">₹145,290.00</div>
                        </div>
                        <div class="w-12 h-12 bg-teal-500/20 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                    </div>

                    <!-- Graph visualization (simplified) -->
                    <div class="h-32 flex items-end gap-2 mb-8 pr-4">
                        @foreach([40, 65, 45, 80, 55, 90, 75, 100] as $h)
                            <div class="flex-1 bg-gradient-to-t from-teal-500/20 to-teal-400 rounded-t-sm hover:from-teal-400 hover:to-teal-300 transition-colors" style="height: {{ $h }}%"></div>
                        @endforeach
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center gap-4 p-4 bg-white/5 rounded-xl border border-white/5 hover:bg-white/10 transition-colors">
                            <div class="w-10 h-10 rounded-full bg-indigo-500/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-white font-bold">Dividends Paid</div>
                                <div class="text-xs text-slate-400">2 hours ago</div>
                            </div>
                            <div class="ml-auto text-teal-400 font-bold">+₹1,240</div>
                        </div>
                        <div class="flex items-center gap-4 p-4 bg-white/5 rounded-xl border border-white/5 hover:bg-white/10 transition-colors">
                            <div class="w-10 h-10 rounded-full bg-purple-500/20 flex items-center justify-center">
                                <svg class="w-5 h-5 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-white font-bold">New Member</div>
                                <div class="text-xs text-slate-400">5 mins ago</div>
                            </div>
                            <div class="ml-auto text-slate-400 text-sm">#1294</div>
                        </div>
                    </div>
                </div>
                
                <!-- Floating Elements behind -->
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-teal-400 rounded-2xl rotate-12 opacity-20 blur-xl animate-float-premium" style="animation-delay: 1s"></div>
                <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-indigo-500 rounded-full opacity-20 blur-xl animate-float-premium" style="animation-delay: 2s"></div>
            </div>
        </div>
    </div>

    <!-- Features Grid -->
    <div id="how-it-works" class="bg-slate-50 py-32 relative z-20">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-20 reveal">
                <span class="text-teal-600 font-bold tracking-wider uppercase text-sm mb-2 block">Process</span>
                <h2 class="text-4xl lg:text-5xl font-black text-slate-900 mb-6">How it Works</h2>
                <div class="h-1 w-20 bg-teal-500 mx-auto rounded-full"></div>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 stagger-3d reveal-trigger">
                @php
                    $steps = [
                        ['num' => '01', 'title' => 'Subscribe', 'desc' => 'Select a tier and join the exclusive pool.'],
                        ['num' => '02', 'title' => 'Pool', 'desc' => 'Funds are aggregated into a smart contract.'],
                        ['num' => '03', 'title' => 'Invest', 'desc' => 'Community votes on high-yield projects.'],
                        ['num' => '04', 'title' => 'Profit', 'desc' => 'Returns are airdropped to your wallet.']
                    ];
                @endphp
                @foreach($steps as $step)
                <div class="bg-white p-8 rounded-3xl shadow-xl hover:shadow-2xl transition-all hover:-translate-y-2 group border border-slate-100 relative overflow-hidden">
                    <div class="absolute -right-4 -top-4 text-9xl font-black text-slate-100 group-hover:text-teal-50 transition-colors z-0 select-none">{{ $step['num'] }}</div>
                    <div class="relative z-10">
                        <div class="w-14 h-14 bg-teal-500 text-white rounded-2xl flex items-center justify-center font-bold text-xl mb-6 shadow-lg shadow-teal-500/30 group-hover:scale-110 transition-transform">
                            {{ $step['num'] }}
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 mb-3">{{ $step['title'] }}</h3>
                        <p class="text-slate-500 leading-relaxed">{{ $step['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Scroll Progress
            window.addEventListener('scroll', () => {
                const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
                const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
                const scrolled = (winScroll / height) * 100;
                document.getElementById('scroll-progress').style.width = scrolled + "%";
            });

            // Count Up Animation
            const counters = document.querySelectorAll('.count-up');
            const runCounter = (el) => {
                const target = +el.getAttribute('data-target');
                const duration = 2000; // ms
                const step = target / (duration / 16); // 60fps
                
                let current = 0;
                const update = () => {
                    current += step;
                    if(current < target) {
                        el.innerText = Math.ceil(current);
                        requestAnimationFrame(update);
                    } else {
                        el.innerText = target;
                    }
                };
                update();
            };

            // Intersection Observer for Reveals
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        // Reveal specific elements
                        if (entry.target.classList.contains('reveal')) {
                            entry.target.classList.add('animate-reveal-3d');
                            entry.target.style.opacity = '1';
                        }
                        
                        // Staggered grids
                        if (entry.target.classList.contains('reveal-trigger')) {
                            entry.target.classList.add('is-visible');
                        }

                        // Trigger counters if visible
                         entry.target.querySelectorAll('.count-up').forEach(c => {
                            if(!c.hasAttribute('data-run')) {
                                c.setAttribute('data-run', 'true');
                                runCounter(c);
                            }
                        });


                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.15 });

            document.querySelectorAll('.reveal, .reveal-trigger, .animate-reveal-3d').forEach(el => observer.observe(el));
        });
    </script>
@endsection

