<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Mail\TestMail;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class EmailSettingController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->hasPermission('email-settings') || auth()->user()->hasPermission('email-settings.access'), 403);

        return view('system.email-settings', [
            'smtp_host' => SystemSetting::get('smtp_host', 'smtp.gmail.com'),
            'smtp_port' => SystemSetting::get('smtp_port', '587'),
            'smtp_encryption' => SystemSetting::get('smtp_encryption', 'tls'),
            'smtp_username' => SystemSetting::get('smtp_username', ''),
            'smtp_password' => SystemSetting::get('smtp_password', ''),
            'mail_from_address' => SystemSetting::get('mail_from_address', ''),
            'mail_from_name' => SystemSetting::get('mail_from_name', config('app.name')),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('email-settings') || auth()->user()->hasPermission('email-settings.access'), 403);

        $data = $request->validate([
            'smtp_host' => ['required', 'string', 'max:255'],
            'smtp_port' => ['required', 'integer', 'min:1', 'max:65535'],
            'smtp_encryption' => ['required', 'string', 'in:tls,ssl,none'],
            'smtp_username' => ['required', 'string', 'max:255'],
            'smtp_password' => ['required', 'string', 'max:255'],
            'mail_from_address' => ['required', 'email', 'max:255'],
            'mail_from_name' => ['required', 'string', 'max:255'],
        ]);

        SystemSetting::set('smtp_host', $data['smtp_host']);
        SystemSetting::set('smtp_port', (string) $data['smtp_port']);
        SystemSetting::set('smtp_encryption', $data['smtp_encryption']);
        SystemSetting::set('smtp_username', $data['smtp_username']);
        SystemSetting::set('smtp_password', $data['smtp_password']);
        SystemSetting::set('mail_from_address', $data['mail_from_address']);
        SystemSetting::set('mail_from_name', $data['mail_from_name']);

        $this->overrideMailConfig();

        return redirect()->route('system.email-settings')
            ->with('success', 'Email settings saved successfully.');
    }

    public function test(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('email-settings') || auth()->user()->hasPermission('email-settings.access'), 403);

        $request->validate([
            'test_email' => ['required', 'email', 'max:255'],
        ]);

        $this->overrideMailConfig();

        try {
            Mail::to($request->test_email)->send(new TestMail);

            return redirect()->route('system.email-settings')
                ->with('success', "Test email sent successfully to {$request->test_email}.");
        } catch (\Throwable $e) {
            return redirect()->route('system.email-settings')
                ->with('error', 'Failed to send test email: '.$e->getMessage());
        }
    }

    private function overrideMailConfig(): void
    {
        $encryption = SystemSetting::get('smtp_encryption', 'tls');
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => SystemSetting::get('smtp_host', 'smtp.gmail.com'),
            'mail.mailers.smtp.port' => (int) SystemSetting::get('smtp_port', '587'),
            'mail.mailers.smtp.encryption' => $encryption === 'none' ? null : $encryption,
            'mail.mailers.smtp.username' => SystemSetting::get('smtp_username', ''),
            'mail.mailers.smtp.password' => SystemSetting::get('smtp_password', ''),
            'mail.from.address' => SystemSetting::get('mail_from_address', ''),
            'mail.from.name' => SystemSetting::get('mail_from_name', config('app.name')),
        ]);
    }
}
