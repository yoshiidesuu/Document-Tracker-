<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Services\UserActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function __construct(
        private readonly UserActivityService $userActivity,
    ) {}

    public function index(): View
    {
        abort_unless(auth()->user()->hasPermission('roles.list'), 403);

        $roles = Role::withCount('users')->orderBy('name')->get();
        return view('system.roles.index', compact('roles'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->hasPermission('roles.create'), 403);

        return view('system.roles.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('roles.create'), 403);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:100', 'unique:roles', 'regex:/^[a-z0-9-]+$/'],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['nullable', 'string'],
        ]);

        $permissions = array_filter(array_map('trim', explode("\n", $data['permissions'] ?? '')));
        $data['permissions'] = $permissions ?: null;
        $data['is_system'] = false;

        $role = Role::create($data);

        $this->userActivity->log('role_created', "Role created: {$role->name} ({$role->slug})", newData: $role->only(['name', 'slug', 'description']));

        return redirect()->route('system.roles.index')
            ->with('success', "Role '{$role->name}' created successfully.");
    }

    public function edit(Role $role): View
    {
        abort_unless(auth()->user()->hasPermission('roles.edit'), 403);

        return view('system.roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('roles.edit'), 403);

        $old = $role->only(['name', 'slug', 'description', 'is_system']);

        if ($role->is_system) {
            $data = $request->validate([
                'description' => ['nullable', 'string', 'max:1000'],
            ]);
            $role->update($data);
        } else {
            $data = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'slug' => ['required', 'string', 'max:100', Rule::unique('roles')->ignore($role->id), 'regex:/^[a-z0-9-]+$/'],
                'description' => ['nullable', 'string', 'max:1000'],
                'permissions' => ['nullable', 'string'],
            ]);

            $permissions = array_filter(array_map('trim', explode("\n", $data['permissions'] ?? '')));
            $data['permissions'] = $permissions ?: null;

            $role->update($data);
        }

        $new = $role->only(['name', 'slug', 'description', 'is_system']);
        $this->userActivity->log('role_updated', "Role updated: {$role->name} ({$role->slug})", oldData: $old, newData: $new);

        return redirect()->route('system.roles.index')
            ->with('success', "Role '{$role->name}' updated successfully.");
    }

    public function destroy(Role $role): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('roles.delete'), 403);
        if ($role->is_system) {
            return redirect()->route('system.roles.index')
                ->withErrors(['error' => "Cannot delete system role '{$role->name}'."]);
        }

        if ($role->users()->count() > 0) {
            return redirect()->route('system.roles.index')
                ->withErrors(['error' => "Cannot delete role '{$role->name}' because it has {$role->users()->count()} user(s) assigned. Remove all users from this role first."]);
        }

        $this->userActivity->log('role_deleted', "Role deleted: {$role->name} ({$role->slug})", oldData: $role->only(['name', 'slug', 'description']));
        $role->delete();

        return redirect()->route('system.roles.index')
            ->with('success', "Role '{$role->name}' deleted successfully.");
    }
}
