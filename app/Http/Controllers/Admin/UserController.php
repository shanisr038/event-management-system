<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    /**
     * Constructor - ensure only admins can access
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    /**
     * Display all users
     */
    public function index()
    {
        $users = User::with('roles')->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show form to edit user roles
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $userRoles = $user->roles->pluck('name')->toArray();
        
        return view('admin.users.edit', compact('user', 'roles', 'userRoles'));
    }

    /**
     * Update user roles
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'array',
            'roles.*' => 'exists:roles,name'
        ]);

        // Sync roles
        $user->syncRoles($request->roles ?? []);

        return redirect()->route('admin.users.index')
            ->with('success', 'User roles updated successfully!');
    }

    /**
     * Make user an organizer
     */
    public function makeOrganizer(User $user)
    {
        $user->assignRole('organizer');
        
        return back()->with('success', "{$user->name} is now an organizer.");
    }

    /**
     * Remove organizer role
     */
    public function removeOrganizer(User $user)
    {
        $user->removeRole('organizer');
        
        return back()->with('success', "Organizer role removed from {$user->name}.");
    }

    /**
     * Make user an admin
     */
    public function makeAdmin(User $user)
    {
        $user->assignRole('admin');
        
        return back()->with('success', "{$user->name} is now an admin.");
    }

    /**
     * Remove admin role
     */
    public function removeAdmin(User $user)
    {
        // Don't allow removing admin from yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', "You cannot remove admin role from yourself.");
        }
        
        $user->removeRole('admin');
        
        return back()->with('success', "Admin role removed from {$user->name}.");
    }

    /**
     * Toggle user active status (optional)
     */
    public function toggleStatus(User $user)
    {
        // You would need an 'is_active' column in users table
        // $user->update(['is_active' => !$user->is_active]);
        
        return back()->with('success', "User status updated.");
    }
}