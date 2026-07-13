<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->hasPermission('settings.access'), 403);
        return view('system.settings', [
            'settings' => [
                'site_logo' => SystemSetting::get('site_logo'),
                'site_favicon' => SystemSetting::get('site_favicon'),
                'site_long_name' => SystemSetting::get('site_long_name', config('app.name')),
                'site_short_name' => SystemSetting::get('site_short_name', 'DT'),
                'site_description' => SystemSetting::get('site_description', 'A secure, military-grade document management system.'),
                'color_primary' => SystemSetting::get('color_primary', '#4f46e5'),
                'color_secondary' => SystemSetting::get('color_secondary', '#7c3aed'),
                'emails' => SystemSetting::get('emails', []),
                'contacts' => SystemSetting::get('contacts', []),
                'addresses' => SystemSetting::get('addresses', []),
                'document_header_title' => SystemSetting::get('document_header_title', config('app.name')),
                'document_right_logo' => SystemSetting::get('document_right_logo'),
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('settings.access'), 403);
        $data = $request->validate([
            'site_long_name' => ['required', 'string', 'max:255'],
            'site_short_name' => ['required', 'string', 'max:50'],
            'site_description' => ['nullable', 'string', 'max:500'],
            'color_primary' => ['required', 'string', 'max:20'],
            'color_secondary' => ['required', 'string', 'max:20'],
            'emails' => ['nullable', 'array'],
            'emails.*' => ['nullable', 'email', 'max:255'],
            'contacts' => ['nullable', 'array'],
            'contacts.*' => ['nullable', 'string', 'max:50'],
            'addresses' => ['nullable', 'array'],
            'addresses.*' => ['nullable', 'string', 'max:500'],
        ]);

        SystemSetting::set('site_long_name', $data['site_long_name']);
        SystemSetting::set('site_short_name', $data['site_short_name']);
        SystemSetting::set('site_description', $data['site_description'] ?? '');
        SystemSetting::set('color_primary', $data['color_primary']);
        SystemSetting::set('color_secondary', $data['color_secondary']);
        SystemSetting::set('emails', array_values(array_filter($data['emails'] ?? [])));
        SystemSetting::set('contacts', array_values(array_filter($data['contacts'] ?? [])));
        SystemSetting::set('addresses', array_values(array_filter($data['addresses'] ?? [])));
        SystemSetting::set('document_header_title', $data['document_header_title'] ?? $data['site_long_name']);

        if ($request->hasFile('site_logo')) {
            $request->validate(['site_logo' => ['image', 'mimes:png,svg,jpg,jpeg', 'max:15360']]);
            $filename = 'logo.' . $request->file('site_logo')->extension();
            $request->file('site_logo')->storeAs('system/logo', $filename, 'local');
            SystemSetting::set('site_logo', $filename);
        }

        if ($request->hasFile('document_right_logo')) {
            $request->validate(['document_right_logo' => ['image', 'mimes:png,svg,jpg,jpeg', 'max:15360']]);
            $filename = 'logo.' . $request->file('document_right_logo')->extension();
            $request->file('document_right_logo')->storeAs('system/document-logo-right', $filename, 'local');
            SystemSetting::set('document_right_logo', $filename);
        }

        if ($request->hasFile('site_favicon')) {
            $request->validate(['site_favicon' => ['image', 'mimes:png,svg,ico,jpg,jpeg', 'max:15360']]);
            $ext = $request->file('site_favicon')->extension();
            $filename = 'favicon.' . $ext;
            $request->file('site_favicon')->storeAs('system/favicon', $filename, 'local');
            SystemSetting::set('site_favicon', $filename);
        }

        return redirect()->route('system.settings')->with('success', 'System settings updated successfully.');
    }
}
