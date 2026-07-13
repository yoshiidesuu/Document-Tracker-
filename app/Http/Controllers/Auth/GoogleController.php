<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\EncryptionService;
use App\Services\SecurityAuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(
        EncryptionService $encryption,
        SecurityAuditService $audit,
    ): RedirectResponse {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login.form')->withErrors([
                'credential' => 'Google authentication failed. Please try again.',
            ]);
        }

        if (!$googleUser->email) {
            return redirect()->route('login.form')->withErrors([
                'credential' => 'Google account must have a valid email address.',
            ]);
        }

        $emailHash = $encryption->hashEmail($googleUser->email);
        $user = User::where('email_hash', $emailHash)->first();

        if (!$user) {
            $nameParts = explode(' ', $googleUser->name, 2);
            $firstName = $nameParts[0] ?: $googleUser->name;
            $lastName = $nameParts[1] ?? '';

            $user = User::create([
                'firstname' => $firstName,
                'lastname' => $lastName,
                'name' => $googleUser->name ?? $googleUser->email,
                'email' => $googleUser->email,
                'email_hash' => $emailHash,
                'email_verified_at' => now(),
                'password' => bcrypt(Str::random(40)),
                'password_changed_at' => now(),
                'profile_picture' => $googleUser->avatar,
                'status' => 'active',
                'locked' => false,
                'banned' => false,
                'terms_accepted_at' => now(),
                'privacy_accepted_at' => now(),
            ]);
        } else {
            if ($user->isBanned()) {
                return redirect()->route('login.form')->withErrors([
                    'credential' => 'This account has been permanently suspended.',
                ]);
            }

            if ($user->isAccountLocked()) {
                return redirect()->route('login.form')->withErrors([
                    'credential' => 'This account is temporarily locked. Please try again later.',
                ]);
            }

            if ($googleUser->avatar && !$user->profile_picture) {
                $user->profile_picture = $googleUser->avatar;
            }
        }

        Auth::login($user, true);

        $request = Request::instance();
        $user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
            'login_count' => ($user->login_count ?? 0) + 1,
        ])->save();

        session([
            'security.fingerprint' => sha1($request->ip() . '|' . $request->userAgent()),
            'security.logged_in_at' => now()->timestamp,
        ]);

        $audit->logLoginSuccess($user->id);

        return redirect()->intended('/system/');
    }
}
