@extends('components.layouts.base')

@section('title', 'Log in - CIPHER')
@section('body_class', 'bg-white')

@section('root')
<div class="min-h-screen flex">
    <!-- Left side - Gradient with illustration -->
    <div class="hidden lg:flex lg:w-1/2 gradient-hero flex-col justify-center items-center p-12">
        <div class="max-w-md text-center">
            <!-- Logo -->
            <div class="flex items-center justify-center space-x-3 mb-8">
                <div class="w-12 h-12 rounded-xl bg-teal flex items-center justify-center">
                    <span class="text-white font-bold text-xl">C</span>
                </div>
                <span class="text-3xl font-bold text-white">CIPHER</span>
            </div>
            
            <!-- Illustration placeholder -->
            <div class="mb-8">
                <svg class="w-64 h-64 mx-auto text-white/20" viewBox="0 0 200 200" fill="currentColor">
                    <circle cx="100" cy="100" r="80" stroke="currentColor" stroke-width="2" fill="none"/>
                    <circle cx="60" cy="80" r="15" opacity="0.5"/>
                    <circle cx="140" cy="80" r="15" opacity="0.5"/>
                    <circle cx="100" cy="140" r="15" opacity="0.5"/>
                    <line x1="60" y1="80" x2="100" y2="140" stroke="currentColor" stroke-width="2" opacity="0.3"/>
                    <line x1="140" y1="80" x2="100" y2="140" stroke="currentColor" stroke-width="2" opacity="0.3"/>
                    <line x1="60" y1="80" x2="140" y2="80" stroke="currentColor" stroke-width="2" opacity="0.3"/>
                </svg>
            </div>
            
            <h2 class="text-2xl font-bold text-white mb-4">Welcome Back</h2>
            <p class="text-white/70">
                Access your portfolio, track investments, and view your rewards — all in one secure dashboard.
            </p>
        </div>
        
        <!-- Trust indicators -->
        <div class="mt-12 flex items-center space-x-6 text-white/50 text-sm">
            <div class="flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                Secure Login
            </div>
            <div class="flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Data Protected
            </div>
        </div>
    </div>
    
    <!-- Right side - Login form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
        <div class="w-full max-w-md">
            <!-- Mobile logo -->
            <div class="lg:hidden flex items-center justify-center space-x-2 mb-8">
                <div class="w-10 h-10 rounded-lg gradient-hero flex items-center justify-center">
                    <span class="text-white font-bold">C</span>
                </div>
                <span class="text-2xl font-bold text-navy">CIPHER</span>
            </div>
            
            <h1 class="text-2xl font-bold text-navy mb-2">Sign in to your account</h1>
            <p class="text-slate mb-8">
                Don't have an account? 
                <a href="{{ route('register') }}" class="text-teal hover:underline font-medium">Get started for free</a>
            </p>

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-4 p-4 rounded-lg bg-light-teal text-teal text-sm">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-navy mb-1.5">Email address</label>
                    <input id="email" 
                           name="email" 
                           type="email" 
                           value="{{ old('email') }}" 
                           required 
                           autofocus 
                           autocomplete="username"
                           class="input-field"
                           placeholder="you@example.com">
                    @error('email')
                        <p class="mt-1.5 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label for="password" class="block text-sm font-medium text-navy">Password</label>
                        <a href="{{ route('password.request') }}" class="text-sm text-teal hover:underline">Forgot password?</a>
                    </div>
                    <input id="password" 
                           name="password" 
                           type="password" 
                           required 
                           autocomplete="current-password"
                           class="input-field"
                           placeholder="••••••••">
                    @error('password')
                        <p class="mt-1.5 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input id="remember_me" 
                           name="remember" 
                           type="checkbox" 
                           class="w-4 h-4 rounded border-gray-300 text-teal focus:ring-teal">
                    <label for="remember_me" class="ml-2 text-sm text-slate">Remember me</label>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-primary w-full">
                    Sign in
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
