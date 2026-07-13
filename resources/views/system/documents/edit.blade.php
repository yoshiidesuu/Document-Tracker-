@extends('layouts.system')

@section('title', \App\Models\SystemSetting::get('site_long_name', config('app.name', 'Document Tracker')) . ' - Edit Document')

@section('page_title', 'Edit Document')

@section('content')
<div class="max-w-2xl mx-auto">
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
        <a href="{{ route('system.documents.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 transition-colors">
            <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Back to Documents
        </a>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Edit Document</h2>
        </div>

        <form method="POST" action="{{ route('system.documents.update', $document->id) }}" class="px-6 py-5 space-y-5">
            @csrf

            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Document Title <span class="text-red-500">*</span></label>
                <input type="text" id="title" name="title" value="{{ old('title', $document->title) }}" required maxlength="255" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm" placeholder="Enter document title">
            </div>

            <div>
                <label for="document_type" class="block text-sm font-medium text-gray-700 mb-1">Document Type <span class="text-red-500">*</span></label>
                <select id="document_type" name="document_type" required class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm">
                    <option value="">Select a document type...</option>
                    @foreach ($documentTypes as $dt)
                        <option value="{{ $dt->name }}" @selected(old('document_type', $document->document_type) === $dt->name)>{{ $dt->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="arta_setting_id" class="block text-sm font-medium text-gray-700 mb-1">ARTA Setting <span class="text-red-500">*</span></label>
                <select id="arta_setting_id" name="arta_setting_id" required class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm">
                    @forelse ($artaSettings as $arta)
                        <option value="{{ $arta->id }}" data-days="{{ $arta->days ?? 0 }}" data-hours="{{ $arta->hours ?? 0 }}" data-minutes="{{ $arta->minutes ?? 0 }}" @selected((string) old('arta_setting_id', $document->arta_setting_id) === (string) $arta->id)>
                            {{ $arta->category }} - {{ $arta->title }} ({{ $arta->duration_label }})
                        </option>
                    @empty
                        <option value="" disabled selected>No ARTA settings available</option>
                    @endforelse
                </select>
            </div>

            <div>
                <label for="processing_time_display" class="block text-sm font-medium text-gray-700 mb-1">Processing Time (ARTA) <span class="text-red-500">*</span></label>
                <input type="text" id="processing_time_display" name="processing_time_display" value="{{ old('processing_time_display', $document->arta_duration_label) }}" readonly required class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 bg-gray-50 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm" placeholder="Auto-filled from ARTA setting">
                <input type="hidden" id="processing_hours" name="processing_hours" value="{{ old('processing_hours', number_format((float) $document->processing_hours, 2, '.', '')) }}">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="is_private" class="block text-sm font-medium text-gray-700 mb-1">Access Level <span class="text-red-500">*</span></label>
                    <select id="is_private" name="is_private" required class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm">
                        <option value="0" @selected((string) old('is_private', (int) $document->is_private) === '0')>Public</option>
                        <option value="1" @selected((string) old('is_private', (int) $document->is_private) === '1')>Private</option>
                    </select>
                </div>
                <div>
                    <label for="access_key" class="block text-sm font-medium text-gray-700 mb-1">Access Key</label>
                    <input type="text" id="access_key" name="access_key" value="{{ old('access_key', $document->access_key) }}" maxlength="255" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm" placeholder="Required if private">
                </div>
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                <textarea id="notes" name="notes" rows="3" maxlength="5000" class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all text-sm" placeholder="Optional notes...">{{ old('notes', $document->notes) }}</textarea>
            </div>

            <div class="flex items-center justify-end space-x-3 pt-2">
                <a href="{{ route('system.documents.view', $document->id) }}" class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    Update Document
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script nonce="{{ $cspNonce }}">
(function () {
    function updateProcessingHours() {
        var select = document.getElementById('arta_setting_id');
        var input = document.getElementById('processing_hours');
        var display = document.getElementById('processing_time_display');
        if (!select || !input || !display || !select.options.length) return;
        var option = select.options[select.selectedIndex];
        if (!option) return;
        var days = parseInt(option.dataset.days || '0', 10);
        var hours = parseInt(option.dataset.hours || '0', 10);
        var minutes = parseInt(option.dataset.minutes || '0', 10);
        var totalMinutes = (days * 24 * 60) + (hours * 60) + minutes;
        var totalHours = totalMinutes / 60;
        input.value = totalHours.toFixed(2);

        var parts = [];
        if (days) parts.push(days + ' day' + (days > 1 ? 's' : ''));
        if (hours) parts.push(hours + ' hour' + (hours > 1 ? 's' : ''));
        if (minutes) parts.push(minutes + ' minute' + (minutes > 1 ? 's' : ''));
        display.value = parts.length ? parts.join(', ') : '-';
    }

    document.addEventListener('DOMContentLoaded', function () {
        updateProcessingHours();
        var select = document.getElementById('arta_setting_id');
        if (select) {
            select.addEventListener('change', updateProcessingHours);
        }
    });
})();
</script>
@endpush
