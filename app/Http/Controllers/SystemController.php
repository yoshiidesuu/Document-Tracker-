<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;

class SystemController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('status', 'active')->count(),
            'inactive_users' => User::where('status', 'inactive')->count(),
            'locked_users' => User::where('locked', true)->count(),
            'banned_users' => User::where('banned', true)->count(),
            'new_today' => User::whereDate('created_at', today())->count(),
            'new_this_week' => User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'logged_in_today' => User::whereDate('last_login_at', today())->count(),
            'logged_in_week' => User::whereBetween('last_login_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'mfa_enabled' => User::where('mfa_enabled', true)->count(),
            'verified' => User::whereNotNull('email_verified_at')->count(),
            'gender_counts' => User::selectRaw("COALESCE(NULLIF(gender, ''), 'unspecified') as label, COUNT(*) as count")
                ->groupBy('label')
                ->pluck('count', 'label'),
            'role_counts' => Role::withCount('users')->get(),
            'department_counts' => Department::withCount('users as users_count')
                ->where('is_active', true)
                ->orderByDesc('users_count')
                ->get(),
            'recent_users' => User::with('roles', 'department')
                ->latest()
                ->take(5)
                ->get(),
        ];

        return view('system.dashboard', compact('stats'));
    }
}
