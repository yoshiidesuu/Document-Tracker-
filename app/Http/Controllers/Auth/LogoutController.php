<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SecurityAuditService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function __construct(
        private readonly SecurityAuditService $audit
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $userId = auth()->id();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($userId) {
            $this->audit->log('logout', 'User logged out', null, $userId);
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Logged out successfully.']);
        }

        return response()->json(['message' => 'Logged out successfully.']);
    }
}
