@extends('layouts.system')

@section('title', \App\Models\SystemSetting::get('site_long_name', config('app.name', 'Document Tracker')) . ' - Dashboard')

@section('page_title', 'Dashboard')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-500">Total Users</h3>
                <div class="p-2 bg-indigo-50 rounded-lg">
                    <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_users'] }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $stats['new_today'] }} new today</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-500">Active Users</h3>
                <div class="p-2 bg-emerald-50 rounded-lg">
                    <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['active_users'] }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $stats['verified'] }} verified</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-500">Logged In Today</h3>
                <div class="p-2 bg-blue-50 rounded-lg">
                    <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['logged_in_today'] }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $stats['logged_in_week'] }} this week</p>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-5">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-medium text-gray-500">Restricted</h3>
                <div class="p-2 bg-red-50 rounded-lg">
                    <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $stats['locked_users'] + $stats['banned_users'] }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $stats['locked_users'] }} locked, {{ $stats['banned_users'] }} banned</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Role Distribution --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Role Distribution</h2>
            </div>
            <div class="p-5">
                @if($stats['role_counts']->count())
                    <div class="space-y-3">
                        @foreach($stats['role_counts'] as $role)
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700">{{ $role->name }}</span>
                                <div class="flex items-center space-x-3">
                                    <div class="w-32 bg-gray-100 rounded-full h-2">
                                        <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ $stats['total_users'] > 0 ? ($role->users_count / $stats['total_users'] * 100) : 0 }}%"></div>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900 w-8 text-right">{{ $role->users_count }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">No roles assigned yet.</p>
                @endif
            </div>
        </div>

        {{-- Department Distribution --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">By Department</h2>
            </div>
            <div class="p-5">
                @if($stats['department_counts']->count())
                    <div class="space-y-3">
                        @foreach($stats['department_counts'] as $dept)
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700 truncate">{{ $dept->name }}</span>
                                <div class="flex items-center space-x-3 flex-shrink-0">
                                    <div class="w-24 bg-gray-100 rounded-full h-2">
                                        <div class="bg-cyan-500 h-2 rounded-full" style="width: {{ $stats['total_users'] > 0 ? ($dept->users_count / $stats['total_users'] * 100) : 0 }}%"></div>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900 w-8 text-right">{{ $dept->users_count }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">No departments configured.</p>
                @endif
            </div>
        </div>

        {{-- Gender Distribution --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Gender Distribution</h2>
            </div>
            <div class="p-5">
                @if($stats['gender_counts']->count())
                    @php
                        $genderColors = ['male' => 'bg-blue-500', 'female' => 'bg-pink-500', 'unspecified' => 'bg-gray-400'];
                        $genderLabels = ['male' => 'Male', 'female' => 'Female', 'unspecified' => 'Unspecified'];
                    @endphp
                    <div class="space-y-3">
                        @foreach($stats['gender_counts'] as $gender => $count)
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700">{{ $genderLabels[$gender] ?? ucfirst($gender) }}</span>
                                <div class="flex items-center space-x-3">
                                    <div class="w-24 bg-gray-100 rounded-full h-2">
                                        <div class="{{ $genderColors[$gender] ?? 'bg-gray-500' }} h-2 rounded-full" style="width: {{ $stats['total_users'] > 0 ? ($count / $stats['total_users'] * 100) : 0 }}%"></div>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900 w-8 text-right">{{ $count }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">No gender data recorded.</p>
                @endif
            </div>
        </div>

    </div>

    {{-- Recent Registrations & Status Overview --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Recent Registrations --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Recent Registrations</h2>
                <span class="text-xs text-gray-400">Latest 5</span>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($stats['recent_users'] as $user)
                    <div class="px-5 py-3 flex items-center space-x-3">
                        <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-xs font-semibold text-indigo-600 flex-shrink-0">
                            {{ $user->initials }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ $user->full_name }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-xs text-gray-500">{{ $user->roles->first()?->name ?? 'No Role' }}</p>
                            <p class="text-xs text-gray-400">{{ $user->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="px-5 py-8 text-center text-sm text-gray-500">No users registered yet.</div>
                @endforelse
            </div>
        </div>

        {{-- User Status Overview --}}
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">User Status Overview</h2>
            </div>
            <div class="p-5 space-y-4">
                @php
                    $maxStatus = max($stats['active_users'], $stats['inactive_users'], $stats['locked_users'], $stats['banned_users'], 1);
                @endphp
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-gray-700">Active</span>
                        <span class="font-semibold text-gray-900">{{ $stats['active_users'] }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-3">
                        <div class="bg-emerald-500 h-3 rounded-full transition-all" style="width: {{ $stats['active_users'] / $maxStatus * 100 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-gray-700">Inactive</span>
                        <span class="font-semibold text-gray-900">{{ $stats['inactive_users'] }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-3">
                        <div class="bg-gray-400 h-3 rounded-full transition-all" style="width: {{ $stats['inactive_users'] / $maxStatus * 100 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-gray-700">Locked</span>
                        <span class="font-semibold text-gray-900">{{ $stats['locked_users'] }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-3">
                        <div class="bg-amber-500 h-3 rounded-full transition-all" style="width: {{ $stats['locked_users'] / $maxStatus * 100 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-gray-700">Banned</span>
                        <span class="font-semibold text-gray-900">{{ $stats['banned_users'] }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-3">
                        <div class="bg-red-500 h-3 rounded-full transition-all" style="width: {{ $stats['banned_users'] / $maxStatus * 100 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-gray-700">MFA Enabled</span>
                        <span class="font-semibold text-gray-900">{{ $stats['mfa_enabled'] }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-3">
                        <div class="bg-purple-500 h-3 rounded-full transition-all" style="width: {{ $stats['mfa_enabled'] / $maxStatus * 100 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-gray-700">Email Verified</span>
                        <span class="font-semibold text-gray-900">{{ $stats['verified'] }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-3">
                        <div class="bg-blue-500 h-3 rounded-full transition-all" style="width: {{ $stats['verified'] / $maxStatus * 100 }}%"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- System Info --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-medium text-gray-500">Your Account</h3>
                <p class="text-lg font-semibold text-gray-900 mt-1">{{ auth()->user()->full_name }}</p>
                <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">{{ auth()->user()->roles->first()?->name ?? 'No Role' }}</p>
                <p class="text-xs text-gray-400">Member since {{ auth()->user()->created_at->format('M d, Y') }}</p>
                @if(auth()->user()->department)
                    <p class="text-xs text-gray-400">{{ auth()->user()->department->name }}</p>
                @endif
            </div>
        </div>
    </div>

</div>
@endsection
