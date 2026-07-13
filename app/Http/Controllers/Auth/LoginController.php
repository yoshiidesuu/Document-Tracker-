<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Services\SecurityAuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function __construct(
        private readonly SecurityAuditService $audit
    ) {}

    public function __invoke(LoginRequest $request): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $credential = $request->input('credential');
        $field = filter_var($credential, FILTER_VALIDATE_EMAIL) ? 'email' : 'id_number';

        $user = User::where($field, $credential)->first();

        if ($user && $user->isBanned()) {
            $this->audit->logLoginFailed($credential, 'Account is banned');
            throw ValidationException::withMessages([
                'credential' => ['This account has been permanently suspended.'],
            ]);
        }

        if ($user && $user->isAccountLocked()) {
            $this->audit->logLoginFailed($credential, 'Account is locked');
            throw ValidationException::withMessages([
                'credential' => ['This account is temporarily locked. Please try again later.'],
            ]);
        }

        if (!Auth::attempt([
            $field => $credential,
            'password' => $request->input('password'),
        ], $request->boolean('remember'))) {
            if ($user) {
                $user->incrementLoginAttempts();
            }
            $this->audit->logLoginFailed($credential);
            throw ValidationException::withMessages([
                'credential' => ['The provided credentials do not match our records.'],
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();
        $user->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
            'login_count' => ($user->login_count ?? 0) + 1,
        ])->save();

        $user->clearLoginAttempts();

        session([
            'security.fingerprint' => sha1($request->ip() . '|' . $request->userAgent()),
            'security.logged_in_at' => now()->timestamp,
        ]);

        $this->audit->logLoginSuccess($user->id);

        if ($request->expectsJson()) {
            $user->append('profile_picture_url');
            $response = ['user' => $user, 'message' => 'Login successful.'];
            if ($user->isPasswordExpired()) {
                $response['warning'] = 'Your password has expired. Please change it.';
            }
            return response()->json($response);
        }

        return redirect()->intended('/system/');
    }
}
