<x-layouts.base>
    @section('title', 'Admin Login – CIPHER')
    @section('body_class', 'bg-navy')

    @section('root')
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-md">
            
            <!-- Logo -->
            <div class="text-center mb-10">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-teal flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <span class="text-2xl font-bold text-white">CIPHER</span>
                </a>
                <p class="text-slate-400 mt-2 text-sm">Administrator Portal</p>
            </div>

            <!-- Login Card -->
            <div class="bg-white rounded-2xl shadow-2xl p-8">
                <h2 class="text-2xl font-bold text-navy text-center mb-2">Admin Sign In</h2>
                <p class="text-slate-500 text-center text-sm mb-8">Access the administration panel</p>

                <!-- Session Status -->
                @if (session('status'))
                    <div class="mb-4 text-sm text-green-600 bg-green-50 rounded-lg p-3 text-center">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login') }}">
                    @csrf

                    <!-- Email -->
                    <div class="mb-5">
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">Email Address</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                               class="input-field h-12 text-base" placeholder="admin@cipher.com">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-6">
                        <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                               class="input-field h-12 text-base" placeholder="••••••••">
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between mb-6">
                        <label for="remember_me" class="flex items-center">
                            <input id="remember_me" type="checkbox" name="remember"
                                   class="rounded border-slate-300 text-teal focus:ring-teal">
                            <span class="ml-2 text-sm text-slate-600">Remember me</span>
                        </label>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="w-full btn-primary h-12 text-base">
                        Sign In to Admin Panel
                    </button>
                </form>
            </div>

            <!-- Back Link -->
            <div class="text-center mt-8">
                <a href="{{ route('home') }}" class="text-slate-400 text-sm hover:text-white transition">
                    &larr; Back to Website
                </a>
            </div>
        </div>
    </div>
    @endsection
</x-layouts.base>
