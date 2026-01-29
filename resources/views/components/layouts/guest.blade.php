@extends('components.layouts.base')

@section('body_class', 'bg-white')

@section('content')
    <div class="min-h-screen flex flex-col">
        <!-- Navigation -->
        <nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('home') }}" class="font-bold text-2xl text-indigo-600">
                                CIPHER
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <a href="{{ route('home') }}" class="inline-flex items-center px-1 pt-1 border-b-2 border-indigo-500 text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out">
                                Home
                            </a>
                            <a href="#" class="inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                How it Works
                            </a>
                        </div>
                    </div>

                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        @auth
                            <a href="{{ Auth::user()->hasRole('admin') ? route('admin.dashboard') : route('subscriber.dashboard') }}" class="text-sm text-gray-700 underline">Dashboard</a>
                            <form method="POST" action="{{ route('logout') }}" class="ml-4">
                                @csrf
                                <button type="submit" class="text-sm text-gray-700 underline">Log out</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="text-sm text-gray-700 underline">Log in</a>
                            <a href="{{ route('register') }}" class="ml-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">Register</a>
                        @endauth
                    </div>

                    <!-- Hamburger -->
                    <div class="-mr-2 flex items-center sm:hidden">
                        <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Responsive Navigation Menu -->
            <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    <x-responsive-nav-link :href="route('home')" :active="request()->routeIs('home')">
                        Home
                    </x-responsive-nav-link>
                     <x-responsive-nav-link href="#" :active="false">
                        How it Works
                    </x-responsive-nav-link>
                </div>

                <!-- Responsive Settings Options -->
                <div class="pt-4 pb-1 border-t border-gray-200">
                    <div class="mt-3 space-y-1">
                        @auth
                            <div class="px-4 mb-2">
                                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                            </div>
                            <x-responsive-nav-link :href="Auth::user()->hasRole('admin') ? route('admin.dashboard') : route('subscriber.dashboard')">
                                Dashboard
                            </x-responsive-nav-link>
                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-responsive-nav-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-responsive-nav-link>
                            </form>
                        @else
                            <x-responsive-nav-link :href="route('login')">
                                Log in
                            </x-responsive-nav-link>
                            <x-responsive-nav-link :href="route('register')">
                                Register
                            </x-responsive-nav-link>
                        @endauth
                    </div>
                </div>
        </nav>

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <h3 class="text-lg font-bold mb-4">CIPHER</h3>
                    <p class="text-gray-400 text-sm">Empowering communities through shared projects and transparent growth.</p>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Links</h3>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="{{ route('page.show', 'about-us') }}" class="hover:text-white">About Us</a></li>
                        <li><a href="{{ route('subscriber.projects.index') }}" class="hover:text-white">Projects</a></li>
                        <li><a href="#pricing" class="hover:text-white">Pricing</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-bold mb-4">Legal</h3>
                    <ul class="space-y-2 text-gray-400 text-sm">
                        <li><a href="{{ route('page.show', 'privacy-policy') }}" class="hover:text-white">Privacy Policy</a></li>
                        <li><a href="{{ route('page.show', 'terms-of-service') }}" class="hover:text-white">Terms of Service</a></li>
                    </ul>
                </div>
            </div>
        </footer>
    </div>
@endsection
