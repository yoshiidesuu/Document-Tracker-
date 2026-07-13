<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\SecurityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SecurityLogController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(auth()->user()->hasPermission('security-logs.access'), 403);

        $query = SecurityLog::with('user');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        if ($request->filled('severity')) {
            $query->where('severity', $request->severity);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderByDesc('created_at')->paginate(50);

        $users = User::whereHas('securityLogs')->orderBy('firstname')->get();
        $events = SecurityLog::select('event')->distinct()->orderBy('event')->pluck('event');

        $filters = $request->only(['user_id', 'event', 'severity', 'date_from', 'date_to']);

        return view('system.security-logs.index', compact('logs', 'users', 'events', 'filters'));
    }
}
