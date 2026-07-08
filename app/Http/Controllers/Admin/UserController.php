<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request): View
    {
        $query = User::with('roles')->orderBy('name');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('roles', fn($q) => $q->where('name', $request->role));
        }

        $users       = $query->paginate(15)->withQueryString();
        $roles       = Role::orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles', 'permissions'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role'     => 'nullable|exists:roles,name',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        if (!empty($validated['role'])) {
            $user->assignRole($validated['role']);
        }

        activity()
            ->performedOn($user)
            ->withProperties(['role' => $validated['role'] ?? 'none', 'ip' => $request->ip()])
            ->log("User '{$user->name}' created");

        return redirect()->route('dashboard.users.index')->with('success', "User '{$user->name}' created successfully!");
    }

    /**
     * Update an existing user.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name  = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        activity()
            ->performedOn($user)
            ->withProperties(['ip' => $request->ip()])
            ->log("User '{$user->name}' updated");

        return redirect()->route('dashboard.users.index')->with('success', "User '{$user->name}' updated successfully!");
    }

    /**
     * Assign roles to a user.
     */
    public function assignRoles(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'roles'   => 'nullable|array',
            'roles.*' => 'exists:roles,name',
        ]);

        $roles = $request->input('roles', []);
        $user->syncRoles($roles);

        activity()
            ->performedOn($user)
            ->withProperties(['roles' => $roles, 'ip' => $request->ip()])
            ->log("Roles updated for user '{$user->name}'");

        return redirect()->route('dashboard.users.index')->with('success', "Roles updated for {$user->name}!");
    }

    /**
     * Delete a user.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return redirect()->route('dashboard.users.index')->with('error', 'You cannot delete your own account!');
        }

        $name = $user->name;
        $user->delete();

        activity()
            ->withProperties(['user' => $name, 'ip' => $request->ip()])
            ->log("User '{$name}' deleted");

        return redirect()->route('dashboard.users.index')->with('success', "User '{$name}' deleted successfully!");
    }
}
