<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function roles()
    {
        $roles = Role::with('permissions')->get()->map(function ($role) {
            $role->users_count = $role->users()->count();
            return $role;
        });

        return Inertia::render('Admin/Roles/Index', [
            'roles' => $roles,
        ]);
    }

    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:' . config('permission.models.role') . ',name'],
            'permissions' => ['array'],
            'permissions.*' => ['exists:' . config('permission.models.permission') . ',name'],
        ]);

        $role = Role::create(['name' => $validated['name'], 'guard_name' => 'web']);

        if (!empty($validated['permissions'])) {
            $role->givePermissionTo($validated['permissions']);
        }

        Cache::forget('spatie.permission.cache');

        return redirect()->back()->with('success', 'تم إنشاء الدور بنجاح');
    }

    public function updateRole(Request $request, Role $role)
    {
        if (in_array($role->name, ['admin'])) {
            return redirect()->back()->with('error', 'لا يمكن تعديل الأدوار الأساسية');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:' . config('permission.models.role') . ',name,' . $role->id],
            'permissions' => ['array'],
            'permissions.*' => ['exists:' . config('permission.models.permission') . ',name'],
        ]);

        $role->update(['name' => $validated['name']]);
        $role->syncPermissions($validated['permissions'] ?? []);

        Cache::forget('spatie.permission.cache');

        return redirect()->back()->with('success', 'تم تحديث الدور بنجاح');
    }

    public function destroyRole(Role $role)
    {
        if (in_array($role->name, ['admin', 'teacher'])) {
            return redirect()->back()->with('error', 'لا يمكن حذف الأدوار الأساسية');
        }

        if ($role->users()->count() > 0) {
            return redirect()->back()->with('error', 'لا يمكن حذف دور لديه مستخدمين');
        }

        $role->delete();

        Cache::forget('spatie.permission.cache');

        return redirect()->back()->with('success', 'تم حذف الدور بنجاح');
    }

    public function permissions()
    {
        $permissions = Permission::all()->groupBy(function ($permission) {
            $parts = explode(' ', $permission->name);
            return $parts[0] ?? 'other';
        });

        return Inertia::render('Admin/Roles/Permissions', [
            'permissions' => $permissions,
        ]);
    }

    public function storePermission(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:' . config('permission.models.permission') . ',name'],
        ]);

        $permission = Permission::create(['name' => $validated['name'], 'guard_name' => 'web']);

        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permission);
        }

        Cache::forget('spatie.permission.cache');

        return redirect()->back()->with('success', 'تم إنشاء الصلاحية بنجاح');
    }

    public function destroyPermission(Permission $permission)
    {
        $permission->delete();

        Cache::forget('spatie.permission.cache');

        return redirect()->back()->with('success', 'تم حذف الصلاحية بنجاح');
    }

    public function editUserRoles(Request $request, \App\Models\User $user)
    {
        $roles = Role::all();
        $userRoles = $user->roles->pluck('name');

        return Inertia::render('Admin/Roles/UserRoles', [
            'user' => $user,
            'roles' => $roles,
            'userRoles' => $userRoles,
        ]);
    }

    public function updateUserRoles(Request $request, \App\Models\User $user)
    {
        $validated = $request->validate([
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['exists:' . config('permission.models.role') . ',name'],
        ]);

        $user->syncRoles($validated['roles']);

        Cache::forget('spatie.permission.cache');

        return redirect()->back()->with('success', 'تم تحديث أدوار المستخدم بنجاح');
    }
}
