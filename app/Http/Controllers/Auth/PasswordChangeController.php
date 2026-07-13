<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\PasswordChangeRequest;
use App\Services\SecurityAuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class PasswordChangeController extends Controller
{
    public function __construct(
        private readonly SecurityAuditService $audit
    ) {}

    public function __invoke(PasswordChangeRequest $request): JsonResponse
    {
        $user = auth()->user();

        if (Hash::check($request->input('new_password'), $user->password)) {
            return response()->json([
                'message' => 'Your new password cannot be the same as your current password.',
            ], 422);
        }

        $user->forceFill([
            'password' => Hash::make($request->input('new_password')),
            'password_changed_at' => now(),
        ])->save();

        $this->audit->logPasswordChange($user->id);

        return response()->json([
            'message' => 'Password changed successfully.',
        ]);
    }
}
