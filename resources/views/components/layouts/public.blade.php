@extends('components.layouts.base')

@section('title', $title ?? 'CIPHER - Public Projects')
@section('body_class', 'bg-slate-900 text-white min-h-screen flex flex-col')

@section('root')
    <!-- Navbar -->
    <nav class="absolute top-0 w-full z-50 border-b border-white/5 bg-slate-900/50 backdrop-blur-md">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2 group">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-teal-400 to-indigo-500 flex items-center justify-center shadow-lg shadow-teal-500/20 group-hover:scale-110 transition-transform">
                    <span class="font-bold text-white text-sm">C</span>
                </div>
                <span class="font-bold text-xl tracking-tight">CIPHER</span>
            </a>

            <div class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-300">
                <a href="/" class="hover:text-white transition-colors">Home</a>
                <a href="{{ route('projects.index') }}" class="text-white">Projects</a>
                <a href="/#how-it-works" class="hover:text-white transition-colors">How it Works</a>
            </div>

            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ route('subscriber.dashboard') }}" class="px-5 py-2.5 bg-white/10 hover:bg-white/20 text-white rounded-lg text-sm font-bold transition-all border border-white/5">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-bold text-slate-300 hover:text-white px-4 py-2">Login</a>
                    <a href="{{ route('register') }}" class="px-5 py-2.5 bg-teal-500 hover:bg-teal-400 text-slate-900 rounded-lg text-sm font-bold transition-all shadow-lg shadow-teal-500/20 hover:scale-105">
                        Start Investing
                    </a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1 pt-20">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="border-t border-white/5 bg-slate-950 py-12 mt-20">
        <div class="max-w-7xl mx-auto px-6 grid md:grid-cols-4 gap-12 text-sm text-slate-400">
            <div class="col-span-2">
                <a href="/" class="flex items-center gap-2 mb-4">
                    <div class="w-6 h-6 rounded bg-gradient-to-br from-teal-400 to-indigo-500 flex items-center justify-center">
                        <span class="font-bold text-white text-xs">C</span>
                    </div>
                    <span class="font-bold text-lg text-white tracking-tight">CIPHER</span>
                </a>
                <p class="max-w-xs leading-relaxed">
                    The world's first transparent community investment DAO. 
                    Pool resources, access vetted high-yield projects, and track every cent on the blockchain.
                </p>
            </div>
            <div>
                <h4 class="font-bold text-white mb-4">Platform</h4>
                <ul class="space-y-2">
                    <li><a href="{{ route('projects.index') }}" class="hover:text-teal-400 transition-colors">Browse Projects</a></li>
                    <li><a href="#" class="hover:text-teal-400 transition-colors">How it Works</a></li>
                    <li><a href="#" class="hover:text-teal-400 transition-colors">Pricing</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-white mb-4">Legal</h4>
                <ul class="space-y-2">
                    <li><a href="#" class="hover:text-teal-400 transition-colors">Privacy Policy</a></li>
                    <li><a href="#" class="hover:text-teal-400 transition-colors">Terms of Service</a></li>
                    <li><a href="#" class="hover:text-teal-400 transition-colors">Risks</a></li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-6 mt-12 pt-8 border-t border-white/5 text-center text-xs text-slate-600">
            &copy; {{ date('Y') }} Cipher Investment Platform. All rights reserved.
        </div>
    </footer>
@endsection
