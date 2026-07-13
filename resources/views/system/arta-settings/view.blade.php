@extends('layouts.system')

@section('title', \App\Models\SystemSetting::get('site_long_name', config('app.name', 'Document Tracker')) . ' - ARTA Setting Details')

@section('page_title', 'ARTA Setting Details')

@section('content')
<div class="max-w-3xl mx-auto">
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
        <a href="{{ route('system.arta-settings.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 transition-colors">
            <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Back to ARTA Settings
        </a>
        <div class="flex items-center gap-2">
            @if(auth()->user()->hasPermission('arta.edit'))
            <a href="{{ route('system.arta-settings.edit', $artaSetting->id) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                <svg class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                Edit
            </a>
            @endif
            @if(auth()->user()->hasPermission('arta.toggle-status'))
            <form method="POST" action="{{ route('system.arta-settings.toggle-status', $artaSetting->id) }}">
                @csrf
                @if ($artaSetting->is_active)
                    <button type="submit" class="px-3 py-1.5 text-xs font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-lg hover:bg-amber-100 transition-colors">Deactivate</button>
                @else
                    <button type="submit" class="px-3 py-1.5 text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition-colors">Activate</button>
                @endif
            </form>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">ARTA Setting Information</h2>
                @if ($artaSetting->is_active)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Active</span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Inactive</span>
                @endif
            </div>
        </div>
        <div class="px-6 py-5">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-500">Category:</span><br>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 mt-1">{{ $artaSetting->category }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Title:</span><br>
                    <span class="font-medium">{{ $artaSetting->title }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Duration:</span><br>
                    <span class="font-medium">{{ $artaSetting->duration_label }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Days:</span><br>
                    <span class="font-medium">{{ $artaSetting->days ?? '—' }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Hours:</span><br>
                    <span class="font-medium">{{ $artaSetting->hours ?? '—' }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Minutes:</span><br>
                    <span class="font-medium">{{ $artaSetting->minutes ?? '—' }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Created:</span><br>
                    <span class="font-medium">{{ $artaSetting->created_at->format('M d, Y h:i A') }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Last Updated:</span><br>
                    <span class="font-medium">{{ $artaSetting->updated_at->format('M d, Y h:i A') }}</span>
                </div>
            </div>
        </div>
    </div>

    @if(auth()->user()->hasPermission('arta.delete'))
    <div class="mt-6 bg-white rounded-xl border border-red-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-red-200 bg-red-50">
            <h2 class="text-lg font-semibold text-red-800">Danger Zone</h2>
        </div>
        <div class="px-6 py-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-900">Delete this ARTA setting</p>
                    <p class="text-xs text-gray-500">Once deleted, this cannot be undone.</p>
                </div>
                <form method="POST" action="{{ route('system.arta-settings.destroy', $artaSetting->id) }}" data-confirm="Are you sure you want to permanently delete this ARTA setting? This cannot be undone.">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">Delete</button>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
