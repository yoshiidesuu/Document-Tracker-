@extends('layouts.system')

@section('title', \App\Models\SystemSetting::get('site_long_name', config('app.name', 'Document Tracker')) . ' - Documents')

@section('page_title', 'Documents')

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
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <h2 class="text-lg font-semibold text-gray-900">All Documents ({{ $documents->count() }})</h2>
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <form method="GET" action="{{ route('system.documents.index') }}" class="flex items-center gap-2 w-full sm:w-auto">
                        <div class="relative flex-1 sm:flex-initial">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
                            <input type="text" name="search" placeholder="Search documents..." value="{{ request('search') }}" class="w-full sm:w-48 pl-9 pr-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <select name="type" class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">All Types</option>
                            @foreach($documentTypes as $dt)
                                <option value="{{ $dt->name }}" {{ request('type') === $dt->name ? 'selected' : '' }}>{{ $dt->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-3 py-1.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">Filter</button>
                        @if(request('search') || request('type'))
                            <a href="{{ route('system.documents.index') }}" class="px-3 py-1.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">Clear</a>
                        @endif
                    </form>
                    @if(auth()->user()->hasPermission('documents.create'))
                    <a href="{{ route('system.documents.create') }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors whitespace-nowrap">
                        <svg class="h-3.5 w-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        Generate
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse border border-gray-300" style="table-layout:fixed">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="text-center px-6 py-3 font-bold text-gray-700 w-[40%] border border-gray-300">Title</th>
                        <th class="text-center px-6 py-3 font-bold text-gray-700 w-[15%] border border-gray-300">Type</th>
                        <th class="text-center px-6 py-3 font-bold text-gray-700 w-[20%] border border-gray-300">Creator</th>
                        <th class="text-center px-6 py-3 font-bold text-gray-700 w-[10%] border border-gray-300">Hours</th>
                        <th class="text-center px-6 py-3 font-bold text-gray-700 w-[15%] border border-gray-300">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($documents as $document)
                        <tr class="hover:bg-indigo-50 transition-colors even:bg-gray-50 cursor-pointer" data-href="{{ route('system.documents.view', $document->id) }}">
                            <td class="px-6 py-4 text-center border border-gray-300" style="word-break:break-word;overflow-wrap:break-word">
                                <span class="font-medium text-gray-900" title="{{ $document->title }}">{{ $document->title }}</span>
                            </td>
                            <td class="px-6 py-4 text-center border border-gray-300">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">{{ $document->document_type }}</span>
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-600 border border-gray-300">
                                {{ $document->creator->full_name ?? $document->creator->name }}
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-600 border border-gray-300">
                                {{ number_format($document->processing_hours, 2) }}
                            </td>
                            <td class="px-6 py-4 text-center border border-gray-300">
                                <button type="button" class="dropdown-toggle inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors" data-document-id="{{ $document->id }}">
                                    Actions
                                    <svg class="h-4 w-4 ml-1.5 -mr-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                                </button>
                                @include('system.documents._dropdown', ['document' => $document])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500 border border-gray-300">No documents found.</td>
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

    document.querySelectorAll('tr[data-href]').forEach(function (row) {
        row.addEventListener('click', function (e) {
            var target = e.target;
            while (target && target !== row) {
                if (target.tagName === 'A' || target.tagName === 'BUTTON' || target.tagName === 'FORM' || target.closest('.dropdown-menu')) {
                    return;
                }
                target = target.parentNode;
            }
            window.location = row.getAttribute('data-href');
        });
    });
});
</script>
@endpush
