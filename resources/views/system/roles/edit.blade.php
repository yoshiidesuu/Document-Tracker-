@extends('layouts.system')

@section('title', \App\Models\SystemSetting::get('site_long_name', config('app.name', 'Document Tracker')) . ' - Edit Role')

@section('page_title', 'Edit Role')

@section('content')
<div class="max-w-2xl mx-auto">
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
        <a href="{{ route('system.roles.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 transition-colors">
            <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Back to Roles
        </a>
    </div>

    <form method="POST" action="{{ route('system.roles.update', $role->id) }}" class="space-y-6">
        @csrf

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Role Details</h2>
                @if ($role->is_system)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">System Role</span>
                @endif
            </div>
            <div class="px-6 py-5 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Role Name</label>
                        @if ($role->is_system)
                            <input type="text" id="name" value="{{ $role->name }}" disabled class="mt-1 block w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-sm text-gray-500 cursor-not-allowed">
                        @else
                            <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        @endif
                    </div>
                    <div>
                        <label for="slug" class="block text-sm font-medium text-gray-700">Slug</label>
                        @if ($role->is_system)
                            <input type="text" id="slug" value="{{ $role->slug }}" disabled class="mt-1 block w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-sm text-gray-500 cursor-not-allowed">
                        @else
                            <input type="text" name="slug" id="slug" value="{{ old('slug', $role->slug) }}" required pattern="^[a-z0-9-]+$" title="Lowercase letters, numbers, and hyphens only" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        @endif
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea name="description" id="description" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">{{ old('description', $role->description) }}</textarea>
                </div>

                <div>
                    <label for="permissions" class="block text-sm font-medium text-gray-700">Permissions</label>
                    @if ($role->is_system)
                        <p class="text-xs text-gray-500 mb-2">System role permissions cannot be edited.</p>
                        <textarea id="permissions" rows="6" disabled class="mt-1 block w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-sm text-gray-500 cursor-not-allowed font-mono">{{ implode("\n", $role->permissions ?? []) }}</textarea>
                    @else
                        <p class="text-xs text-gray-500 mb-2">One permission per line.</p>
                        <textarea name="permissions" id="permissions" rows="6" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm font-mono">{{ old('permissions', implode("\n", $role->permissions ?? [])) }}</textarea>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('system.roles.index') }}" class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Cancel</a>
            <button type="submit" class="px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">Save Changes</button>
        </div>
    </form>
</div>
@endsection
