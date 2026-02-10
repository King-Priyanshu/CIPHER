<?php

namespace App\Http\Controllers\Subscriber;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ProfileController extends Controller
{
    public function index()
    {
        return view('subscriber.profile.index', [
            'user' => Auth::user(),
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . Auth::id()],
            'participation_mode' => ['required', 'in:auto,manual'],
            'payment_reminders_enabled' => ['boolean'],
            'payment_reminder_method' => ['string', 'in:email,sms,both'],
            'payment_reminder_days' => ['integer', 'min:1', 'max:14'],
            'two_factor_enabled' => ['boolean'],
            'two_factor_method' => ['string', 'in:email,sms,authenticator'],
        ]);

        $request->user()->update([
            'name' => $request->name,
            'email' => $request->email,
            'participation_mode' => $request->participation_mode,
            'payment_reminders_enabled' => $request->boolean('payment_reminders_enabled'),
            'payment_reminder_method' => $request->payment_reminder_method ?? 'email',
            'payment_reminder_days' => $request->payment_reminder_days ?? 3,
            'two_factor_enabled' => $request->boolean('two_factor_enabled'),
            'two_factor_method' => $request->two_factor_method ?? 'email',
        ]);

        return back()->with('status', 'profile-updated');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }
}
