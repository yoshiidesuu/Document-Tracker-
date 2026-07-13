<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ config('app.name', 'Document Tracker') }} - Secure document management system">

    <title>@yield('title', config('app.name', 'Document Tracker'))</title>

    <link rel="icon" type="image/x-icon" href="{{ route('favicon') }}">
    <meta name="chat-user-id" content="{{ auth()->id() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
<style nonce="{{ $cspNonce ?? '' }}">
#mainContent { margin-left: 0; width: 100%; }
@media (min-width: 1024px) { #mainContent { margin-left: 16rem; width: calc(100% - 16rem); } }
</style>
</head>
<body class="font-sans antialiased text-gray-900 bg-gray-100 min-h-screen flex">

    <aside id="sidebar" class="fixed inset-y-0 left-0 z-30 w-64 bg-white border-r border-gray-200 flex flex-col transition-transform duration-200 -translate-x-full lg:translate-x-0 overflow-y-auto">
        <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200">
            <a href="{{ route('system.dashboard') }}" class="flex items-center space-x-2">
                @php $logo = \App\Models\SystemSetting::get('site_logo'); @endphp
                @if ($logo)
                    <img src="{{ route('file.logo') }}" alt="Logo" class="h-8 w-8 object-contain flex-shrink-0" style="-webkit-user-drag: none; user-select: none;">
                @else
                    <svg class="h-7 w-7 text-indigo-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                @endif
                <span class="text-lg font-bold text-gray-900 truncate max-w-[140px]">{{ \App\Models\SystemSetting::get('site_short_name', config('app.name', 'DT')) }}</span>
            </a>
            <button id="closeSidebar" class="lg:hidden p-1 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 focus:outline-none">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <nav class="flex-1 px-3 py-4 space-y-1 overflow-y-auto">

            <a href="{{ route('system.dashboard') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('system.dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg class="h-5 w-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zm0 9.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zm0 9.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                </svg>
                Dashboard
            </a>

            @if(auth()->user()->hasPermission('messages') || auth()->user()->hasPermission('messages.access'))
            <a href="{{ route('system.messages') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('system.messages') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg class="h-5 w-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                </svg>
                Messages
            </a>
            @endif

            <a href="{{ route('system.profile') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('system.profile') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                <svg class="h-5 w-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                </svg>
                My Profile
            </a>

            {{-- User Management --}}
            @php $showUserMgmt = auth()->user()->hasRole('admin') || auth()->user()->hasPermission('users') || auth()->user()->hasPermission('users.list') || auth()->user()->hasPermission('roles') || auth()->user()->hasPermission('roles.list') || auth()->user()->hasPermission('permissions') || auth()->user()->hasPermission('permissions.manage'); @endphp
            @if($showUserMgmt)
            <div class="pt-4 mt-4 border-t border-gray-200">
                <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">User Management</p>
                @if(auth()->user()->hasPermission('users') || auth()->user()->hasPermission('users.list'))
                <a href="{{ route('system.users.index') }}" class="mt-2 flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('system.users.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="h-5 w-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                    </svg>
                    Users
                </a>
                @endif
                @if(auth()->user()->hasPermission('roles') || auth()->user()->hasPermission('roles.list'))
                <a href="{{ route('system.roles.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('system.roles.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="h-5 w-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                    Roles
                </a>
                @endif
                @if(auth()->user()->hasRole('admin') || auth()->user()->hasPermission('permissions') || auth()->user()->hasPermission('permissions.manage'))
                <a href="{{ route('system.permissions.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('system.permissions.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="h-5 w-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Permissions
                </a>
                @endif
            </div>
            @endif

            {{-- Document Management --}}
            @php $showDocMgmt = auth()->user()->hasPermission('documents') || auth()->user()->hasPermission('documents.list') || auth()->user()->hasPermission('documents.my') || auth()->user()->hasPermission('document-types') || auth()->user()->hasPermission('document-types.list'); @endphp
            @if($showDocMgmt)
            <div class="pt-4 mt-4 border-t border-gray-200">
                <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Document Management</p>
                @if(auth()->user()->hasPermission('documents') || auth()->user()->hasPermission('documents.list'))
                <a href="{{ route('system.documents.index') }}" class="mt-2 flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('system.documents.index') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="h-5 w-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                    All Documents
                </a>
                @endif
                @if(auth()->user()->hasPermission('documents.my'))
                <a href="{{ route('system.documents.my') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('system.documents.my') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="h-5 w-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                    My Documents
                </a>
                @endif
                @if(auth()->user()->hasPermission('document-types') || auth()->user()->hasPermission('document-types.list'))
                <a href="{{ route('system.document-types.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('system.document-types.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="h-5 w-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Document Types
                </a>
                @endif
            </div>
            @endif

            {{-- Designations --}}
            @php $showDesignations = auth()->user()->hasPermission('departments') || auth()->user()->hasPermission('departments.list') || auth()->user()->hasPermission('offices') || auth()->user()->hasPermission('offices.list') || auth()->user()->hasPermission('statistics.access'); @endphp
            @if($showDesignations)
            <div class="pt-4 mt-4 border-t border-gray-200">
                <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Designations</p>
                @if(auth()->user()->hasPermission('departments') || auth()->user()->hasPermission('departments.list'))
                <a href="{{ route('system.departments.index') }}" class="mt-2 flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('system.departments.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="h-5 w-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                    </svg>
                    Departments
                </a>
                @endif
                @if(auth()->user()->hasPermission('offices') || auth()->user()->hasPermission('offices.list'))
                <a href="{{ route('system.offices.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('system.offices.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="h-5 w-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605" />
                    </svg>
                    Offices
                </a>
                @endif
                @if(auth()->user()->hasPermission('statistics.access'))
                <a href="{{ route('system.statistics') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('system.statistics') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="h-5 w-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                    Statistics
                </a>
                @endif
            </div>
            @endif

            {{-- Scanning Management --}}
            @php $showScanning = auth()->user()->hasPermission('documents.receive') || auth()->user()->hasPermission('documents.finish') || auth()->user()->hasPermission('documents.terminate'); @endphp
            @if($showScanning)
            <div class="pt-4 mt-4 border-t border-gray-200">
                <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Scanning Management</p>
                @if(auth()->user()->hasPermission('documents.receive'))
                <a href="{{ route('system.documents.receive') }}" class="mt-2 flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('system.documents.receive') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="h-5 w-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Receiving Scanner
                </a>
                @endif
                @if(auth()->user()->hasPermission('documents.finish'))
                <a href="{{ route('system.documents.finish') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('system.documents.finish') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="h-5 w-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Finish Scanner
                </a>
                @endif
                @if(auth()->user()->hasPermission('documents.terminate'))
                <a href="{{ route('system.documents.terminate') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('system.documents.terminate') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="h-5 w-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Terminate Scanner
                </a>
                @endif
            </div>
            @endif

            {{-- Settings Management --}}
            @php $showSettings = auth()->user()->hasPermission('arta') || auth()->user()->hasPermission('arta.list') || auth()->user()->hasPermission('email-settings') || auth()->user()->hasPermission('email-settings.access') || auth()->user()->hasPermission('activity-logs') || auth()->user()->hasPermission('activity-logs.access') || auth()->user()->hasPermission('security-logs') || auth()->user()->hasPermission('security-logs.access') || auth()->user()->hasPermission('settings') || auth()->user()->hasPermission('settings.access'); @endphp
            @if($showSettings)
            <div class="pt-4 mt-4 border-t border-gray-200">
                <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Settings Management</p>
                @if(auth()->user()->hasPermission('arta') || auth()->user()->hasPermission('arta.list'))
                <a href="{{ route('system.arta-settings.index') }}" class="mt-2 flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('system.arta-settings.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="h-5 w-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                    ARTA Settings
                </a>
                @endif
                @if(auth()->user()->hasPermission('email-settings') || auth()->user()->hasPermission('email-settings.access'))
                <a href="{{ route('system.email-settings') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('system.email-settings') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="h-5 w-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                    Email Settings
                </a>
                @endif
                @if(auth()->user()->hasPermission('activity-logs') || auth()->user()->hasPermission('activity-logs.access'))
                <a href="{{ route('system.activity-logs.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('system.activity-logs.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="h-5 w-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Activity Logs
                </a>
                @endif
                @if(auth()->user()->hasPermission('security-logs') || auth()->user()->hasPermission('security-logs.access'))
                <a href="{{ route('system.security-logs.index') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('system.security-logs.*') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="h-5 w-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                    Security Logs
                </a>
                @endif
                @if(auth()->user()->hasPermission('settings') || auth()->user()->hasPermission('settings.access'))
                <a href="{{ route('system.settings') }}" class="flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('system.settings') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="h-5 w-5 mr-3 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Settings
                </a>
                @endif
            </div>
            @endif
        </nav>

        <div class="border-t border-gray-200 p-4">
            <div class="flex items-center space-x-3">
                @php $profilePic = auth()->user()->profile_picture_url; @endphp
                @if ($profilePic)
                    <img src="{{ $profilePic }}" alt="" class="h-8 w-8 rounded-full object-cover flex-shrink-0" style="-webkit-user-drag: none; user-select: none;">
                @else
                    <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-sm font-semibold text-indigo-600 flex-shrink-0">
                        {{ auth()->user()->initials }}
                    </div>
                @endif
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->firstname ?: auth()->user()->name }}</p>
                    @if(auth()->user()->roles->isNotEmpty())
                        <p class="text-xs text-gray-500 truncate">{{ auth()->user()->roles->pluck('name')->implode(', ') }}</p>
                    @endif
                </div>
                <a href="{{ route('system.profile') }}" class="p-1 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 focus:outline-none mr-1" title="My Profile">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="p-1 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 focus:outline-none" title="Sign out">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <div id="sidebarOverlay" class="fixed inset-0 bg-black/30 z-20 hidden lg:hidden"></div>

    <div id="mainContent" class="flex flex-col min-w-0 h-screen overflow-y-auto">
        <header class="bg-white border-b border-gray-200 h-16 flex items-center px-4 lg:px-6 sticky top-0 z-10">
            <button id="openSidebar" class="lg:hidden p-1 mr-3 rounded-md text-gray-400 hover:text-gray-600 hover:bg-gray-100 focus:outline-none">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
            <div class="flex-1 flex items-center justify-between">
                <h1 class="text-lg font-semibold text-gray-900">@yield('page_title', 'System')</h1>
                <div class="flex items-center space-x-3">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                        {{ auth()->user()->roles->first()?->name ?? 'User' }}
                    </span>
                </div>
            </div>
        </header>

        <main class="flex-1 p-4 lg:p-6 overflow-auto">
            @yield('content')
        </main>

        <footer class="bg-white border-t border-gray-200 px-4 lg:px-6 py-3">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-2">
                <div class="flex items-center space-x-2 text-xs text-gray-500">
                    <svg class="h-3.5 w-3.5 text-indigo-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                    <span>&copy; {{ date('Y') }} {{ \App\Models\SystemSetting::get('site_long_name', config('app.name', 'Document Tracker')) }}. All rights reserved.</span>
                </div>
                <div class="flex items-center space-x-4 text-xs">
                    <a href="#" class="text-gray-500 hover:text-gray-700 transition-colors">Privacy</a>
                    <a href="#" class="text-gray-500 hover:text-gray-700 transition-colors">Terms</a>
                    <a href="#" class="text-gray-500 hover:text-gray-700 transition-colors">Contact</a>
                    @if (config('security.dict.data_privacy_officer_email'))
                        <a href="mailto:{{ config('security.dict.data_privacy_officer_email') }}" class="text-gray-500 hover:text-gray-700 transition-colors">DPO</a>
                    @endif
                </div>
            </div>
        </footer>
    </div>

    @stack('scripts')

    <script nonce="{{ $cspNonce ?? '' }}">
        (function() {
            document.addEventListener('dragstart', function(e) {
                if (e.target.tagName === 'IMG') e.preventDefault();
            });
            document.addEventListener('contextmenu', function(e) {
                if (e.target.tagName === 'IMG') e.preventDefault();
            });

            document.addEventListener('submit', function(e) {
                var form = e.target;
                var msg = form.getAttribute('data-confirm');
                if (msg && !confirm(msg)) {
                    e.preventDefault();
                }
            });

            document.addEventListener('click', function(e) {
                var btn = e.target.closest('[data-confirm]');
                if (btn) {
                    var msg = btn.getAttribute('data-confirm');
                    if (msg && !confirm(msg)) {
                        e.preventDefault();
                        return;
                    }
                }

                var copyBtn = e.target.closest('[data-action="copy"]');
                if (copyBtn) {
                    var val = copyBtn.getAttribute('data-value');
                    if (val) {
                        navigator.clipboard.writeText(val).then(function() {
                            var orig = copyBtn.textContent;
                            copyBtn.textContent = 'Copied!';
                            setTimeout(function() { copyBtn.textContent = orig; }, 2000);
                        });
                    }
                }

                var reopenBtn = e.target.closest('[data-action="reopen"]');
                if (reopenBtn) {
                    var id = reopenBtn.getAttribute('data-id');
                    if (id && confirm('Reopen this document?')) {
                        var form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '/system/documents/' + id + '/reopen';
                        form.innerHTML = '<input type="hidden" name="_token" value="' + document.querySelector('meta[name="csrf-token"]').content + '">';
                        document.body.appendChild(form);
                        form.submit();
                    }
                }
            });

            var toggleBtn = document.getElementById('togglePasswordBtn');
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    var input = document.getElementById('smtp_password');
                    var eye = document.getElementById('pwEye');
                    if (input && eye) {
                        if (input.type === 'password') {
                            input.type = 'text';
                            eye.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />';
                        } else {
                            input.type = 'password';
                            eye.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
                        }
                    }
                });
            }
        })();
    </script>
</body>
</html>
