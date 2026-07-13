@extends('layouts.system')

@section('title', \App\Models\SystemSetting::get('site_long_name', config('app.name', 'Document Tracker')) . ' - User Details')

@section('page_title', 'User Details')

@section('content')
<div class="max-w-5xl mx-auto">
    @if (session('success'))
        <div class="mb-6 rounded-lg bg-emerald-50 border border-emerald-200 p-4">
            <div class="flex items-start space-x-3">
                <svg class="h-5 w-5 text-emerald-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm text-emerald-700">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4">
            <div class="flex items-start space-x-3">
                <svg class="h-5 w-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
                <div class="text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('system.users.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 transition-colors">
            <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Back to Users
        </a>
        @if(auth()->user()->hasPermission('users.edit'))
        <a href="{{ route('system.users.edit', $user->id) }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
            <svg class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
            Edit User
        </a>
        @endif
    </div>

    <div class="space-y-6">

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Profile Information</h2>
            </div>
            <div class="px-6 py-5">
                <div class="flex items-center space-x-5 mb-6">
                    @php $pic = $user->profile_picture_url; @endphp
                    @if ($pic)
                        <img src="{{ $pic }}" alt="" class="h-16 w-16 rounded-full object-cover"  style="-webkit-user-drag: none; user-select: none;">
                    @else
                        <div class="h-16 w-16 rounded-full bg-indigo-100 flex items-center justify-center text-lg font-semibold text-indigo-600">{{ $user->initials }}</div>
                    @endif
                    <div>
                        <p class="text-lg font-semibold text-gray-900">{{ $user->full_name }}</p>
                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div><span class="text-gray-500">First Name:</span><br><span class="font-medium">{{ $user->firstname ?? '—' }}</span></div>
                    <div><span class="text-gray-500">Middle Name:</span><br><span class="font-medium">{{ $user->middlename ?? '—' }}</span></div>
                    <div><span class="text-gray-500">Last Name:</span><br><span class="font-medium">{{ $user->lastname ?? '—' }}</span></div>
                    <div><span class="text-gray-500">Department:</span><br><span class="font-medium">{{ $user->department?->name ?? '—' }}</span></div>
                    <div><span class="text-gray-500">Office:</span><br><span class="font-medium">{{ $user->office?->name ?? '—' }}</span></div>
                    <div><span class="text-gray-500">ID Number:</span><br><span class="font-medium">{{ $user->id_number ?? '—' }}</span></div>
                    <div><span class="text-gray-500">Age:</span><br><span class="font-medium">{{ $user->age ?? '—' }}</span></div>
                    <div><span class="text-gray-500">Gender:</span><br><span class="font-medium">{{ $user->gender ? ucfirst(str_replace('-', ' ', $user->gender)) : '—' }}</span></div>
                    <div><span class="text-gray-500">Birthday:</span><br><span class="font-medium">{{ $user->bday?->format('M d, Y') ?? '—' }}</span></div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Roles</h2>
            </div>
            <div class="px-6 py-5">
                <div class="flex flex-wrap gap-2">
                    @forelse ($user->roles as $role)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $role->slug === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-indigo-100 text-indigo-800' }}">{{ $role->name }}</span>
                    @empty
                        <span class="text-sm text-gray-400">No roles assigned.</span>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Security & Account</h2>
                </div>
                <div class="px-6 py-5 space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Status</span><span class="font-medium">{{ ucfirst($user->status) }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Locked</span>
                        <span class="font-medium {{ $user->locked ? 'text-amber-600' : 'text-emerald-600' }}">{{ $user->locked ? 'Yes' : 'No' }}</span>
                    </div>
                    <div class="flex justify-between"><span class="text-gray-500">Banned</span>
                        <span class="font-medium {{ $user->banned ? 'text-red-600' : 'text-emerald-600' }}">{{ $user->banned ? 'Yes' : 'No' }}</span>
                    </div>
                    <div class="flex justify-between"><span class="text-gray-500">Email Verified</span><span class="font-medium">{{ $user->email_verified_at ? 'Yes' : 'No' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">MFA Enabled</span><span class="font-medium">{{ $user->mfa_enabled ? 'Yes' : 'No' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Password Expired</span>
                        <span class="font-medium {{ $user->isPasswordExpired() ? 'text-red-600' : 'text-emerald-600' }}">{{ $user->isPasswordExpired() ? 'Yes' : 'No' }}</span>
                    </div>
                    @if(!$user->isPasswordExpired() && $user->passwordExpiresInDays() !== null)
                        <div class="flex justify-between"><span class="text-gray-500">Password Expires In</span><span class="font-medium text-amber-600">{{ $user->passwordExpiresInDays() }} days</span></div>
                    @endif
                    <div class="flex justify-between"><span class="text-gray-500">Password Last Changed</span><span class="font-medium">{{ $user->password_changed_at?->format('M d, Y') ?? 'Never' }}</span></div>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Activity</h2>
                </div>
                <div class="px-6 py-5 space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Login Count</span><span class="font-medium">{{ $user->login_count }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Last Login</span><span class="font-medium">{{ $user->last_login_at?->format('M d, Y h:i A') ?? 'Never' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Last IP</span><span class="font-medium">{{ $user->last_login_ip ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Registration IP</span><span class="font-medium">{{ $user->ip ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Member Since</span><span class="font-medium">{{ $user->created_at->format('M d, Y') }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Terms Accepted</span><span class="font-medium">{{ $user->terms_accepted_at?->format('M d, Y') ?? '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Privacy Accepted</span><span class="font-medium">{{ $user->privacy_accepted_at?->format('M d, Y') ?? '—' }}</span></div>
                </div>
            </div>

            @if ($user->geolocation)
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Registration Geolocation</h2>
                    </div>
                    <div class="px-6 py-5 space-y-3 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Country</span><span class="font-medium">{{ $user->geolocation['country'] ?? '—' }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Region</span><span class="font-medium">{{ $user->geolocation['region'] ?? '—' }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">City</span><span class="font-medium">{{ $user->geolocation['city'] ?? '—' }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">ISP</span><span class="font-medium">{{ $user->geolocation['isp'] ?? '—' }}</span></div>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden md:col-span-2">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Actions</h2>
                </div>
                <div class="px-6 py-5">
                    <div class="flex flex-wrap gap-3">
                        @if(auth()->user()->hasPermission('users.reset-password'))
                        <form method="POST" action="{{ route('system.users.password', $user->id) }}" class="inline-flex items-center space-x-2">
                            @csrf
                            <input type="password" name="new_password" placeholder="New password" class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <button type="submit" class="px-3 py-1.5 text-xs font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-lg hover:bg-amber-100 transition-colors">Reset Password</button>
                        </form>
                        @endif

                        @if(auth()->user()->hasPermission('users.ban') || auth()->user()->hasPermission('users.unban'))
                            @if ($user->banned)
                                @if(auth()->user()->hasPermission('users.unban'))
                                <form method="POST" action="{{ route('system.users.unban', $user->id) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition-colors">Unban</button>
                                </form>
                                @endif
                            @else
                                @if(auth()->user()->hasPermission('users.ban'))
                                <form method="POST" action="{{ route('system.users.ban', $user->id) }}" data-confirm="Are you sure you want to ban this user?">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">Ban</button>
                                </form>
                                @endif
                            @endif
                        @endif

                        @if(auth()->user()->hasPermission('users.lock') || auth()->user()->hasPermission('users.unlock'))
                            @if ($user->locked)
                                @if(auth()->user()->hasPermission('users.unlock'))
                                <form method="POST" action="{{ route('system.users.unlock', $user->id) }}">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition-colors">Unlock</button>
                                </form>
                                @endif
                            @else
                                @if(auth()->user()->hasPermission('users.lock'))
                                <form method="POST" action="{{ route('system.users.lock', $user->id) }}" data-confirm="Are you sure you want to lock this user?">
                                    @csrf
                                    <button type="submit" class="px-3 py-1.5 text-xs font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-lg hover:bg-amber-100 transition-colors">Lock</button>
                                </form>
                                @endif
                            @endif
                        @endif

                        @if(auth()->user()->hasPermission('users.delete'))
                        <form method="POST" action="{{ route('system.users.destroy', $user->id) }}" data-confirm="Are you sure you want to permanently delete this user? This cannot be undone.">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors">Delete</button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
