<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(auth()->user()->hasPermission('activity-logs.access'), 403);

        $query = UserActivity::with('user');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('activity')) {
            $query->where('activity', $request->activity);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderByDesc('created_at')->paginate(50);

        $users = User::whereHas('userActivities')->orderBy('firstname')->get();
        $activities = UserActivity::select('activity')->distinct()->orderBy('activity')->pluck('activity');

        $filters = $request->only(['user_id', 'activity', 'date_from', 'date_to']);

        return view('system.activity-logs.index', compact('logs', 'users', 'activities', 'filters'));
    }
}
