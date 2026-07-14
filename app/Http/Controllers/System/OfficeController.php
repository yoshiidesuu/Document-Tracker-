<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\Office;
use App\Services\UserActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OfficeController extends Controller
{
    public function __construct(
        private readonly UserActivityService $userActivity,
    ) {}

    public function index(): View
    {
        abort_unless(auth()->user()->hasPermission('offices.list'), 403);

        $offices = Office::orderBy('name')->get();

        return view('system.offices.index', compact('offices'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->hasPermission('offices.create'), 403);

        return view('system.offices.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('offices.create'), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50', 'unique:offices', 'regex:/^[a-z0-9-]+$/'],
            'description' => ['nullable', 'string', 'max:1000'],
            'department_id' => ['required', 'exists:departments,id'],
        ]);

        $data['is_active'] = true;

        $office = Office::create($data);

        $this->userActivity->log('office_created', "Office created: {$office->name}", newData: $office->only(['name', 'code', 'description', 'department_id']));

        return redirect()->route('system.offices.index')
            ->with('success', "Office '{$office->name}' created successfully.");
    }

    public function view(Office $office): View
    {
        abort_unless(auth()->user()->hasPermission('offices.view'), 403);

        return view('system.offices.view', compact('office'));
    }

    public function edit(Office $office): View
    {
        abort_unless(auth()->user()->hasPermission('offices.edit'), 403);

        return view('system.offices.edit', compact('office'));
    }

    public function update(Request $request, Office $office): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('offices.edit'), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50', Rule::unique('offices')->ignore($office->id), 'regex:/^[a-z0-9-]+$/'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $old = $office->only(['name', 'code', 'description', 'is_active']);
        $office->update($data);
        $new = $office->only(['name', 'code', 'description', 'is_active']);

        $this->userActivity->log('office_updated', "Office updated: {$office->name}", oldData: $old, newData: $new);

        return redirect()->route('system.offices.view', $office->id)
            ->with('success', "Office '{$office->name}' updated successfully.");
    }

    public function destroy(Office $office): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('offices.delete'), 403);

        $this->userActivity->log('office_deleted', "Office deleted: {$office->name}", oldData: $office->only(['name', 'code', 'description']));
        $office->delete();

        return redirect()->route('system.offices.index')
            ->with('success', "Office '{$office->name}' deleted successfully.");
    }

    public function toggleStatus(Office $office): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('offices.toggle-status'), 403);

        $old = ['is_active' => $office->is_active];
        $office->update(['is_active' => ! $office->is_active]);
        $new = ['is_active' => $office->is_active];

        $status = $office->is_active ? 'activated' : 'deactivated';
        $this->userActivity->log('office_status_toggled', "Office {$status}: {$office->name}", oldData: $old, newData: $new);

        return redirect()->route('system.offices.index')
            ->with('success', "Office '{$office->name}' {$status} successfully.");
    }
}
