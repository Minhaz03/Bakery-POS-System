<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    /**
     * Display roles & permissions management page.
     */
    public function index(): View
    {
        $roles       = Role::with('permissions')->orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();

        $groupLabels = [
            'users' => 'User Management',
            'roles' => 'Roles & Permissions',
            'products' => 'Products & Catalog',
            'categories' => 'Product Categories',
            'units' => 'Units of Measurement',
            'brands' => 'Brands',
            'taxes' => 'Tax Settings',
            'stock' => 'Stock & Inventory Ledger',
            'suppliers' => 'Suppliers & Vendor Payments',
            'purchases' => 'Purchase Orders',
            'pos' => 'POS Terminal',
            'sales' => 'Sales & Orders',
            'customers' => 'Customers',
            'expenses' => 'Expenses & Expense Categories',
            'reports' => 'Reports',
            'analytics' => 'Analytics & Insights',
            'settings' => 'System Settings',
            'modules' => 'Module Management',
            'recipes' => 'Recipes & Formulas',
            'production' => 'Production Orders',
            'supply' => 'Supply Orders',
            'custom' => 'Custom Orders',
            'warehouse' => 'Warehouse Management',
            'branch' => 'Branch Management',
            'activity' => 'Activity Logs',
            'tickets' => 'Support Tickets',
        ];

        // Group permissions by prefix (e.g., "users.view" -> "users")
        $groupedPermissions = $permissions->groupBy(function ($p) {
            return explode('.', $p->name)[0];
        });

        return view('admin.roles.index', compact('roles', 'permissions', 'groupedPermissions', 'groupLabels'));
    }

    /**
     * Store a new role.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:125|unique:roles,name',
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);

        activity()
            ->withProperties(['role' => $role->name, 'ip' => $request->ip()])
            ->log("Role '{$role->name}' created");

        return redirect()->route('dashboard.roles.index')->with('success', "Role '{$role->name}' created!");
    }

    /**
     * Update a role's name.
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        // Protect built-in roles
        if (in_array($role->name, ['Admin', 'super-admin'])) {
            return redirect()->route('dashboard.roles.index')->with('error', 'Cannot rename a system role.');
        }

        $request->validate([
            'name' => 'required|string|max:125|unique:roles,name,' . $role->id,
        ]);

        $old = $role->name;
        $role->update(['name' => $request->name]);

        activity()
            ->withProperties(['old' => $old, 'new' => $role->name, 'ip' => $request->ip()])
            ->log("Role renamed from '{$old}' to '{$role->name}'");

        return redirect()->route('dashboard.roles.index')->with('success', "Role renamed to '{$role->name}'!");
    }

    /**
     * Sync permissions for a role.
     */
    public function syncPermissions(Request $request, Role $role): RedirectResponse
    {
        $request->validate([
            'permissions'   => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $permissions = $request->input('permissions', []);
        $role->syncPermissions($permissions);

        activity()
            ->withProperties(['role' => $role->name, 'permissions' => $permissions, 'ip' => $request->ip()])
            ->log("Permissions synced for role '{$role->name}'");

        return redirect()->route('dashboard.roles.index')->with('success', "Permissions updated for '{$role->name}'!");
    }

    /**
     * Delete a role.
     */
    public function destroy(Request $request, Role $role): RedirectResponse
    {
        if (in_array($role->name, ['Admin', 'super-admin'])) {
            return redirect()->route('dashboard.roles.index')->with('error', 'Cannot delete a system role.');
        }

        $name = $role->name;
        $role->delete();

        activity()
            ->withProperties(['role' => $name, 'ip' => $request->ip()])
            ->log("Role '{$name}' deleted");

        return redirect()->route('dashboard.roles.index')->with('success', "Role '{$name}' deleted!");
    }
}
