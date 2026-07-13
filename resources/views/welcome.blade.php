<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ config('app.name', 'Document Tracker') }} - Secure document management system">
    <title>{{ \App\Models\SystemSetting::get('site_long_name', config('app.name', 'Document Tracker')) }} - @yield('subtitle', 'Sign In')</title>
    <link rel="icon" type="image/x-icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%234f46e5'%3E%3Cpath d='M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'/%3E%3C/svg%3E">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="font-sans antialiased text-gray-900 min-h-screen flex">

@php
    $siteLongName = \App\Models\SystemSetting::get('site_long_name', config('app.name', 'Document Tracker'));
    $logoDataUrl = \App\Models\SystemSetting::getFileDataUrl('site_logo', 'system/logo');
    $siteDescription = \App\Models\SystemSetting::get('site_description', 'A secure, military-grade document management system.');
    $isRegisterPage = !empty($showRegisterMessage);
@endphp

<div class="min-h-screen w-full flex flex-col lg:flex-row">
    <div class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-indigo-900 to-violet-900 w-full lg:w-3/5 xl:w-3/5 min-h-[320px] lg:min-h-screen flex-shrink-0">
        <div class="absolute -top-32 -right-32 w-[32rem] h-[32rem] bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse"></div>
        <div class="absolute -bottom-40 -left-40 w-[36rem] h-[36rem] bg-violet-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-pulse" style="animation-delay: 2s"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-sky-500 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-pulse" style="animation-delay: 4s"></div>

        <div class="absolute inset-0 opacity-[0.03]">
            <svg class="w-full h-full" viewBox="0 0 800 800" xmlns="http://www.w3.org/2000/svg">
                <defs>
                    <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                        <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="1"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid)" />
            </svg>
        </div>

        <div class="absolute inset-0 bg-gradient-to-t from-slate-900/40 via-transparent to-transparent"></div>

        <div class="relative z-10 flex flex-col justify-center px-6 sm:px-10 lg:px-16 xl:px-20 py-8 lg:py-0 min-h-[320px] lg:min-h-screen">
            <div class="max-w-lg mx-auto lg:mx-0 w-full">
                <div class="flex items-center space-x-3 mb-6 sm:mb-8 lg:mb-10">
                    @php $logo = \App\Models\SystemSetting::get('site_logo'); @endphp
                    @if ($logo && $logoDataUrl)
                        <div class="p-2.5 sm:p-3 bg-white/10 backdrop-blur-md rounded-xl sm:rounded-2xl ring-1 ring-white/20">
                            <img src="{{ $logoDataUrl }}" alt="Logo" class="h-7 w-7 sm:h-10 sm:w-10 object-contain"  style="-webkit-user-drag: none; user-select: none;">
                        </div>
                    @else
                        <div class="p-2.5 sm:p-3 bg-white/10 backdrop-blur-md rounded-xl sm:rounded-2xl ring-1 ring-white/20">
                            <svg class="h-7 w-7 sm:h-10 sm:w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                        </div>
                    @endif
                    <span class="text-xl sm:text-2xl lg:text-3xl font-bold text-white tracking-tight">{{ $siteLongName }}</span>
                </div>

                <h1 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-bold text-white leading-tight mb-3 sm:mb-4">
                    <span class="hidden sm:inline">Welcome to </span>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-300 via-purple-300 to-pink-300">{{ $siteLongName }}</span>
                </h1>

                <p class="text-sm sm:text-base lg:text-lg xl:text-xl text-indigo-200/80 leading-relaxed mb-6 sm:mb-8 lg:mb-10 max-w-xl">
                    <span class="hidden sm:inline">{{ $siteDescription }}</span>
                    <span class="sm:hidden">Secure document management with enterprise-level security.</span>
                    Track, manage, and protect your documents.
                </p>

                <div class="space-y-3 sm:space-y-4 mb-8 lg:mb-12 hidden sm:block">
                    <div class="p-4 bg-white/5 backdrop-blur-sm rounded-xl border border-white/10 hover:bg-white/10 transition-all duration-300">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 p-1.5 bg-emerald-500/20 rounded-lg ring-1 ring-emerald-500/30">
                                <svg class="h-4 w-4 sm:h-5 sm:w-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-white font-semibold text-sm sm:text-base">End-to-End Encryption</h3>
                                <p class="text-indigo-300/70 text-xs sm:text-sm">AES-256-CBC encryption for all sensitive data at rest and in transit</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 bg-white/5 backdrop-blur-sm rounded-xl border border-white/10 hover:bg-white/10 transition-all duration-300">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 p-1.5 bg-emerald-500/20 rounded-lg ring-1 ring-emerald-500/30">
                                <svg class="h-4 w-4 sm:h-5 sm:w-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-white font-semibold text-sm sm:text-base">OWASP & ISO Compliant</h3>
                                <p class="text-indigo-300/70 text-xs sm:text-sm">Protected against all OWASP Top 10 risks with ISO 27001 & DICT compliance</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-4 bg-white/5 backdrop-blur-sm rounded-xl border border-white/10 hover:bg-white/10 transition-all duration-300">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 p-1.5 bg-emerald-500/20 rounded-lg ring-1 ring-emerald-500/30">
                                <svg class="h-4 w-4 sm:h-5 sm:w-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-white font-semibold text-sm sm:text-base">Full Audit Trail</h3>
                                <p class="text-indigo-300/70 text-xs sm:text-sm">Complete logging of all actions with 365-day retention for compliance</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-white/10 pt-4 lg:pt-6 hidden sm:block">
                    <p class="text-indigo-300/60 text-xs sm:text-sm">
                        Powered by <span class="text-white/90 font-medium">{{ $siteLongName }}</span>
                        &mdash; Secure | Reliable | Compliant
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="w-full lg:w-2/5 xl:w-2/5 flex items-center justify-center px-6 sm:px-8 lg:px-12 py-8 lg:py-0 bg-[#f8fafc] flex-1">
        <div class="w-full max-w-sm sm:max-w-md">
            @if ($isRegisterPage)
                <div class="lg:hidden mb-6 sm:mb-8 text-center">
                    @if ($logo && $logoDataUrl)
                        <img src="{{ $logoDataUrl }}" alt="Logo" class="h-8 w-8 sm:h-10 sm:w-10 object-contain">
                    @else
                        <svg class="h-8 w-8 sm:h-10 sm:w-10 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    @endif
                    <span class="text-xl sm:text-2xl font-bold text-gray-900">{{ $siteLongName }}</span>
                </div>

                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-amber-100 mb-4">
                        <svg class="h-8 w-8 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Registration Unavailable</h2>
                    <p class="text-sm text-gray-500 mb-6 max-w-xs mx-auto">Please contact your administrator to create an account for you.</p>
                    <a href="{{ route('login.form') }}" class="inline-flex items-center px-4 py-2.5 text-sm font-medium text-indigo-600 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition-colors">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Sign In
                    </a>
                </div>
            @else
                <div class="lg:hidden mb-6 sm:mb-8 text-center">
                    @if ($logo && $logoDataUrl)
                        <img src="{{ $logoDataUrl }}" alt="Logo" class="h-8 w-8 sm:h-10 sm:w-10 object-contain">
                    @else
                        <svg class="h-8 w-8 sm:h-10 sm:w-10 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    @endif
                    <span class="text-xl sm:text-2xl font-bold text-gray-900">{{ $siteLongName }}</span>
                </div>

                <div class="hidden lg:block mb-8">
                    <h2 class="text-2xl font-bold text-gray-900">Welcome back</h2>
                    <p class="mt-1.5 text-sm text-gray-500">Sign in to your account to continue</p>
                </div>

                <div id="savedAccountsContainer" class="hidden mb-6"></div>

                <form method="POST" action="{{ route('login') }}" class="space-y-5" id="loginForm" data-check-url="{{ route('check-credential') }}">
                    @csrf

                    <div id="errorContainer">
                        @if ($errors->any())
                            <div class="rounded-lg bg-red-50 border border-red-200 p-4">
                                <div class="flex items-start">
                                    <svg class="h-5 w-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                    </svg>
                                    <div class="ml-3 text-sm text-red-700">
                                        @foreach ($errors->all() as $error)
                                            <p>{{ $error }}</p>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div>
                        <label for="credential" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Email or ID Number
                        </label>
                        <div class="relative">
                            <input
                                id="credential"
                                name="credential"
                                type="text"
                                autocomplete="username"
                                required
                                value="{{ old('credential') }}"
                                class="block w-full px-4 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm bg-white shadow-sm pr-10"
                                placeholder="you@example.com or ID-0001"
                                autofocus
                            >
                            <span id="credentialStatus" class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none"></span>
                        </div>
                        <p id="credentialHelp" class="mt-1.5 text-xs text-gray-500 hidden"></p>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Password
                        </label>
                        <div class="relative">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                autocomplete="current-password"
                                required
                                class="block w-full px-4 py-3 border border-gray-300 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm bg-white shadow-sm pr-10"
                                placeholder="Enter your password"
                            >
                            <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 focus:outline-none" tabindex="-1">
                                <svg id="eyeIcon" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        <div id="capsLockWarning" class="mt-1.5 hidden">
                            <div class="flex items-center space-x-1.5 text-xs text-amber-600">
                                <svg class="h-3.5 w-3.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                                <span>Caps Lock is on</span>
                            </div>
                        </div>
                        <div id="passwordStrength" class="mt-3 p-3 bg-gray-50 rounded-lg border border-gray-200 hidden">
                            <p class="text-xs font-medium text-gray-500 mb-2">Password must contain:</p>
                            <div class="space-y-1.5">
                                <div id="pwLength" class="flex items-center space-x-2 text-sm text-gray-400">
                                    <span class="w-4 text-center font-bold">✕</span>
                                    <span>At least 6 characters</span>
                                </div>
                                <div id="pwUpper" class="flex items-center space-x-2 text-sm text-gray-400">
                                    <span class="w-4 text-center font-bold">✕</span>
                                    <span>Uppercase letter (ABC)</span>
                                </div>
                                <div id="pwLower" class="flex items-center space-x-2 text-sm text-gray-400">
                                    <span class="w-4 text-center font-bold">✕</span>
                                    <span>Lowercase letter (abc)</span>
                                </div>
                                <div id="pwNumber" class="flex items-center space-x-2 text-sm text-gray-400">
                                    <span class="w-4 text-center font-bold">✕</span>
                                    <span>Number (123)</span>
                                </div>
                                <div id="pwSpecial" class="flex items-center space-x-2 text-sm text-gray-400">
                                    <span class="w-4 text-center font-bold">✕</span>
                                    <span>Special character (#?!@)</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center space-x-2.5 cursor-pointer group">
                            <input
                                type="checkbox"
                                name="remember"
                                value="1"
                                id="rememberMe"
                                class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 cursor-pointer transition-shadow"
                            >
                            <span class="text-sm text-gray-600 group-hover:text-gray-800 transition-colors">Remember me</span>
                        </label>
                        <a href="#" id="forgotPasswordLink" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 transition-colors">Forgot password?</a>
                    </div>

                    @if (config('services.google.client_id'))
                        <div class="relative flex items-center py-1">
                            <div class="flex-grow border-t border-gray-200"></div>
                            <span class="flex-shrink mx-4 text-xs text-gray-400 uppercase tracking-wider font-medium">or continue with</span>
                            <div class="flex-grow border-t border-gray-200"></div>
                        </div>

                        <a href="{{ route('auth.google') }}"
                           class="w-full flex items-center justify-center px-4 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all shadow-sm">
                            <svg class="h-5 w-5 mr-2.5 flex-shrink-0" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/>
                                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                            </svg>
                            Sign in with Google
                        </a>
                    @endif

                    <button
                        type="submit"
                        id="loginBtn"
                        class="w-full flex items-center justify-center px-4 py-3 text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-indigo-500 rounded-xl hover:from-indigo-700 hover:to-indigo-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all shadow-lg shadow-indigo-500/25 hover:shadow-xl hover:shadow-indigo-500/30 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <svg class="h-4 w-4 sm:h-5 sm:w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                        </svg>
                        Sign in
                    </button>

                    @if (Route::has('register'))
                        <p class="text-center text-sm text-gray-500">
                            Don't have an account?
                            <a href="{{ route('register.form') }}" class="font-semibold text-indigo-600 hover:text-indigo-500 transition-colors">
                                Create one here
                            </a>
                        </p>
                    @endif
                </form>

                <div id="forgotPasswordModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
                    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                            <h3 class="text-base font-semibold text-gray-900">Reset Password</h3>
                            <button type="button" id="closeForgotModal" class="text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="px-6 py-5">
                            <p class="text-sm text-gray-500 mb-5">Enter your email address and we'll send you a link to reset your password.</p>
                            <div id="forgotError" class="hidden mb-4 rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-700"></div>
                            <div id="forgotSuccess" class="hidden mb-4 rounded-lg bg-emerald-50 border border-emerald-200 p-3 text-sm text-emerald-700"></div>
                            <form id="forgotPasswordForm">
                                @csrf
                                <div class="mb-4">
                                    <label for="forgotEmail" class="block text-sm font-medium text-gray-700 mb-1.5">Email Address</label>
                                    <input type="email" id="forgotEmail" required
                                        class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm bg-white shadow-sm"
                                        placeholder="you@example.com">
                                </div>
                                <button type="submit" id="sendResetBtn"
                                    class="w-full flex items-center justify-center px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                    <svg id="sendResetSpinner" class="hidden h-4 w-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/>
                                    </svg>
                                    Send Reset Link
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script nonce="{{ $cspNonce ?? '' }}">
(function() {
    const passwordInput = document.getElementById('password');
    const capsLockWarning = document.getElementById('capsLockWarning');
    const strengthEl = document.getElementById('passwordStrength');

    function checkCapsLock(e) {
        const isOn = e.getModifierState && e.getModifierState('CapsLock');
        capsLockWarning.classList.toggle('hidden', !isOn);
    }

    function updateStrength() {
        const val = passwordInput.value;

        if (val.length === 0) {
            strengthEl.classList.add('hidden');
            return;
        }
        strengthEl.classList.remove('hidden');

        const checks = {
            pwLength: val.length > 6,
            pwUpper: /[A-Z]/.test(val),
            pwLower: /[a-z]/.test(val),
            pwNumber: /[0-9]/.test(val),
            pwSpecial: /[^A-Za-z0-9]/.test(val),
        };

        Object.entries(checks).forEach(([id, pass]) => {
            const el = document.getElementById(id);
            const indicator = el.querySelector('span:first-child');
            if (pass) {
                el.classList.remove('text-gray-400');
                el.classList.add('text-emerald-600');
                indicator.textContent = '✓';
            } else {
                el.classList.remove('text-emerald-600');
                el.classList.add('text-gray-400');
                indicator.textContent = '✕';
            }
        });
    }

    if (passwordInput) {
        passwordInput.addEventListener('keydown', checkCapsLock);
        passwordInput.addEventListener('keyup', function(e) {
            checkCapsLock(e);
            updateStrength();
        });
        passwordInput.addEventListener('input', updateStrength);
    }

    @if (!$isRegisterPage)
    const credentialInput = document.getElementById('credential');
    const rememberCheck = document.getElementById('rememberMe');
    const passwordField = document.getElementById('password');
    const loginForm = document.getElementById('loginForm');

    const saved = localStorage.getItem('remembered_credential');
    const savedPass = localStorage.getItem('remembered_password');
    if (saved) {
        credentialInput.value = saved;
        rememberCheck.checked = true;
        if (savedPass) {
            passwordField.value = savedPass;
        }
    }

    loginForm.addEventListener('submit', function() {
        if (rememberCheck.checked) {
            localStorage.setItem('remembered_credential', credentialInput.value);
            localStorage.setItem('remembered_password', passwordField.value);
        } else {
            localStorage.removeItem('remembered_credential');
            localStorage.removeItem('remembered_password');
        }
    });

    // Forgot Password Modal
    const forgotLink = document.getElementById('forgotPasswordLink');
    const forgotModal = document.getElementById('forgotPasswordModal');
    const closeForgot = document.getElementById('closeForgotModal');
    const forgotForm = document.getElementById('forgotPasswordForm');
    const forgotEmail = document.getElementById('forgotEmail');
    const forgotError = document.getElementById('forgotError');
    const forgotSuccess = document.getElementById('forgotSuccess');
    const sendResetBtn = document.getElementById('sendResetBtn');
    const sendResetSpinner = document.getElementById('sendResetSpinner');

    function openForgotModal(e) {
        e.preventDefault();
        forgotError.classList.add('hidden');
        forgotSuccess.classList.add('hidden');
        forgotEmail.value = credentialInput.value;
        forgotModal.classList.remove('hidden');
    }

    function closeForgotModal() {
        forgotModal.classList.add('hidden');
    }

    forgotLink.addEventListener('click', openForgotModal);
    closeForgot.addEventListener('click', closeForgotModal);
    forgotModal.addEventListener('click', function(e) {
        if (e.target === forgotModal) closeForgotModal();
    });

    forgotForm.addEventListener('submit', function(e) {
        e.preventDefault();
        forgotError.classList.add('hidden');
        forgotSuccess.classList.add('hidden');
        sendResetBtn.disabled = true;
        sendResetSpinner.classList.remove('hidden');

        fetch('{{ route("password.email") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ email: forgotEmail.value }),
        })
        .then(r => r.json())
        .then(data => {
            forgotSuccess.textContent = data.message;
            forgotSuccess.classList.remove('hidden');
            sendResetBtn.disabled = false;
            sendResetSpinner.classList.add('hidden');
            forgotEmail.value = '';
        })
        .catch(() => {
            forgotError.textContent = 'Something went wrong. Please try again.';
            forgotError.classList.remove('hidden');
            sendResetBtn.disabled = false;
            sendResetSpinner.classList.add('hidden');
        });
    });
    @endif
})();
</script>
@stack('scripts')
</body>
</html>
