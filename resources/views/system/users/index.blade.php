@extends('layouts.system')

@section('title', \App\Models\SystemSetting::get('site_long_name', config('app.name', 'Document Tracker')) . ' - Users')

@section('page_title', 'User Management')

@section('content')
<div class="w-full">
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

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <h2 class="text-lg font-semibold text-gray-900">All Users ({{ $users->total() }})</h2>
                    @if(auth()->user()->hasPermission('users.create'))
                    <a href="{{ route('system.users.create') }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                        <svg class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Create User
                    </a>
                    @endif
                </div>
                <form method="GET" action="{{ route('system.users.index') }}" class="flex flex-col sm:flex-row gap-2">
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name or ID..." class="w-full sm:w-64 px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <select name="status" class="w-full sm:w-36 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Status</option>
                        <option value="active" @selected(request('status') === 'active')>Active</option>
                        <option value="locked" @selected(request('status') === 'locked')>Locked</option>
                        <option value="banned" @selected(request('status') === 'banned')>Banned</option>
                    </select>
                    <select name="role" class="w-full sm:w-36 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All Roles</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->slug }}" @selected(request('role') === $role->slug)>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">Filter</button>
                    @if (request()->anyFilled(['search', 'status', 'role']))
                        <a href="{{ route('system.users.index') }}" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors text-center">Clear</a>
                    @endif
                </form>
            </div>
        </div>

        <form method="POST" action="{{ route('system.users.bulk') }}" id="bulk-form">
            @csrf

            <div id="bulk-actions" class="px-6 py-3 border-b border-gray-200 bg-gray-50 flex items-center gap-2" style="display: none;">
                <span class="text-sm text-gray-600"><span id="selected-count">0</span> selected</span>
                <div class="flex items-center gap-2 ml-4">
                    @if(auth()->user()->hasPermission('users.bulk-delete'))
                    <button type="submit" name="action" value="delete" class="px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors" data-confirm="Are you sure you want to delete the selected users? This cannot be undone.">Delete</button>
                    @endif
                    @if(auth()->user()->hasPermission('users.bulk-ban'))
                    <button type="submit" name="action" value="ban" class="px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 transition-colors" data-confirm="Are you sure you want to ban the selected users?">Ban</button>
                    @endif
                    @if(auth()->user()->hasPermission('users.bulk-unban'))
                    <button type="submit" name="action" value="unban" class="px-3 py-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition-colors">Unban</button>
                    @endif
                    @if(auth()->user()->hasPermission('users.bulk-lock'))
                    <button type="submit" name="action" value="lock" class="px-3 py-1.5 text-xs font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-lg hover:bg-amber-100 transition-colors" data-confirm="Are you sure you want to lock the selected users?">Lock</button>
                    @endif
                    @if(auth()->user()->hasPermission('users.bulk-unlock'))
                    <button type="submit" name="action" value="unlock" class="px-3 py-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition-colors">Unlock</button>
                    @endif
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-4 py-3 w-10">
                                <input type="checkbox" id="select-all" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                            </th>
                            <th class="text-left px-6 py-3 font-medium text-gray-500">User</th>
                            <th class="text-left px-6 py-3 font-medium text-gray-500">ID Number</th>
                            <th class="text-left px-6 py-3 font-medium text-gray-500">Roles</th>
                            <th class="text-left px-6 py-3 font-medium text-gray-500">Status</th>
                            <th class="text-left px-6 py-3 font-medium text-gray-500">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'login_count', 'dir' => request('sort') === 'login_count' && request('dir') === 'asc' ? 'desc' : 'asc']) }}" class="hover:text-gray-700">Logins @if(request('sort') === 'login_count'){{ request('dir') === 'asc' ? '↑' : '↓' }}@endif</a>
                            </th>
                            <th class="text-left px-6 py-3 font-medium text-gray-500">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'last_login_at', 'dir' => request('sort') === 'last_login_at' && request('dir') === 'asc' ? 'desc' : 'asc']) }}" class="hover:text-gray-700">Last Login @if(request('sort') === 'last_login_at'){{ request('dir') === 'asc' ? '↑' : '↓' }}@endif</a>
                            </th>
                            <th class="text-left px-6 py-3 font-medium text-gray-500">
                                <a href="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'dir' => request('sort') === 'created_at' && request('dir') === 'asc' ? 'desc' : 'asc']) }}" class="hover:text-gray-700">Joined @if(request('sort') === 'created_at'){{ request('dir') === 'asc' ? '↑' : '↓' }}@endif</a>
                            </th>
                            <th class="text-right px-6 py-3 font-medium text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($users as $user)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-4">
                                    <input type="checkbox" name="selected[]" value="{{ $user->id }}" class="user-checkbox rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        @php $pic = $user->profile_picture_url; @endphp
                                        @if ($pic)
                                            <img src="{{ $pic }}" alt="" class="h-9 w-9 rounded-full object-cover flex-shrink-0"  style="-webkit-user-drag: none; user-select: none;">
                                        @else
                                            <div class="h-9 w-9 rounded-full bg-indigo-100 flex items-center justify-center text-sm font-semibold text-indigo-600 flex-shrink-0">{{ $user->initials }}</div>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="font-medium text-gray-900 truncate">{{ $user->full_name }}</p>
                                            <p class="text-xs text-gray-500 truncate">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $user->id_number ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse ($user->roles as $role)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $role->slug === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-indigo-100 text-indigo-800' }}">{{ $role->name }}</span>
                                        @empty
                                            <span class="text-xs text-gray-400">None</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($user->banned)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Banned</span>
                                    @elseif ($user->locked)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">Locked</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Active</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $user->login_count }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $user->last_login_at?->diffForHumans() ?? 'Never' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $user->created_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <button type="button" class="dropdown-toggle inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors" data-user-id="{{ $user->id }}">
                                        Actions
                                        <svg class="h-3 w-3 ml-1 -mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                                    </button>
                                    <div class="dropdown-menu hidden fixed w-44 bg-white border border-gray-200 rounded-lg shadow-lg z-[9999] py-1" data-user-id="{{ $user->id }}">
                                        @if(auth()->user()->hasPermission('users.view'))
                                        <a href="{{ route('system.users.view', $user->id) }}" class="flex items-center px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 transition-colors">
                                            <svg class="h-3.5 w-3.5 mr-2 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                            View
                                        </a>
                                        @endif
                                        @if(auth()->user()->hasPermission('users.edit'))
                                        <a href="{{ route('system.users.edit', $user->id) }}" class="flex items-center px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 transition-colors">
                                            <svg class="h-3.5 w-3.5 mr-2 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                            Edit
                                        </a>
                                        @endif
                                        <hr class="my-1 border-gray-100">
                                        @if(auth()->user()->hasPermission('users.ban') || auth()->user()->hasPermission('users.unban'))
                                            @if ($user->banned)
                                                @if(auth()->user()->hasPermission('users.unban'))
                                                <form method="POST" action="{{ route('system.users.unban', $user->id) }}">
                                                    @csrf
                                                    <button type="submit" class="flex items-center w-full px-4 py-2 text-xs text-emerald-700 hover:bg-emerald-50 transition-colors">
                                                        <svg class="h-3.5 w-3.5 mr-2 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                        Unban
                                                    </button>
                                                </form>
                                                @endif
                                            @else
                                                @if(auth()->user()->hasPermission('users.ban'))
                                                <form method="POST" action="{{ route('system.users.ban', $user->id) }}" data-confirm="Ban this user?">
                                                    @csrf
                                                    <button type="submit" class="flex items-center w-full px-4 py-2 text-xs text-red-700 hover:bg-red-50 transition-colors">
                                                        <svg class="h-3.5 w-3.5 mr-2 text-red-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                                        Ban
                                                    </button>
                                                </form>
                                                @endif
                                            @endif
                                        @endif
                                        @if(auth()->user()->hasPermission('users.lock') || auth()->user()->hasPermission('users.unlock'))
                                            @if ($user->locked)
                                                @if(auth()->user()->hasPermission('users.unlock'))
                                                <form method="POST" action="{{ route('system.users.unlock', $user->id) }}">
                                                    @csrf
                                                    <button type="submit" class="flex items-center w-full px-4 py-2 text-xs text-emerald-700 hover:bg-emerald-50 transition-colors">
                                                        <svg class="h-3.5 w-3.5 mr-2 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 119 0v3.75M3.75 21.75h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H3.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                                                        Unlock
                                                    </button>
                                                </form>
                                                @endif
                                            @else
                                                @if(auth()->user()->hasPermission('users.lock'))
                                                <form method="POST" action="{{ route('system.users.lock', $user->id) }}" data-confirm="Lock this user?">
                                                    @csrf
                                                    <button type="submit" class="flex items-center w-full px-4 py-2 text-xs text-amber-700 hover:bg-amber-50 transition-colors">
                                                        <svg class="h-3.5 w-3.5 mr-2 text-amber-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg>
                                                        Lock
                                                    </button>
                                                </form>
                                                @endif
                                            @endif
                                        @endif
                                        @if(auth()->user()->hasPermission('users.force-logout'))
                                        <hr class="my-1 border-gray-100">
                                        <form method="POST" action="{{ route('system.users.force-logout', $user->id) }}" data-confirm="Force logout all sessions for this user?">
                                            @csrf
                                            <button type="submit" class="flex items-center w-full px-4 py-2 text-xs text-orange-700 hover:bg-orange-50 transition-colors">
                                                <svg class="h-3.5 w-3.5 mr-2 text-orange-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" /></svg>
                                                Force Logout
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-12 text-center text-sm text-gray-500">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        @if ($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.user-checkbox');
    const bulkActions = document.getElementById('bulk-actions');
    const selectedCount = document.getElementById('selected-count');

    function updateBulkActions() {
        const checked = document.querySelectorAll('.user-checkbox:checked');
        const count = checked.length;
        selectedCount.textContent = count;
        bulkActions.style.display = count === 0 ? 'none' : 'flex';
    }

    selectAll.addEventListener('change', function () {
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateBulkActions();
    });

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function () {
            if (!this.checked) selectAll.checked = false;
            if (document.querySelectorAll('.user-checkbox:checked').length === checkboxes.length) {
                selectAll.checked = true;
            }
            updateBulkActions();
        });
    });

    updateBulkActions();

    document.querySelectorAll('.dropdown-toggle').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            closeAllDropdowns();
            var menu = this.nextElementSibling;
            if (menu && menu.classList.contains('dropdown-menu')) {
                var rect = this.getBoundingClientRect();
                menu.style.left = Math.max(8, rect.left + rect.width - 176) + 'px';
                menu.style.top = (rect.bottom + 4) + 'px';
                menu.classList.remove('hidden');
            }
        });
    });

    document.addEventListener('click', function () {
        closeAllDropdowns();
    });

    document.addEventListener('scroll', function () {
        closeAllDropdowns();
    }, true);

    window.addEventListener('resize', function () {
        closeAllDropdowns();
    });

    function closeAllDropdowns() {
        document.querySelectorAll('.dropdown-menu').forEach(function (m) {
            m.classList.add('hidden');
        });
    }
});
</script>
@endpush
