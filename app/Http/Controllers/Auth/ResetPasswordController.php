<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
use App\Models\User;
use App\Services\SecurityAuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ResetPasswordController extends Controller
{
    public function __construct(
        private readonly SecurityAuditService $audit,
    ) {}

    public function showResetForm(string $token): View|RedirectResponse
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function reset(ResetPasswordRequest $request): RedirectResponse
    {

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record || !hash_equals($record->token, hash('sha256', $request->token))) {
            return redirect()->route('password.reset', $request->token)
                ->withErrors(['email' => 'Invalid or expired password reset token.']);
        }

        if ($record->created_at < now()->subHour()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return redirect()->route('password.reset', $request->token)
                ->withErrors(['email' => 'This password reset link has expired. Please request a new one.']);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return redirect()->route('password.reset', $request->token)
                ->withErrors(['email' => 'We cannot find a user with that email address.']);
        }

        $user->forceFill([
            'password' => Hash::make($request->password),
            'password_changed_at' => now(),
        ])->save();

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        $this->audit->logPasswordChange($user->id);

        return redirect()->route('login.form')
            ->with('success', 'Your password has been reset successfully. Please sign in with your new password.');
    }
}
