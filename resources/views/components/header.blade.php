@php
    $siteShortName = \App\Models\SystemSetting::get('site_short_name', config('app.name', 'DT'));
    $logoDataUrl = \App\Models\SystemSetting::getFileDataUrl('site_logo', 'system/logo');
@endphp
<nav class="bg-white border-b border-gray-200 fixed top-0 left-0 right-0 z-50">
    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-14 sm:h-16">
            <div class="flex items-center space-x-2 sm:space-x-3">
                <div class="flex-shrink-0 flex items-center space-x-1.5 sm:space-x-2">
                    @php $siteLogo = \App\Models\SystemSetting::get('site_logo'); @endphp
                    @if ($siteLogo && $logoDataUrl)
                        <img src="{{ $logoDataUrl }}" alt="Logo" class="h-7 w-7 sm:h-8 sm:w-8 object-contain flex-shrink-0"  style="-webkit-user-drag: none; user-select: none;">
                    @else
                        <svg class="h-6 w-6 sm:h-8 sm:w-8 text-indigo-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    @endif
                    <span class="text-base sm:text-xl font-bold text-gray-900 tracking-tight truncate max-w-[140px] sm:max-w-none">{{ $siteShortName }}</span>
                </div>
            </div>

            @if (Route::has('login') && !auth()->check())
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <a href="{{ route('login') }}" class="text-xs sm:text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors whitespace-nowrap">
                        Sign in
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="inline-flex items-center px-3 py-1.5 sm:px-4 sm:py-2 text-xs sm:text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors whitespace-nowrap">
                            <span class="hidden sm:inline">Get started</span>
                            <span class="sm:hidden">Register</span>
                        </a>
                    @endif
                </div>
            @endif

            @auth
                <div class="flex items-center space-x-2 sm:space-x-4">
                    <a href="{{ route('system.dashboard') }}" class="text-xs sm:text-sm font-medium text-indigo-600 hover:text-indigo-500 transition-colors whitespace-nowrap">
                        Dashboard
                    </a>
                    <span class="text-xs sm:text-sm text-gray-600 truncate max-w-[100px] sm:max-w-[200px]">{{ auth()->user()->firstname ?: auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-xs sm:text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors whitespace-nowrap">
                            Sign out
                        </button>
                    </form>
                </div>
            @endauth
        </div>
    </div>
</nav>
