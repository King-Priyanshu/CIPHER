<x-layouts.subscriber>
    <x-slot:title>
        Profile Settings
    </x-slot:title>

    <div class="max-w-3xl space-y-6">
        
        <!-- Profile Information -->
        <div class="card p-6 md:p-8">
            <div class="mb-6">
                <h3 class="text-lg font-bold text-navy">Profile Information</h3>
                <p class="text-sm text-slate-500 mt-1">Update your account's profile information and email address.</p>
            </div>

            <form method="post" action="{{ route('subscriber.profile.update') }}" class="space-y-6 max-w-xl">
                @csrf
                @method('patch')

                <div>
                    <label for="name" class="block text-sm font-semibold text-navy mb-1.5">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required 
                           class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-navy placeholder-slate-400">
                    @error('name') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-navy mb-1.5">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required 
                           class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-navy placeholder-slate-400">
                    @error('email') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-4 pt-2">
                    <button type="submit" class="px-4 py-2 bg-navy hover:bg-slate-800 text-white text-sm font-bold rounded-lg transition shadow-sm">
                        Save Changes
                    </button>
                    @if (session('status') === 'profile-updated')
                        <p class="text-sm text-emerald-600 font-medium">Saved successfully.</p>
                    @endif
                </div>
            </form>
        </div>

        <!-- Update Password -->
        <div class="card p-6 md:p-8">
            <div class="mb-6">
                <h3 class="text-lg font-bold text-navy">Update Password</h3>
                <p class="text-sm text-slate-500 mt-1">Ensure your account is using a long, random password to stay secure.</p>
            </div>

            <form method="post" action="{{ route('subscriber.profile.password') }}" class="space-y-6 max-w-xl">
                @csrf
                @method('put')

                <div>
                    <label for="current_password" class="block text-sm font-semibold text-navy mb-1.5">Current Password</label>
                    <input type="password" name="current_password" id="current_password" 
                           class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-navy">
                    @error('current_password') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-navy mb-1.5">New Password</label>
                    <input type="password" name="password" id="password" 
                           class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-navy">
                    @error('password') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-navy mb-1.5">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" 
                           class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-navy">
                </div>

                <div class="flex items-center gap-4 pt-2">
                    <button type="submit" class="px-4 py-2 bg-navy hover:bg-slate-800 text-white text-sm font-bold rounded-lg transition shadow-sm">
                        Update Password
                    </button>
                    @if (session('status') === 'password-updated')
                        <p class="text-sm text-emerald-600 font-medium">Password updated.</p>
                    @endif
                </div>
            </form>
        </div>
    </div>
</x-layouts.subscriber>
