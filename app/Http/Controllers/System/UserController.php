<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Office;
use App\Models\Role;
use App\Models\User;
use App\Rules\StrongPassword;
use App\Services\UserActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        private readonly UserActivityService $userActivity,
    ) {}

    public function index(Request $request): View
    {
        abort_unless(auth()->user()->hasPermission('users.list'), 403);

        $query = User::with('roles');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('firstname', 'like', "%{$search}%")
                    ->orWhere('lastname', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('id_number', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            if ($status === 'banned') {
                $query->where('banned', true);
            } elseif ($status === 'locked') {
                $query->where('locked', true);
            } elseif ($status === 'active') {
                $query->where('status', 'active')->where('banned', false)->where('locked', false);
            }
        }

        if ($roleSlug = $request->input('role')) {
            $query->whereHas('roles', fn ($q) => $q->where('slug', $roleSlug));
        }

        $sortField = $request->input('sort', 'created_at');
        $sortDir = $request->input('dir', 'desc');
        if (in_array($sortField, ['firstname', 'lastname', 'created_at', 'last_login_at', 'login_count', 'status'])) {
            $query->orderBy($sortField, $sortDir === 'asc' ? 'asc' : 'desc');
        }

        $users = $query->paginate(15)->withQueryString();
        $roles = Role::all();

        return view('system.users.index', compact('users', 'roles'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->hasPermission('users.create'), 403);

        $allRoles = Role::all();
        $departments = Department::orderBy('name')->get();
        $offices = Office::orderBy('name')->get();

        return view('system.users.create', compact('allRoles', 'departments', 'offices'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('users.create'), 403);

        $data = $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'middlename' => ['nullable', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'office_id' => ['nullable', 'integer', 'exists:offices,id'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'id_number' => ['nullable', 'string', 'max:100', 'unique:users'],
            'age' => ['nullable', 'integer', 'min:1', 'max:150'],
            'gender' => ['nullable', 'string', Rule::in(['male', 'female', 'other', 'prefer-not-to-say'])],
            'bday' => ['nullable', 'date'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        $data['name'] = trim(implode(' ', array_filter([
            $data['firstname'] ?? '',
            $data['middlename'] ?? '',
            $data['lastname'] ?? '',
        ])));

        $data['password'] = bcrypt($data['password']);

        $user = User::create($data);

        if ($request->has('roles')) {
            $user->roles()->sync($request->input('roles', []));
        }

        $this->userActivity->log('user_created', "User created: {$user->email}", userId: $user->id, newData: $user->only(['firstname', 'lastname', 'email', 'id_number', 'status']));

        return redirect()->route('system.users.view', $user->id)
            ->with('success', 'User created successfully.');
    }

    public function view(int $id): View
    {
        abort_unless(auth()->user()->hasPermission('users.view'), 403);

        $user = User::with('roles', 'department', 'office')->findOrFail($id);

        return view('system.users.view', compact('user'));
    }

    public function edit(int $id): View
    {
        abort_unless(auth()->user()->hasPermission('users.edit'), 403);

        $user = User::with('roles')->findOrFail($id);
        $allRoles = Role::all();
        $departments = Department::orderBy('name')->get();
        $offices = Office::orderBy('name')->get();

        return view('system.users.edit', compact('user', 'allRoles', 'departments', 'offices'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('users.edit'), 403);

        $user = User::findOrFail($id);

        $data = $request->validate([
            'firstname' => ['required', 'string', 'max:255'],
            'middlename' => ['nullable', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'department_id' => ['nullable', 'integer', 'exists:departments,id'],
            'office_id' => ['nullable', 'integer', 'exists:offices,id'],
            'id_number' => ['nullable', 'string', 'max:100', Rule::unique('users')->ignore($user->id)],
            'age' => ['nullable', 'integer', 'min:1', 'max:150'],
            'gender' => ['nullable', 'string', Rule::in(['male', 'female', 'other', 'prefer-not-to-say'])],
            'bday' => ['nullable', 'date'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['exists:roles,id'],
        ]);

        $data['name'] = trim(implode(' ', array_filter([
            $data['firstname'] ?? '',
            $data['middlename'] ?? '',
            $data['lastname'] ?? '',
        ])));

        $old = $user->only(['firstname', 'lastname', 'id_number', 'status', 'department_id', 'office_id', 'gender']);
        $user->update($data);

        if ($request->has('roles')) {
            $user->roles()->sync($request->input('roles', []));
        }

        $new = $user->only(['firstname', 'lastname', 'id_number', 'status', 'department_id', 'office_id', 'gender']);
        $this->userActivity->log('user_updated', "User profile updated: {$user->email}", userId: $user->id, oldData: $old, newData: $new);

        return redirect()->route('system.users.view', $user->id)
            ->with('success', 'User updated successfully.');
    }

    public function updatePassword(Request $request, int $id): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('users.reset-password'), 403);

        $user = User::findOrFail($id);

        $request->validate([
            'new_password' => ['required', 'string', new StrongPassword],
        ]);

        $user->forceFill([
            'password' => Hash::make($request->input('new_password')),
            'password_changed_at' => now(),
        ])->save();

        $this->userActivity->log('password_force_reset', "Password force reset by admin for: {$user->email}", userId: $user->id);

        return redirect()->route('system.users.view', $user->id)
            ->with('success', 'Password reset successfully.');
    }

    public function ban(int $id): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('users.ban'), 403);

        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            return redirect()->route('system.users.view', $id)
                ->withErrors(['error' => 'You cannot ban your own account.']);
        }
        $user->ban();

        return redirect()->route('system.users.view', $id)
            ->with('success', 'User banned successfully.');
    }

    public function unban(int $id): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('users.unban'), 403);

        $user = User::findOrFail($id);
        $user->unban();

        return redirect()->route('system.users.view', $id)
            ->with('success', 'User unbanned successfully.');
    }

    public function lock(int $id): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('users.lock'), 403);

        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            return redirect()->route('system.users.view', $id)
                ->withErrors(['error' => 'You cannot lock your own account.']);
        }
        $user->lock();

        return redirect()->route('system.users.view', $id)
            ->with('success', 'User locked successfully.');
    }

    public function unlock(int $id): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('users.unlock'), 403);

        $user = User::findOrFail($id);
        $user->unlock();

        return redirect()->route('system.users.view', $id)
            ->with('success', 'User unlocked successfully.');
    }

    public function forceLogout(int $id): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('users.force-logout'), 403);

        $user = User::findOrFail($id);

        DB::table('sessions')->where('user_id', $user->id)->delete();

        $user->forceFill(['remember_token' => null])->save();

        $this->userActivity->log('user_force_logout', "Admin forced logout for user: {$user->email}", userId: $user->id);

        return redirect()->route('system.users.view', $user->id)
            ->with('success', 'All sessions terminated for this user.');
    }

    public function bulkAction(Request $request): RedirectResponse
    {
        $request->validate([
            'selected' => ['required', 'array'],
            'selected.*' => ['exists:users,id'],
            'action' => ['required', 'string', Rule::in(['delete', 'ban', 'unban', 'lock', 'unlock'])],
        ]);

        $action = $request->input('action');

        $permMap = [
            'delete' => 'users.bulk-delete',
            'ban' => 'users.bulk-ban',
            'unban' => 'users.bulk-unban',
            'lock' => 'users.bulk-lock',
            'unlock' => 'users.bulk-unlock',
        ];
        abort_unless(auth()->user()->hasPermission($permMap[$action]), 403);

        $ids = $request->input('selected', []);
        $users = User::whereIn('id', $ids)->get();
        $count = 0;

        foreach ($users as $user) {
            if ($action === 'delete') {
                if ($user->id === auth()->id()) {
                    continue;
                }
                $this->userActivity->log('user_deleted', "User deleted: {$user->email} (ID: {$user->id})", userId: $user->id, oldData: $user->only(['firstname', 'lastname', 'email', 'id_number']));
                $user->delete();
                $count++;
            } elseif ($action === 'ban') {
                if ($user->id === auth()->id()) {
                    continue;
                }
                $user->ban();
                $count++;
            } elseif ($action === 'unban') {
                $user->unban();
                $count++;
            } elseif ($action === 'lock') {
                if ($user->id === auth()->id()) {
                    continue;
                }
                $user->lock();
                $count++;
            } elseif ($action === 'unlock') {
                $user->unlock();
                $count++;
            }
        }

        $actionLabel = match ($action) {
            'delete' => 'deleted',
            'ban' => 'banned',
            'unban' => 'unbanned',
            'lock' => 'locked',
            'unlock' => 'unlocked',
        };

        return redirect()->route('system.users.index')
            ->with('success', "{$count} user(s) {$actionLabel} successfully.");
    }

    public function destroy(int $id): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('users.delete'), 403);

        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) {
            return redirect()->route('system.users.index')
                ->withErrors(['error' => 'You cannot delete your own account.']);
        }

        $this->userActivity->log('user_deleted', "User deleted: {$user->email} (ID: {$user->id})", userId: $user->id, oldData: $user->only(['firstname', 'lastname', 'email', 'id_number']));
        $user->delete();

        return redirect()->route('system.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
