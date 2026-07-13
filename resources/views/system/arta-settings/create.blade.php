@extends('layouts.system')

@section('title', \App\Models\SystemSetting::get('site_long_name', config('app.name', 'Document Tracker')) . ' - Add ARTA Setting')

@section('page_title', 'Add ARTA Setting')

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
        <a href="{{ route('system.arta-settings.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 transition-colors">
            <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Back to ARTA Settings
        </a>
    </div>

    <form method="POST" action="{{ route('system.arta-settings.store') }}" class="space-y-6">
        @csrf

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">ARTA Setting Details</h2>
            </div>
            <div class="px-6 py-5 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700">Category <span class="text-red-500">*</span></label>
                        <div class="mt-1 flex gap-2">
                            <select id="category_select" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <option value="">Select existing...</option>
                                @foreach ($categories as $cat)
                                    <option value="{{ $cat }}" @selected(old('category') === $cat)>{{ $cat }}</option>
                                @endforeach
                                <option value="__new__">+ New category</option>
                            </select>
                            <input type="text" name="category" id="category_input" value="{{ old('category') }}" required maxlength="255" placeholder="Or type new" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        </div>
                    </div>
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Title <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required maxlength="255" placeholder="e.g. ARTA" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Processing Duration</label>
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                            <label for="days" class="block text-xs text-gray-500 mb-1">Days</label>
                            <input type="number" name="days" id="days" value="{{ old('days') }}" min="0" max="9999" placeholder="0" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        </div>
                        <div>
                            <label for="hours" class="block text-xs text-gray-500 mb-1">Hours</label>
                            <input type="number" name="hours" id="hours" value="{{ old('hours') }}" min="0" max="9999" placeholder="0" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        </div>
                        <div>
                            <label for="minutes" class="block text-xs text-gray-500 mb-1">Minutes</label>
                            <input type="number" name="minutes" id="minutes" value="{{ old('minutes') }}" min="0" max="9999" placeholder="0" class="block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-end space-x-3">
            <a href="{{ route('system.arta-settings.index') }}" class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">Cancel</a>
            <button type="submit" class="px-4 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">Create ARTA Setting</button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
document.addEventListener('DOMContentLoaded', function () {
    var select = document.getElementById('category_select');
    var input = document.getElementById('category_input');

    function sync() {
        if (select.value === '__new__') {
            input.value = '';
            input.readOnly = false;
            input.focus();
        } else if (select.value) {
            input.value = select.value;
            input.readOnly = true;
        } else {
            input.readOnly = false;
        }
    }

    select.addEventListener('change', sync);
    sync();
});
</script>
@endpush
