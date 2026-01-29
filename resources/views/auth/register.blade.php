@extends('components.layouts.base')

@section('title', 'Create Account - CIPHER')
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
            
            <!-- Illustration -->
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
            
            <h2 class="text-2xl font-bold text-white mb-4">Join the Community</h2>
            <p class="text-white/70">
                Start investing with as little as one subscription. Pool funds with others and access premium opportunities.
            </p>
        </div>
        
        <!-- Features -->
        <div class="mt-12 grid grid-cols-2 gap-4 text-white/70 text-sm max-w-sm">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Transparent Funds
            </div>
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Fair Rewards
            </div>
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Community Driven
            </div>
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-teal" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Secure Platform
            </div>
        </div>
    </div>
    
    <!-- Right side - Registration form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
        <div class="w-full max-w-md">
            <!-- Mobile logo -->
            <div class="lg:hidden flex items-center justify-center space-x-2 mb-8">
                <div class="w-10 h-10 rounded-lg gradient-hero flex items-center justify-center">
                    <span class="text-white font-bold">C</span>
                </div>
                <span class="text-2xl font-bold text-navy">CIPHER</span>
            </div>
            
            <h1 class="text-2xl font-bold text-navy mb-2">Create your account</h1>
            <p class="text-slate mb-8">
                Already have an account? 
                <a href="{{ route('login') }}" class="text-teal hover:underline font-medium">Sign in</a>
            </p>

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-navy mb-1.5">Full name</label>
                    <input id="name" 
                           name="name" 
                           type="text" 
                           value="{{ old('name') }}" 
                           required 
                           autofocus 
                           autocomplete="name"
                           class="input-field"
                           placeholder="John Doe">
                    @error('name')
                        <p class="mt-1.5 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-navy mb-1.5">Email address</label>
                    <input id="email" 
                           name="email" 
                           type="email" 
                           value="{{ old('email') }}" 
                           required 
                           autocomplete="username"
                           class="input-field"
                           placeholder="you@example.com">
                    @error('email')
                        <p class="mt-1.5 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-navy mb-1.5">Password</label>
                    <input id="password" 
                           name="password" 
                           type="password" 
                           required 
                           autocomplete="new-password"
                           class="input-field"
                           placeholder="At least 8 characters">
                    @error('password')
                        <p class="mt-1.5 text-sm text-error">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-navy mb-1.5">Confirm password</label>
                    <input id="password_confirmation" 
                           name="password_confirmation" 
                           type="password" 
                           required 
                           autocomplete="new-password"
                           class="input-field"
                           placeholder="Repeat your password">
                </div>

                <!-- Terms -->
                <div class="flex items-start">
                    <input id="terms" 
                           name="terms" 
                           type="checkbox" 
                           required
                           class="w-4 h-4 mt-0.5 rounded border-gray-300 text-teal focus:ring-teal">
                    <label for="terms" class="ml-2 text-sm text-slate">
                        I agree to the <a href="{{ route('page.show', 'terms-conditions') }}" class="text-teal hover:underline">Terms of Service</a> 
                        and <a href="{{ route('page.show', 'privacy-policy') }}" class="text-teal hover:underline">Privacy Policy</a>
                    </label>
                </div>

                <!-- Submit -->
                <button type="submit" class="btn-primary w-full">
                    Create account
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
