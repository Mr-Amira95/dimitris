<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search', '');

        $roles = Role::withCount('users')
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->get();

        return view('admin.roles.index', compact('roles', 'search'));
    }

    public function create()
    {
        $permissions = Role::PERMISSIONS;

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:50|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:' . implode(',', array_keys(Role::PERMISSIONS)),
        ]);

        Role::create([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'permissions' => $request->permissions ?? [],
            'is_system'   => false,
        ]);

        return redirect()->route('admin.roles.index')
            ->with('success', "Role \"{$request->name}\" created successfully.");
    }

    public function edit(Role $role)
    {
        $permissions = Role::PERMISSIONS;

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name'        => 'required|string|max:50|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|in:' . implode(',', array_keys(Role::PERMISSIONS)),
        ]);

        $role->update([
            'name'        => $request->name,
            'slug'        => Str::slug($request->name),
            'permissions' => $request->permissions ?? [],
        ]);

        return redirect()->route('admin.roles.index')
            ->with('success', "Role \"{$role->name}\" updated successfully.");
    }

    public function destroy(Role $role)
    {
        if ($role->is_system) {
            return back()->withErrors(['error' => 'System roles cannot be deleted.']);
        }

        if ($role->users()->count() > 0) {
            return back()->withErrors(['error' => "Cannot delete role \"{$role->name}\" — it is assigned to {$role->users()->count()} user(s). Reassign those users first."]);
        }

        $role->delete();

        return redirect()->route('admin.roles.index')
            ->with('success', "Role \"{$role->name}\" deleted.");
    }
}
