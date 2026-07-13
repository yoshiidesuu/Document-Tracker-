<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Services\UserActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function __construct(
        private readonly UserActivityService $userActivity,
    ) {}

    public function index(): View
    {
        abort_unless(auth()->user()->hasPermission('departments.list'), 403);

        $departments = Department::orderBy('name')->get();

        return view('system.departments.index', compact('departments'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->hasPermission('departments.create'), 403);

        return view('system.departments.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('departments.create'), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50', 'unique:departments', 'regex:/^[a-z0-9-]+$/'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $data['is_active'] = true;

        $department = Department::create($data);

        $this->userActivity->log('department_created', "Department created: {$department->name}", newData: $department->only(['name', 'code', 'description']));

        return redirect()->route('system.departments.index')
            ->with('success', "Department '{$department->name}' created successfully.");
    }

    public function view(Department $department): View
    {
        abort_unless(auth()->user()->hasPermission('departments.view'), 403);

        return view('system.departments.view', compact('department'));
    }

    public function edit(Department $department): View
    {
        abort_unless(auth()->user()->hasPermission('departments.edit'), 403);

        return view('system.departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('departments.edit'), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50', Rule::unique('departments')->ignore($department->id), 'regex:/^[a-z0-9-]+$/'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $old = $department->only(['name', 'code', 'description', 'is_active']);
        $department->update($data);
        $new = $department->only(['name', 'code', 'description', 'is_active']);

        $this->userActivity->log('department_updated', "Department updated: {$department->name}", oldData: $old, newData: $new);

        return redirect()->route('system.departments.view', $department->id)
            ->with('success', "Department '{$department->name}' updated successfully.");
    }

    public function destroy(Department $department): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('departments.delete'), 403);

        $this->userActivity->log('department_deleted', "Department deleted: {$department->name}", oldData: $department->only(['name', 'code', 'description']));
        $department->delete();

        return redirect()->route('system.departments.index')
            ->with('success', "Department '{$department->name}' deleted successfully.");
    }

    public function toggleStatus(Department $department): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('departments.toggle-status'), 403);

        $old = ['is_active' => $department->is_active];
        $department->update(['is_active' => ! $department->is_active]);
        $new = ['is_active' => $department->is_active];

        $status = $department->is_active ? 'activated' : 'deactivated';
        $this->userActivity->log('department_status_toggled', "Department {$status}: {$department->name}", oldData: $old, newData: $new);

        return redirect()->route('system.departments.view', $department->id)
            ->with('success', "Department '{$department->name}' {$status} successfully.");
    }
}
