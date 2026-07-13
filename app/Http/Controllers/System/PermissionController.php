<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PermissionController extends Controller
{
    private function canManage(): bool
    {
        return auth()->user()?->hasRole('admin') || auth()->user()->hasPermission('permissions.manage');
    }

    public function index(): View
    {
        abort_unless($this->canManage(), 403);

        $sidebarItems = config('permissions');
        $roles = Role::orderBy('name')->get();

        return view('system.permissions.index', compact('sidebarItems', 'roles'));
    }

    public function update(Request $request): RedirectResponse
    {
        abort_unless($this->canManage(), 403);
        $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string'],
        ]);

        $role = Role::findOrFail($request->input('role_id'));
        $role->update(['permissions' => $request->input('permissions', [])]);

        return redirect()->route('system.permissions.index', ['role' => $role->id])
            ->with('success', "Permissions updated for role '{$role->name}'.");
    }

    public function toggle(Request $request): JsonResponse
    {
        abort_unless($this->canManage(), 403);

        $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
            'permission' => ['required', 'string'],
            'enabled' => ['required', 'boolean'],
        ]);

        $role = Role::findOrFail($request->role_id);
        $perms = $role->permissions ?? [];

        if ($request->enabled) {
            if (!in_array($request->permission, $perms)) {
                $perms[] = $request->permission;
            }
        } else {
            $perms = array_values(array_filter($perms, fn($p) => $p !== $request->permission));
        }

        $role->update(['permissions' => $perms]);

        return response()->json(['success' => true]);
    }
}
