@extends('layouts.system')

@section('title', \App\Models\SystemSetting::get('site_long_name', config('app.name', 'Document Tracker')) . ' - ARTA Settings')

@section('page_title', 'ARTA Settings')

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
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">All ARTA Settings ({{ $groups->flatten()->count() }})</h2>
                @if(auth()->user()->hasPermission('arta.create'))
                <a href="{{ route('system.arta-settings.create') }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                    <svg class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                    Add ARTA Setting
                </a>
                @endif
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="text-left px-6 py-3 font-medium text-gray-500">Category</th>
                        <th class="text-left px-6 py-3 font-medium text-gray-500">Title</th>
                        <th class="text-left px-6 py-3 font-medium text-gray-500">Duration</th>
                        <th class="text-center px-6 py-3 font-medium text-gray-500">Status</th>
                        <th class="text-right px-6 py-3 font-medium text-gray-500">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($groups as $category => $items)
                        @foreach ($items as $index => $arta)
                            <tr class="hover:bg-gray-50 transition-colors">
                                @if ($index === 0)
                                    <td class="px-6 py-4 align-top" rowspan="{{ $items->count() }}">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">{{ $category }}</span>
                                    </td>
                                @endif
                                <td class="px-6 py-4">
                                    <span class="font-medium text-gray-900">{{ $arta->title }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $arta->duration_label }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($arta->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Active</span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button type="button" class="dropdown-toggle inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors" data-arta-id="{{ $arta->id }}">
                                        Actions
                                        <svg class="h-3 w-3 ml-1 -mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                                    </button>
                                    <div class="dropdown-menu hidden fixed w-40 bg-white border border-gray-200 rounded-lg shadow-lg z-[9999] py-1" data-arta-id="{{ $arta->id }}">
                                        @if(auth()->user()->hasPermission('arta.view'))
                                        <a href="{{ route('system.arta-settings.view', $arta->id) }}" class="flex items-center px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 transition-colors">
                                            <svg class="h-3.5 w-3.5 mr-2 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                            View
                                        </a>
                                        @endif
                                        @if(auth()->user()->hasPermission('arta.edit'))
                                        <a href="{{ route('system.arta-settings.edit', $arta->id) }}" class="flex items-center px-4 py-2 text-xs text-gray-700 hover:bg-gray-50 transition-colors">
                                            <svg class="h-3.5 w-3.5 mr-2 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                                            Edit
                                        </a>
                                        @endif
                                        @if(auth()->user()->hasPermission('arta.delete'))
                                        <hr class="my-1 border-gray-100">
                                        <form method="POST" action="{{ route('system.arta-settings.destroy', $arta->id) }}" data-confirm="Delete '{{ $arta->title }}' in {{ $arta->category }}? This cannot be undone.">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="flex items-center w-full px-4 py-2 text-xs text-red-700 hover:bg-red-50 transition-colors">
                                                <svg class="h-3.5 w-3.5 mr-2 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                                Delete
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">No ARTA settings found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.dropdown-toggle').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            closeAllDropdowns();
            var menu = this.nextElementSibling;
            if (menu && menu.classList.contains('dropdown-menu')) {
                var rect = this.getBoundingClientRect();
                menu.style.left = Math.max(8, rect.left + rect.width - 160) + 'px';
                menu.style.top = (rect.bottom + 4) + 'px';
                menu.classList.remove('hidden');
            }
        });
    });

    document.addEventListener('click', function () { closeAllDropdowns(); });
    document.addEventListener('scroll', function () { closeAllDropdowns(); }, true);
    window.addEventListener('resize', function () { closeAllDropdowns(); });

    function closeAllDropdowns() {
        document.querySelectorAll('.dropdown-menu').forEach(function (m) {
            m.classList.add('hidden');
        });
    }
});
</script>
@endpush
