<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with('role');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role_id', $request->role);
        }

        $users = $query->latest()->paginate(10);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load(['role', 'subscription', 'payments', 'activityLogs' => fn($q) => $q->latest()->limit(10)]);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role' => ['exists:roles,id'],
            'status' => ['in:active,inactive,banned'],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->has('role')) {
            $user->update(['role_id' => $request->role]);
        }

        if ($request->has('status')) {
            $user->update(['status' => $request->status]);
        }

        // Log activity
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'user_updated',
            'description' => "Updated user: {$user->name}",
            'entity_type' => 'User',
            'entity_id' => $user->id,
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Activate a user.
     */
    public function activate(Request $request, User $user)
    {
        $user->update(['status' => 'active']);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'user_activated',
            'description' => "Activated user: {$user->name}",
            'entity_type' => 'User',
            'entity_id' => $user->id,
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'User activated successfully.');
    }

    /**
     * Deactivate a user.
     */
    public function deactivate(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot deactivate yourself.');
        }

        $user->update(['status' => 'inactive']);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'user_deactivated',
            'description' => "Deactivated user: {$user->name}",
            'entity_type' => 'User',
            'entity_id' => $user->id,
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'User deactivated successfully.');
    }

    /**
     * Ban a user.
     */
    public function ban(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot ban yourself.');
        }

        $user->update(['status' => 'banned']);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'user_banned',
            'description' => "Banned user: {$user->name}",
            'entity_type' => 'User',
            'entity_id' => $user->id,
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'User banned successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'user_deleted',
            'description' => "Deleted user: {$user->name} ({$user->email})",
            'entity_type' => 'User',
            'entity_id' => $user->id,
            'ip_address' => $request->ip(),
        ]);

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }
}
