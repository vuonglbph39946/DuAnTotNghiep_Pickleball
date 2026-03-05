<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    /**
     * Hiển thị màn phân quyền
     */
    public function index()
    {
        $users = User::with('role.permissions')->latest()->paginate(10);
        $roles = Role::with('permissions')->latest()->get();
        $permissions = Permission::latest()->get();

        // Build $modulesMap dynamically từ permissions trong DB
        $modulesMap = [];

        foreach ($permissions as $permission) {
            $permName = $permission->name;
            $permModules = $permission->modules; // cast array

            // TH1: Có cột modules định danh (VD: ["users"])
            if (!empty($permModules) && is_array($permModules)) {
                $parts = explode('.', $permName, 2);
                $action = (count($parts) === 2) ? $parts[1] : $permName;
                
                foreach ($permModules as $mod) {
                    if (!isset($modulesMap[$mod])) $modulesMap[$mod] = [];
                    if (!in_array($action, $modulesMap[$mod])) $modulesMap[$mod][] = $action;
                }
            } else {
                // TH2: Fallback parse từ name dạng "module.action"
                $parts = explode('.', $permName, 2);
                if (count($parts) === 2) {
                    [$mod, $act] = $parts;
                    if (!isset($modulesMap[$mod])) $modulesMap[$mod] = [];
                    if (!in_array($act, $modulesMap[$mod])) $modulesMap[$mod][] = $act;
                }
            }
        }

        // Sort modules và actions
        ksort($modulesMap);
        foreach ($modulesMap as &$acts) {
            sort($acts);
        }
        unset($acts);

        $modules = $modulesMap;

        return view('admin.permissions.index', compact('users', 'roles', 'permissions', 'modules'));
    }

    /**
     * Quản lý Roles
     */
    public function roles()
    {
        $roles = Role::with('permissions')->latest()->paginate(10);
        $permissions = Permission::orderBy('name')->get();

        // Danh sách các modules và actions
        $modules = [
            'users' => ['create', 'read', 'update', 'delete'],
            'categories' => ['create', 'read', 'update', 'delete'],
            'news' => ['create', 'read', 'update', 'delete'],
            'contacts' => ['create', 'read', 'update', 'delete'],
            'products' => ['create', 'read', 'update', 'delete'],
            'orders' => ['create', 'read', 'update', 'delete'],
        ];

        return view('admin.permissions.roles', compact('roles', 'permissions', 'modules'));
    }

    /**
     * Show role (for AJAX)
     */
    public function showRole(Request $request, Role $role)
    {
        $role->load('permissions');
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'role' => $role,
            ]);
        }
        
        return redirect()->route('admin.permissions.roles');
    }

    /**
     * Quản lý Permissions
     */
    public function permissions()
    {
        $permissions = Permission::latest()->paginate(10);

        // Danh sách các modules
        $modules = [
            'users' => ['create', 'read', 'update', 'delete'],
            'categories' => ['create', 'read', 'update', 'delete'],
            'news' => ['create', 'read', 'update', 'delete'],
            'contacts' => ['create', 'read', 'update', 'delete'],
            'products' => ['create', 'read', 'update', 'delete'],
            'orders' => ['create', 'read', 'update', 'delete'],
        ];

        return view('admin.permissions.permissions', compact('permissions', 'modules'));
    }

    /**
     * Tạo role mới
     */
    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'slug' => 'nullable|string|max:255|unique:roles,slug',
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Auto-generate slug from name if not provided
        if (empty($validated['slug']) && !empty($validated['name'])) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
            // Ensure uniqueness
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Role::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $role = Role::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? null,
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'] ?? true,
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->sync($validated['permissions']);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Role created successfully!',
                'role' => $role->load('permissions'),
            ]);
        }

        return redirect()->route('admin.permissions.roles')->with('success', 'Role created successfully!');
    }

    /**
     * Cập nhật role
     */
    public function updateRole(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'slug' => 'nullable|string|max:255|unique:roles,slug,' . $role->id,
            'description' => 'nullable|string',
            'status' => 'nullable|boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Auto-generate slug from name if not provided
        if (empty($validated['slug']) && !empty($validated['name'])) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
            // Ensure uniqueness
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Role::where('slug', $validated['slug'])->where('id', '!=', $role->id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $role->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'] ?? $role->slug,
            'description' => $validated['description'] ?? $role->description,
            'status' => $validated['status'] ?? $role->status,
        ]);

        if ($request->has('permissions')) {
            $role->permissions()->sync($validated['permissions']);
        } else {
            $role->permissions()->detach();
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully!',
                'role' => $role->fresh()->load('permissions'),
            ]);
        }

        return redirect()->route('admin.permissions.roles')->with('success', 'Role updated successfully!');
    }

    /**
     * Xóa role
     */
    public function destroyRole(Request $request, Role $role)
    {
        $role->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully!',
            ]);
        }

        return redirect()->route('admin.permissions.roles')->with('success', 'Role deleted successfully!');
    }

    /**
     * Toggle permission for role (quick update)
     */
    public function toggleRolePermission(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permission_name' => 'required|string',
            'add' => 'required|boolean',
        ]);

        // Find or create permission
        $permission = Permission::firstOrCreate(['name' => $validated['permission_name']]);

        if ($validated['add']) {
            $role->permissions()->syncWithoutDetaching([$permission->id]);
        } else {
            $role->permissions()->detach($permission->id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Permission updated successfully!',
            'role' => $role->fresh()->load('permissions'),
        ]);
    }

    /**
     * Tạo permission mới (hỗ trợ batch: nhiều modules × nhiều actions)
     */
    public function storePermission(Request $request)
    {
        $request->validate([
            'modules'     => 'required|array|min:1',
            'modules.*'   => 'string',
            'actions'     => 'required|array|min:1',
            'actions.*'   => 'string',
            'description' => 'nullable|string',
            'status'      => 'nullable|boolean',
        ]);

        $modules     = $request->input('modules');     // VD: ['users', 'products']
        $actions     = $request->input('actions');     // VD: ['create', 'read']
        $description = $request->input('description');
        $status      = $request->boolean('status', true);

        $created  = [];
        $skipped  = [];

        // Tạo tất cả tổ hợp module × action
        foreach ($modules as $mod) {
            foreach ($actions as $act) {
                $permName = strtolower($mod) . '.' . strtolower($act);

                // Bỏ qua nếu đã tồn tại
                if (Permission::where('name', $permName)->exists()) {
                    $skipped[] = $permName;
                    continue;
                }

                $perm = Permission::create([
                    'name'        => $permName,
                    'modules'     => json_encode([$mod]),
                    'description' => $description,
                    'status'      => $status,
                ]);
                $created[] = $perm->name;
            }
        }

        $total   = count($created);
        $skippedCount = count($skipped);

        if ($request->ajax()) {
            $msg = $total > 0
                ? "Đã tạo {$total} permission" . ($skippedCount > 0 ? ", bỏ qua {$skippedCount} đã tồn tại" : '') . '!'
                : "Tất cả permissions đã tồn tại ({$skippedCount} bỏ qua).";

            return response()->json([
                'success'  => $total > 0 || $skippedCount > 0,
                'message'  => $msg,
                'created'  => $created,
                'skipped'  => $skipped,
            ]);
        }

        return redirect()->route('admin.permissions.permissions')
            ->with('success', "Đã tạo {$total} permissions!");
    }

    /**
     * Cập nhật permission (single edit)
     */
    public function updatePermission(Request $request, Permission $permission)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'modules'     => 'nullable|array',
            'modules.*'   => 'string',
            'actions'     => 'nullable|array',
            'actions.*'   => 'string',
            'description' => 'nullable|string',
            'status'      => 'nullable|boolean',
        ]);

        // Tính modules từ tên permission (module = phần trước dấu chấm)
        $parts = explode('.', $validated['name'], 2);
        $moduleFromName = $parts[0] ?? null;
        $modulesToSave = $validated['modules'] ?? ($moduleFromName ? [$moduleFromName] : []);

        $permission->update([
            'name'        => $validated['name'],
            'modules'     => json_encode($modulesToSave),
            'description' => $validated['description'] ?? $permission->description,
            'status'      => $validated['status'] ?? $permission->status,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success'    => true,
                'message'    => 'Đã cập nhật permission!',
                'permission' => $permission->fresh()
            ]);
        }

        return redirect()->route('admin.permissions.permissions')->with('success', 'Permission updated successfully!');
    }


    /**
     * Xóa permission
     */
    public function destroyPermission(Request $request, Permission $permission)
    {
        $permission->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Permission deleted successfully!',
            ]);
        }

        return redirect()->route('admin.permissions.permissions')->with('success', 'Permission deleted successfully!');
    }

    /**
     * Cập nhật permissions cho user
     */
    public function updateUserPermissions(Request $request, User $user)
    {
        $validated = $request->validate([
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        if ($request->has('roles')) {
            $user->roles()->sync($validated['roles']);
        } else {
            $user->roles()->detach();
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'User permissions updated successfully!',
                'user' => $user->fresh()->load('roles.permissions'),
            ]);
        }

        return redirect()->route('admin.permissions.index')->with('success', 'User permissions updated successfully!');
    }
}
