<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SecurityAuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function __construct(
        private readonly SecurityAuditService $audit,
    ) {}

    public function sendResetLink(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return response()->json([
                'message' => 'If that email exists in our system, a password reset link has been sent.',
            ]);
        }

        $this->audit->log('password_reset_requested', "Password reset requested for: {$request->email}", null, $user->id);

        $token = Str::random(64);

        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => hash('sha256', $token),
            'created_at' => now(),
        ]);

        $mailConfig = [
            'host' => config('mail.mailers.smtp.host'),
            'port' => config('mail.mailers.smtp.port'),
            'username' => config('mail.mailers.smtp.username'),
            'password' => config('mail.mailers.smtp.password'),
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
        ];

        Mail::send('emails.password-reset', [
            'user' => $user,
            'token' => $token,
            'mailConfig' => $mailConfig,
        ], function ($message) use ($user, $mailConfig) {
            $message->to($user->email)
                ->subject('Reset Your Password - '.config('app.name'));
            if ($mailConfig['from_address']) {
                $message->from($mailConfig['from_address'], $mailConfig['from_name'] ?? config('app.name'));
            }
        });

        return response()->json([
            'message' => 'If that email exists in our system, a password reset link has been sent.',
        ]);
    }
}
