@extends('layouts.app')

@section('title', config('app.name', 'Document Tracker') . ' - Dashboard')

@section('content')
<div class="min-h-screen w-full pt-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
            <div class="flex items-center space-x-4 mb-6">
                <div class="p-3 bg-indigo-100 rounded-xl">
                    <svg class="h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Welcome, {{ auth()->user()->firstname ?: auth()->user()->name }}!</h1>
                    <p class="text-sm text-gray-500">You are signed in as <strong>{{ auth()->user()->email }}</strong></p>
                </div>
            </div>

            @if (auth()->user()->isPasswordExpired())
                <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                    <div class="flex items-start space-x-3">
                        <svg class="h-5 w-5 text-amber-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                        <div class="text-sm text-amber-700">
                            <p class="font-medium">Your password has expired.</p>
                            <p>Please change it to maintain account security.</p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="p-5 bg-gradient-to-br from-indigo-50 to-indigo-100 rounded-xl border border-indigo-200">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-indigo-900">Account Status</h3>
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full {{ auth()->user()->isActive() ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ ucfirst(auth()->user()->status) }}
                        </span>
                    </div>
                    <p class="text-2xl font-bold text-indigo-900">{{ auth()->user()->login_count ?? 0 }}</p>
                    <p class="text-xs text-indigo-600">Total logins</p>
                </div>

                <div class="p-5 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl border border-purple-200">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-purple-900">Member Since</h3>
                    </div>
                    <p class="text-lg font-bold text-purple-900">{{ auth()->user()->created_at ? auth()->user()->created_at->format('M d, Y') : 'N/A' }}</p>
                    <p class="text-xs text-purple-600">Registration date</p>
                </div>

                <div class="p-5 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl border border-emerald-200">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="text-sm font-medium text-emerald-900">Last Login</h3>
                    </div>
                    <p class="text-lg font-bold text-emerald-900">{{ auth()->user()->last_login_at ? auth()->user()->last_login_at->diffForHumans() : 'First login' }}</p>
                    <p class="text-xs text-emerald-600">From {{ auth()->user()->last_login_ip ?? 'unknown IP' }}</p>
                </div>
            </div>

            <div class="border-t border-gray-200 pt-6">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div class="text-sm text-gray-500">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mr-2">
                            Protected
                        </span>
                        IPS: {{ request()->ip() }} &middot; Session: {{ session('security.fingerprint') ? 'Verified' : 'New' }}
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                            </svg>
                            Sign out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
