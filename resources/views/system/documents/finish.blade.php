@php
    $title = 'Finish Scanner';
    $breadcrumbs = [
        ['label' => 'System', 'url' => route('system.dashboard')],
        ['label' => 'Documents', 'url' => route('system.documents.index')],
        ['label' => 'Finish Scanner'],
    ];
@endphp

@extends('layouts.system')
@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Finish Document Transaction</h2>
            <div class="flex rounded-lg border border-gray-300 overflow-hidden">
                <button type="button" id="mode-qr"
                    class="px-4 py-1.5 text-sm font-medium transition-colors bg-indigo-600 text-white">
                    QR Code
                </button>
                <button type="button" id="mode-barcode"
                    class="px-4 py-1.5 text-sm font-medium transition-colors text-gray-600 hover:bg-gray-100">
                    Barcode
                </button>
            </div>
        </div>

        <div id="scanner-container" class="relative w-full aspect-video bg-gray-900 rounded-lg overflow-hidden">
            <div id="scanner-loading" class="absolute inset-0 flex items-center justify-center text-gray-400 text-sm z-10">
                Loading camera...
            </div>
            <div id="scanner" class="w-full h-full"></div>
            <div id="detect-highlight" class="hidden absolute z-20 border-[3px] border-green-400 rounded-lg shadow-[0_0_20px_6px_rgba(74,222,128,0.35)] pointer-events-none"></div>
            <div id="scan-flash" class="hidden absolute inset-0 z-10 pointer-events-none">
                <div class="absolute inset-0 bg-green-500/20 animate-ping-slow"></div>
            </div>
        </div>

        <div class="mt-4 flex items-center gap-3">
            <input type="text" id="manual-code" placeholder="Or enter QR / barcode value manually..."
                class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            <button type="button" id="manual-lookup-btn"
                class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                Look Up
            </button>
        </div>
        <p id="scan-status" class="mt-3 text-sm text-gray-500"></p>
    </div>

    <div id="result-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/50 transition-opacity"></div>
        <div class="relative bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto overflow-x-hidden">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between rounded-t-xl z-10">
                <h3 class="text-lg font-semibold text-gray-900">Document Information</h3>
                <button type="button" id="close-modal" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-6 space-y-6">
                <div id="document-details" class="grid grid-cols-2 gap-4 text-sm">
                    <div class="col-span-2 flex items-start gap-4">
                        <img id="doc-qr-image" src="" alt="QR Code" class="w-20 h-20 border rounded flex-shrink-0">
                        <div class="min-w-0">
                            <h4 id="doc-title" class="text-base font-semibold text-gray-900 break-words"></h4>
                            <p id="doc-type" class="text-gray-500"></p>
                            <p id="doc-creator" class="text-gray-500"></p>
                        </div>
                    </div>
                    <div>
                        <span class="block text-gray-500">Created</span>
                        <span id="doc-created-at" class="text-gray-900"></span>
                    </div>
                    <div>
                        <span class="block text-gray-500">Updated</span>
                        <span id="doc-updated-at" class="text-gray-900"></span>
                    </div>
                    <div class="col-span-2">
                        <span class="block text-gray-500">Notes</span>
                        <p id="doc-notes" class="text-gray-900 whitespace-pre-wrap"></p>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4">
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">Current Holder</h4>
                    <div id="current-holder-info" class="bg-indigo-50 rounded-lg p-4 text-sm text-indigo-900">
                        <span id="current-holder-text" class="font-medium"></span>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4">
                    <h4 class="text-sm font-semibold text-gray-900 mb-3">Past Holders</h4>
                    <div id="past-holders-list" class="space-y-2">
                        <p class="text-sm text-gray-500 italic">No past holders.</p>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4 flex justify-end">
                    <button type="button" id="finish-btn"
                        class="px-6 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        Finish Transaction
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<form id="finish-form" method="POST" action="" class="hidden">
    @csrf
</form>
@endsection

@push('styles')
<style>
@keyframes ping-slow {
    0% { opacity: 0.4; transform: scale(1); }
    50% { opacity: 0.2; transform: scale(1.03); }
    100% { opacity: 0; transform: scale(1.05); }
}

.animate-ping-slow {
    animation: ping-slow 0.5s ease-out forwards;
}

#detect-highlight {
    transition: none;
}
</style>
@endpush

@push('scripts')
@vite(['resources/js/finish.js'])
@endpush
