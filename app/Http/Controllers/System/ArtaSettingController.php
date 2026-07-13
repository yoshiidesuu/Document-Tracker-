<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\ArtaSetting;
use App\Services\UserActivityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ArtaSettingController extends Controller
{
    public function __construct(
        private readonly UserActivityService $userActivity,
    ) {}

    public function index(): View
    {
        abort_unless(auth()->user()->hasPermission('arta.list'), 403);

        $groups = ArtaSetting::orderBy('category')
            ->orderBy('title')
            ->get()
            ->groupBy('category');

        return view('system.arta-settings.index', compact('groups'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->hasPermission('arta.create'), 403);

        $categories = ArtaSetting::select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('system.arta-settings.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('arta.create'), 403);

        $data = $request->validate([
            'category' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255', Rule::unique('arta_settings')->where('category', $request->category)],
            'days' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'hours' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'minutes' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        $data['is_active'] = true;

        $arta = ArtaSetting::create($data);

        $this->userActivity->log('arta_setting_created', "ARTA setting created: {$arta->category} - {$arta->title}", newData: $arta->only(['category', 'title', 'days', 'hours', 'minutes']));

        return redirect()->route('system.arta-settings.index')
            ->with('success', "ARTA setting '{$arta->title}' created successfully.");
    }

    public function view(ArtaSetting $artaSetting): View
    {
        abort_unless(auth()->user()->hasPermission('arta.view'), 403);

        return view('system.arta-settings.view', compact('artaSetting'));
    }

    public function edit(ArtaSetting $artaSetting): View
    {
        abort_unless(auth()->user()->hasPermission('arta.edit'), 403);

        $categories = ArtaSetting::select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('system.arta-settings.edit', compact('artaSetting', 'categories'));
    }

    public function update(Request $request, ArtaSetting $artaSetting): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('arta.edit'), 403);

        $data = $request->validate([
            'category' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255', Rule::unique('arta_settings')->where('category', $request->category)->ignore($artaSetting->id)],
            'days' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'hours' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'minutes' => ['nullable', 'integer', 'min:0', 'max:9999'],
        ]);

        $old = $artaSetting->only(['category', 'title', 'days', 'hours', 'minutes', 'is_active']);
        $artaSetting->update($data);
        $new = $artaSetting->only(['category', 'title', 'days', 'hours', 'minutes', 'is_active']);

        $this->userActivity->log('arta_setting_updated', "ARTA setting updated: {$artaSetting->category} - {$artaSetting->title}", oldData: $old, newData: $new);

        return redirect()->route('system.arta-settings.view', $artaSetting->id)
            ->with('success', "ARTA setting '{$artaSetting->title}' updated successfully.");
    }

    public function destroy(ArtaSetting $artaSetting): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('arta.delete'), 403);

        $this->userActivity->log('arta_setting_deleted', "ARTA setting deleted: {$artaSetting->category} - {$artaSetting->title}", oldData: $artaSetting->only(['category', 'title', 'days', 'hours', 'minutes']));
        $artaSetting->delete();

        return redirect()->route('system.arta-settings.index')
            ->with('success', "ARTA setting '{$artaSetting->title}' deleted successfully.");
    }

    public function toggleStatus(ArtaSetting $artaSetting): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('arta.toggle-status'), 403);

        $old = ['is_active' => $artaSetting->is_active];
        $artaSetting->update(['is_active' => !$artaSetting->is_active]);
        $new = ['is_active' => $artaSetting->is_active];

        $status = $artaSetting->is_active ? 'activated' : 'deactivated';
        $this->userActivity->log('arta_setting_status_toggled', "ARTA setting {$status}: {$artaSetting->category} - {$artaSetting->title}", oldData: $old, newData: $new);

        return redirect()->route('system.arta-settings.view', $artaSetting->id)
            ->with('success', "ARTA setting '{$artaSetting->title}' {$status} successfully.");
    }
}
