<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Carbon\Carbon;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create()
    {
        $referralCode = session('referral_code');
        return view('auth.register', compact('referralCode'));
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['required', 'accepted'], // Mandatory as per wireframe
            'referral_code' => ['required', 'string', 'exists:users,referral_code'],
        ]);

        // Auto-create subscriber role if missing (to avoid manual seeding requirement)
        $subscriberRole = Role::firstOrCreate(
            ['slug' => 'subscriber'],
            [
                'name' => 'Subscriber',
                'description' => 'Standard user with subscription capabilities'
            ]
        );

        if (!$subscriberRole) {
            \Illuminate\Support\Facades\Log::error('Registration failed: Could not create Subscriber role.');
            return back()->withErrors(['email' => 'System error: Could not initialize user role.'])->withInput();
        }
        
        // Find referrer if referral code provided
        // Find referrer via Service
        $referrerId = null;
        if ($request->referral_code) {
           // We can rely on service logic if we want, or keep specific validation here.
           // Since validation 'exists:users,referral_code' already passed, user exists.
           // We just need to check role.
           
           $referrer = User::where('referral_code', $request->referral_code)->first();
           if ($referrer && ($referrer->hasRole('admin') || $referrer->hasRole('manager'))) {
               $referrerId = $referrer->id;
           } else {
               return back()->withErrors(['referral_code' => 'Invalid referral code or referrer not authorized.'])->withInput();
           }
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $subscriberRole->id,
                'terms_accepted_at' => Carbon::now(),
                'referred_by' => $referredBy,
            ]);

            \Illuminate\Support\Facades\Log::info('User created successfully: ' . $user->id);

            event(new Registered($user));

            Auth::login($user);

            return redirect(route('subscriber.dashboard'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Registration Error: ' . $e->getMessage());
            \Illuminate\Support\Facades\Log::error($e->getTraceAsString());
            return back()->withErrors(['email' => 'Registration failed: ' . $e->getMessage()])->withInput();
        }
    }
}
