@extends('components.layouts.base')

@section('title', 'CIPHER - Transparent Community Investment')
@section('body_class', 'bg-white')

@section('root')
<div class="min-h-screen flex flex-col">
    <!-- Navigation -->
    <nav x-data="{ open: false }" class="bg-white border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-lg gradient-hero flex items-center justify-center">
                            <span class="text-white font-bold text-sm">C</span>
                        </div>
                        <span class="text-xl font-bold text-navy">CIPHER</span>
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden sm:flex sm:items-center sm:space-x-8">
                    <a href="{{ route('home') }}" class="text-slate hover:text-navy font-medium transition-colors">Home</a>
                    <a href="#how-it-works" class="text-slate hover:text-navy font-medium transition-colors">How it Works</a>
                    <a href="#pricing" class="text-slate hover:text-navy font-medium transition-colors">Pricing</a>
                    
                    @auth
                        <a href="{{ Auth::user()->hasRole('admin') ? route('admin.dashboard') : route('subscriber.dashboard') }}" 
                           class="text-slate hover:text-navy font-medium transition-colors">Dashboard</a>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="btn-ghost text-sm">Log Out</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-slate hover:text-navy font-medium transition-colors">Log in</a>
                        <a href="{{ route('register') }}" class="btn-primary text-sm">Get Started</a>
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="flex items-center sm:hidden">
                    <button @click="open = !open" type="button" class="p-2 rounded-md text-slate hover:text-navy hover:bg-light-teal focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path :class="{'hidden': open, 'inline-flex': ! open }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden border-t border-gray-100">
            <div class="pt-2 pb-3 space-y-1 px-4">
                <a href="{{ route('home') }}" class="block py-2 text-slate hover:text-navy font-medium">Home</a>
                <a href="#how-it-works" class="block py-2 text-slate hover:text-navy font-medium">How it Works</a>
                <a href="#pricing" class="block py-2 text-slate hover:text-navy font-medium">Pricing</a>
                
                @auth
                    <a href="{{ Auth::user()->hasRole('admin') ? route('admin.dashboard') : route('subscriber.dashboard') }}" 
                       class="block py-2 text-slate hover:text-navy font-medium">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left py-2 text-slate hover:text-navy font-medium">Log Out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block py-2 text-slate hover:text-navy font-medium">Log in</a>
                    <a href="{{ route('register') }}" class="block py-2 text-teal font-semibold">Get Started</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <main class="flex-1">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-navy text-white py-12 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Brand -->
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-8 h-8 rounded-lg bg-teal flex items-center justify-center">
                            <span class="text-white font-bold text-sm">C</span>
                        </div>
                        <span class="text-xl font-bold">CIPHER</span>
                    </div>
                    <p class="text-gray-400 text-sm max-w-md">
                        Transparent, community-driven investment platform. Pool funds collectively and invest in selected projects with fair profit distribution.
                    </p>
                </div>
                
                <!-- Links -->
                <div>
                    <h4 class="font-semibold mb-4">Platform</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-white transition-colors">How it Works</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Pricing</a></li>
                        <li><a href="#" class="hover:text-white transition-colors">Projects</a></li>
                    </ul>
                </div>
                
                <!-- Legal -->
                <div>
                    <h4 class="font-semibold mb-4">Legal</h4>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="{{ route('page.show', 'terms-conditions') }}" class="hover:text-white transition-colors">Terms & Conditions</a></li>
                        <li><a href="{{ route('page.show', 'privacy-policy') }}" class="hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a href="{{ route('page.show', 'about-us') }}" class="hover:text-white transition-colors">About Us</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="border-t border-gray-700 mt-8 pt-8 text-center text-sm text-gray-400">
                © {{ date('Y') }} CIPHER. All rights reserved.
            </div>
        </div>
    </footer>
</div>
@endsection
