<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\DocumentType;
use App\Services\UserActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DocumentTypeController extends Controller
{
    public function __construct(
        private readonly UserActivityService $userActivity,
    ) {}

    public function index(): View
    {
        abort_unless(auth()->user()->hasPermission('document-types.list'), 403);

        $documentTypes = DocumentType::orderBy('name')->get();

        return view('system.document-types.index', compact('documentTypes'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->hasPermission('document-types.create'), 403);

        return view('system.document-types.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('document-types.create'), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50', 'unique:document_types', 'regex:/^[a-z0-9-]+$/'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $data['is_active'] = true;

        $documentType = DocumentType::create($data);

        $this->userActivity->log('document_type_created', "Document type created: {$documentType->name}", newData: $documentType->only(['name', 'code', 'description']));

        return redirect()->route('system.document-types.index')
            ->with('success', "Document type '{$documentType->name}' created successfully.");
    }

    public function view(DocumentType $documentType): View
    {
        abort_unless(auth()->user()->hasPermission('document-types.view'), 403);

        return view('system.document-types.view', compact('documentType'));
    }

    public function edit(DocumentType $documentType): View
    {
        abort_unless(auth()->user()->hasPermission('document-types.edit'), 403);

        return view('system.document-types.edit', compact('documentType'));
    }

    public function update(Request $request, DocumentType $documentType): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('document-types.edit'), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50', Rule::unique('document_types')->ignore($documentType->id), 'regex:/^[a-z0-9-]+$/'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $old = $documentType->only(['name', 'code', 'description', 'is_active']);
        $documentType->update($data);
        $new = $documentType->only(['name', 'code', 'description', 'is_active']);

        $this->userActivity->log('document_type_updated', "Document type updated: {$documentType->name}", oldData: $old, newData: $new);

        return redirect()->route('system.document-types.view', $documentType->id)
            ->with('success', "Document type '{$documentType->name}' updated successfully.");
    }

    public function destroy(DocumentType $documentType): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('document-types.delete'), 403);

        $this->userActivity->log('document_type_deleted', "Document type deleted: {$documentType->name}", oldData: $documentType->only(['name', 'code', 'description']));
        $documentType->delete();

        return redirect()->route('system.document-types.index')
            ->with('success', "Document type '{$documentType->name}' deleted successfully.");
    }

    public function toggleStatus(DocumentType $documentType): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('document-types.toggle-status'), 403);

        $old = ['is_active' => $documentType->is_active];
        $documentType->update(['is_active' => ! $documentType->is_active]);
        $new = ['is_active' => $documentType->is_active];

        $status = $documentType->is_active ? 'activated' : 'deactivated';
        $this->userActivity->log('document_type_status_toggled', "Document type {$status}: {$documentType->name}", oldData: $old, newData: $new);

        return redirect()->route('system.document-types.view', $documentType->id)
            ->with('success', "Document type '{$documentType->name}' {$status} successfully.");
    }
}
