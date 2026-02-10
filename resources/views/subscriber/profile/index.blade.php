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

                <!-- Participation Mode -->
                <div>
                    <label class="block text-sm font-semibold text-navy mb-3">Participation Mode</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="cursor-pointer relative">
                            <input type="radio" name="participation_mode" value="auto" {{ old('participation_mode', $user->participation_mode) === 'auto' ? 'checked' : '' }} class="peer sr-only">
                            <div class="p-4 rounded-xl border-2 border-gray-200 peer-checked:border-teal-500 peer-checked:bg-teal-50 transition hover:border-teal-200">
                                <div class="flex items-center gap-2 mb-1">
                                    <div class="w-4 h-4 rounded-full border border-gray-300 peer-checked:border-teal-500 peer-checked:bg-teal-500 flex items-center justify-center">
                                        <div class="w-1.5 h-1.5 rounded-full bg-white opacity-0 peer-checked:opacity-100"></div>
                                    </div>
                                    <span class="font-bold text-navy">Auto Mode</span>
                                </div>
                                <p class="text-xs text-slate-500 ml-6">Funds are automatically allocated to active projects.</p>
                            </div>
                        </label>

                        <label class="cursor-pointer relative">
                            <input type="radio" name="participation_mode" value="manual" {{ old('participation_mode', $user->participation_mode) === 'manual' ? 'checked' : '' }} class="peer sr-only">
                            <div class="p-4 rounded-xl border-2 border-gray-200 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 transition hover:border-indigo-200">
                                <div class="flex items-center gap-2 mb-1">
                                    <div class="w-4 h-4 rounded-full border border-gray-300 peer-checked:border-indigo-500 peer-checked:bg-indigo-500 flex items-center justify-center">
                                        <div class="w-1.5 h-1.5 rounded-full bg-white opacity-0 peer-checked:opacity-100"></div>
                                    </div>
                                    <span class="font-bold text-navy">Manual Mode</span>
                                </div>
                                <p class="text-xs text-slate-500 ml-6">You actively choose which projects to support.</p>
                            </div>
                        </label>
                    </div>
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

        <!-- Payment Reminder Settings -->
        <div class="card p-6 md:p-8" role="region" aria-label="Payment Reminder Settings">
            <div class="mb-6">
                <h3 class="text-lg font-bold text-navy">Payment Reminder Settings</h3>
                <p class="text-sm text-slate-500 mt-1">Configure how you want to receive payment reminders.</p>
            </div>

            <form method="post" action="{{ route('subscriber.profile.update') }}" class="space-y-6 max-w-xl">
                @csrf
                @method('patch')

                <div class="flex items-center gap-3">
                    <input type="hidden" name="name" value="{{ $user->name }}">
                    <input type="hidden" name="email" value="{{ $user->email }}">
                    <input type="hidden" name="participation_mode" value="{{ $user->participation_mode }}">
                    
                    <input 
                        type="checkbox" 
                        name="payment_reminders_enabled" 
                        id="payment_reminders_enabled"
                        {{ old('payment_reminders_enabled', $user->payment_reminders_enabled ?? true) ? 'checked' : '' }}
                        class="w-5 h-5 rounded border-gray-300 text-teal-600 focus:ring-teal-500"
                        aria-label="Enable Payment Reminders"
                    >
                    <label for="payment_reminders_enabled" class="text-sm font-semibold text-navy cursor-pointer">
                        Enable Payment Reminders
                    </label>
                </div>

                <div id="reminder-methods" class="{{ old('payment_reminders_enabled', $user->payment_reminders_enabled ?? true) ? '' : 'opacity-50 pointer-events-none' }}">
                    <div>
                        <label for="payment_reminder_method" class="block text-sm font-semibold text-navy mb-1.5">Reminder Method</label>
                        <select 
                            name="payment_reminder_method" 
                            id="payment_reminder_method"
                            class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-navy"
                        >
                            <option value="email" {{ old('payment_reminder_method', $user->payment_reminder_method ?? 'email') === 'email' ? 'selected' : '' }}>
                                Email
                            </option>
                            <option value="sms" {{ old('payment_reminder_method', $user->payment_reminder_method ?? 'email') === 'sms' ? 'selected' : '' }}>
                                SMS
                            </option>
                            <option value="both" {{ old('payment_reminder_method', $user->payment_reminder_method ?? 'email') === 'both' ? 'selected' : '' }}>
                                Email and SMS
                            </option>
                        </select>
                        @error('payment_reminder_method') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="payment_reminder_days" class="block text-sm font-semibold text-navy mb-1.5">Reminder Days</label>
                        <select 
                            name="payment_reminder_days" 
                            id="payment_reminder_days"
                            class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-navy"
                        >
                            <option value="1" {{ old('payment_reminder_days', $user->payment_reminder_days ?? 3) === 1 ? 'selected' : '' }}>
                                1 day before
                            </option>
                            <option value="2" {{ old('payment_reminder_days', $user->payment_reminder_days ?? 3) === 2 ? 'selected' : '' }}>
                                2 days before
                            </option>
                            <option value="3" {{ old('payment_reminder_days', $user->payment_reminder_days ?? 3) === 3 ? 'selected' : '' }}>
                                3 days before
                            </option>
                            <option value="5" {{ old('payment_reminder_days', $user->payment_reminder_days ?? 3) === 5 ? 'selected' : '' }}>
                                5 days before
                            </option>
                            <option value="7" {{ old('payment_reminder_days', $user->payment_reminder_days ?? 3) === 7 ? 'selected' : '' }}>
                                7 days before
                            </option>
                            <option value="10" {{ old('payment_reminder_days', $user->payment_reminder_days ?? 3) === 10 ? 'selected' : '' }}>
                                10 days before
                            </option>
                            <option value="14" {{ old('payment_reminder_days', $user->payment_reminder_days ?? 3) === 14 ? 'selected' : '' }}>
                                14 days before
                            </option>
                        </select>
                        @error('payment_reminder_days') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center gap-4 pt-2">
                    <button type="submit" class="px-4 py-2 bg-navy hover:bg-slate-800 text-white text-sm font-bold rounded-lg transition shadow-sm">
                        Save Settings
                    </button>
                    @if (session('status') === 'profile-updated')
                        <p class="text-sm text-emerald-600 font-medium">Settings saved.</p>
                    @endif
                </div>
            </form>
        </div>

        <!-- Security Settings -->
        <div class="card p-6 md:p-8">
            <div class="mb-6">
                <h3 class="text-lg font-bold text-navy">Security Settings</h3>
                <p class="text-sm text-slate-500 mt-1">Manage your account security preferences.</p>
            </div>

            <form method="post" action="{{ route('subscriber.profile.update') }}" class="space-y-6 max-w-xl">
                @csrf
                @method('patch')

                <div class="flex items-center gap-3">
                    <input type="hidden" name="name" value="{{ $user->name }}">
                    <input type="hidden" name="email" value="{{ $user->email }}">
                    <input type="hidden" name="participation_mode" value="{{ $user->participation_mode }}">
                    
                    <input 
                        type="checkbox" 
                        name="two_factor_enabled" 
                        id="two_factor_enabled"
                        {{ old('two_factor_enabled', $user->two_factor_enabled ?? false) ? 'checked' : '' }}
                        class="w-5 h-5 rounded border-gray-300 text-teal-600 focus:ring-teal-500"
                        aria-label="Enable Two-Factor Authentication"
                    >
                    <label for="two_factor_enabled" class="text-sm font-semibold text-navy cursor-pointer">
                        Enable Two-Factor Authentication
                    </label>
                </div>

                <div id="two-factor-methods" class="{{ old('two_factor_enabled', $user->two_factor_enabled ?? false) ? '' : 'opacity-50 pointer-events-none' }}">
                    <div>
                        <label for="two_factor_method" class="block text-sm font-semibold text-navy mb-1.5">Authentication Method</label>
                        <select 
                            name="two_factor_method" 
                            id="two_factor_method"
                            class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-navy"
                        >
                            <option value="email" {{ old('two_factor_method', $user->two_factor_method ?? 'email') === 'email' ? 'selected' : '' }}>
                                Email Verification
                            </option>
                            <option value="sms" {{ old('two_factor_method', $user->two_factor_method ?? 'email') === 'sms' ? 'selected' : '' }}>
                                SMS Code
                            </option>
                            <option value="authenticator" {{ old('two_factor_method', $user->two_factor_method ?? 'email') === 'authenticator' ? 'selected' : '' }}>
                                Authenticator App (Google Authenticator)
                            </option>
                        </select>
                        @error('two_factor_method') <p class="text-red-500 text-xs mt-1 font-medium">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center gap-4 pt-2">
                    <button type="submit" class="px-4 py-2 bg-navy hover:bg-slate-800 text-white text-sm font-bold rounded-lg transition shadow-sm">
                        Save Security Settings
                    </button>
                    @if (session('status') === 'profile-updated')
                        <p class="text-sm text-emerald-600 font-medium">Settings saved.</p>
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const remindersEnabled = document.getElementById('payment_reminders_enabled');
            const reminderMethods = document.getElementById('reminder-methods');
            const twoFactorEnabled = document.getElementById('two_factor_enabled');
            const twoFactorMethods = document.getElementById('two-factor-methods');
            
            if (remindersEnabled && reminderMethods) {
                remindersEnabled.addEventListener('change', function() {
                    if (this.checked) {
                        reminderMethods.classList.remove('opacity-50', 'pointer-events-none');
                        reminderMethods.style.animation = 'fadeIn 0.3s ease-out';
                    } else {
                        reminderMethods.classList.add('opacity-50', 'pointer-events-none');
                    }
                });
            }

            if (twoFactorEnabled && twoFactorMethods) {
                twoFactorEnabled.addEventListener('change', function() {
                    if (this.checked) {
                        twoFactorMethods.classList.remove('opacity-50', 'pointer-events-none');
                        twoFactorMethods.style.animation = 'fadeIn 0.3s ease-out';
                    } else {
                        twoFactorMethods.classList.add('opacity-50', 'pointer-events-none');
                    }
                });
            }
        });
    </script>
</x-layouts.subscriber>
