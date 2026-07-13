@php
    $title = 'Document Details';
    $breadcrumbs = [
        ['label' => 'Documents', 'url' => route('system.documents.index')],
        ['label' => $document->title],
    ];
@endphp

@extends('layouts.system')
@section('content')
<div class="space-y-6">
    @if (session('success'))
        <div class="rounded-lg bg-emerald-50 border border-emerald-200 p-4">
            <div class="flex items-start gap-3">
                <svg class="h-5 w-5 text-emerald-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm text-emerald-700">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if ($errors->any())
        <div class="rounded-lg bg-red-50 border border-red-200 p-4">
            <div class="flex items-start gap-3">
                <svg class="h-5 w-5 text-red-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
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

    <div class="flex items-center justify-between">
        <a href="{{ route('system.documents.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 transition-colors">
            <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Back to Documents
        </a>
        <div class="flex items-center gap-2">
            @if(auth()->user()->hasPermission('documents.view'))
            <a href="{{ route('system.documents.print', $document->id) }}"
               class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                <svg class="h-3.5 w-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" /></svg>
                Re-Print
            </a>
            @endif
            @if(auth()->user()->hasPermission('documents.edit') && !in_array($document->status, ['finished', 'terminated']))
            <a href="{{ route('system.documents.edit', $document->id) }}"
               class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                <svg class="h-3.5 w-3.5 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" /></svg>
                Edit
            </a>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Document Information</h2>
        </div>
        <div class="px-4 sm:px-6 py-5">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm">
                <div class="sm:col-span-2">
                    <dt class="text-gray-500">Title</dt>
                    <dd class="font-medium text-gray-900 break-words">{{ $document->title }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Document Type</dt>
                    <dd><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800 mt-0.5">{{ $document->document_type }}</span></dd>
                </div>
                <div>
                    <dt class="text-gray-500">Status</dt>
                    <dd>
                        @if($document->status === 'terminated')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mt-0.5">Terminated</span>
                        @elseif($document->status === 'finished')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 mt-0.5">Finished</span>
                        @elseif($currentTrack)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 mt-0.5">Active</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 mt-0.5">Unclaimed</span>
                        @endif
                        @if(in_array($document->status, ['finished', 'terminated']) && auth()->user()->hasPermission('documents.reopen'))
                        <button type="button" data-action="reopen" data-id="{{ $document->id }}"
                            class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 hover:bg-amber-200 transition-colors">
                            Reopen
                        </button>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="text-gray-500">Processing Hours</dt>
                    <dd class="font-medium">{{ number_format($document->processing_hours, 2) }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">ARTA Duration</dt>
                    <dd class="font-medium">{{ $document->arta_duration_label }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Created By</dt>
                    <dd class="font-medium">{{ $document->creator->full_name ?? $document->creator->name }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Department</dt>
                    <dd class="font-medium">{{ $document->creator->department->name ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Office</dt>
                    <dd class="font-medium">{{ $document->creator->office->name ?? '-' }}</dd>
                </div>
                @if($document->notes)
                <div class="sm:col-span-2">
                    <dt class="text-gray-500">Notes</dt>
                    <dd class="text-gray-700 break-words">{{ $document->notes }}</dd>
                </div>
                @endif
                <div>
                    <dt class="text-gray-500">Created</dt>
                    <dd class="font-medium">{{ $document->created_at->format('M d, Y h:i A') }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Last Updated</dt>
                    <dd class="font-medium">{{ $document->updated_at->format('M d, Y h:i A') }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">QR Code</h2>
            </div>
            <div class="px-4 sm:px-6 py-5 flex flex-col items-center gap-3">
                <img src="{{ $document->getQrCodeUrl() }}" alt="QR Code" class="w-40 h-40 sm:w-48 sm:h-48 object-contain"  style="-webkit-user-drag: none; user-select: none;">
                <button data-action="copy" data-value="{{ $document->qr_value }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184" /></svg>
                    Copy Code
                </button>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Barcode</h2>
            </div>
            <div class="px-4 sm:px-6 py-5 flex flex-col items-center justify-center gap-3">
                <img src="{{ $document->getBarcodeUrl() }}" alt="Barcode" class="w-full max-w-md object-contain mx-auto"  style="-webkit-user-drag: none; user-select: none;">
                <button data-action="copy" data-value="{{ $document->barcode_value }}"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.666 3.888A2.25 2.25 0 0013.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 01-.75.75H9a.75.75 0 01-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 01-2.25 2.25H6.75A2.25 2.25 0 014.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 011.927-.184" /></svg>
                    Copy Code
                </button>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="space-y-6">
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Current Holder</h2>
                </div>
                <div class="px-4 sm:px-6 py-5">
                    @if($currentTrack)
                        @php
                            $ch = $currentTrack->user;
                            $start = $currentTrack->received_at;
                            $heldMinutes = (int) $start->diffInRealMinutes(now());
                            $heldLabel = $heldMinutes > 60
                                ? round($heldMinutes / 60, 1) . ' hours'
                                : $heldMinutes . ' minutes';
                        @endphp
                        <div class="flex flex-col items-center text-center gap-2">
                            @if($ch->profile_picture_url)
                                <img src="{{ $ch->profile_picture_url }}" alt="" class="h-16 w-16 rounded-full object-cover border-2 border-emerald-300"  style="-webkit-user-drag: none; user-select: none;">
                            @else
                                <div class="h-16 w-16 rounded-full bg-emerald-100 flex items-center justify-center text-xl font-bold text-emerald-600 border-2 border-emerald-300">
                                    {{ $ch->initials }}
                                </div>
                            @endif
                            <div>
                                <p class="text-sm font-semibold text-gray-900">{{ $ch->full_name ?? $ch->name }}</p>
                                <p class="text-xs text-gray-500">{{ $ch->office->name ?? $ch->department->name ?? '' }}</p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">Holding</span>
                            <div class="w-full pt-1 text-xs text-gray-500 space-y-1">
                                <p>Since {{ $start->format('M d, Y h:i A') }}</p>
                                <p class="font-medium text-gray-700">Duration: {{ $heldLabel }}</p>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-gray-500 text-center py-4">No current holder.</p>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Rankings</h2>
                </div>
                <div class="px-4 sm:px-6 py-5">
                    @if($userRankings->isEmpty())
                        <p class="text-sm text-gray-500 text-center py-4">No tracking data.</p>
                    @else
                        <div class="space-y-2">
                            @foreach($userRankings as $i => $item)
                            <div class="flex items-center gap-3 p-2 rounded-lg {{ $i === 0 ? 'bg-amber-50 border border-amber-200' : ($i < 3 ? 'bg-gray-50 border border-gray-100' : '') }}">
                                <div class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold shrink-0
                                    {{ $i === 0 ? 'bg-amber-400 text-white' : ($i === 1 ? 'bg-gray-300 text-gray-700' : ($i === 2 ? 'bg-amber-700 text-white' : 'bg-gray-100 text-gray-500')) }}">
                                    {{ $i + 1 }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $item->user->full_name ?? $item->user->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ $item->user->office->name ?? $item->user->department->name ?? '' }}</p>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="text-sm font-semibold text-gray-900">{{ round($item->total_minutes / 60, 1) }}h</p>
                                    <p class="text-xs text-gray-400">{{ $item->count }} {{ Str::plural('hold', $item->count) }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="md:col-span-2 bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Holder Timeline</h2>
            </div>
            <div class="px-4 sm:px-6 py-5">
                @php
                    $currentEntry = $timeline->firstWhere('is_current', true);
                    $pastEntries = $timeline->where('is_current', false)->sortByDesc('received_at');
                @endphp
                <div class="relative">
                    <div class="absolute left-[21px] sm:left-[23px] top-0 bottom-0 w-0.5 bg-gray-200"></div>
                    <div class="space-y-6">
                        @if($currentEntry)
                        <div class="relative pl-14 sm:pl-16">
                            <div class="absolute left-[13px] sm:left-[15px] top-3 w-4 h-4 rounded-full border-[3px] bg-emerald-400 border-emerald-500"></div>
                            <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-3 sm:p-4">
                                <div class="flex items-start gap-3">
                                    @if($currentEntry->user->profile_picture_url)
                                        <img src="{{ $currentEntry->user->profile_picture_url }}" alt="" class="h-8 w-8 sm:h-10 sm:w-10 rounded-full object-cover shrink-0 border border-emerald-300"  style="-webkit-user-drag: none; user-select: none;">
                                    @else
                                        <div class="h-8 w-8 sm:h-10 sm:w-10 rounded-full bg-emerald-100 flex items-center justify-center text-xs font-bold text-emerald-600 shrink-0 border border-emerald-300">
                                            {{ $currentEntry->user->initials }}
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2">
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">{{ $currentEntry->user->full_name ?? $currentEntry->user->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $currentEntry->user->office->name ?? $currentEntry->user->department->name ?? '' }}</p>
                                            </div>
                                            <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-200 text-emerald-800">Received</span>
                                        </div>
                                        <div class="mt-2 text-xs text-gray-500 space-y-1">
                                            <p><span class="text-gray-400">Date:</span> {{ $currentEntry->received_at->format('M d, Y') }}</p>
                                            <p><span class="text-gray-400">Time:</span> {{ $currentEntry->received_at->format('h:i A') }}</p>
                                            <p><span class="text-gray-400">Office:</span> {{ $currentEntry->user->office->name ?? $currentEntry->user->department->name ?? 'N/A' }}</p>
                                            <p class="font-medium text-gray-700"><span class="text-gray-400">Duration:</span> <span class="live-timer" data-received="{{ $currentEntry->received_at->toIso8601String() }}">{{ $currentEntry->duration_label }}</span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @foreach($pastEntries as $entry)
                        @php
                            $durationSeconds = (int) $entry->received_at->diffInRealSeconds($entry->released_at);
                            $dh = floor($durationSeconds / 3600);
                            $dm = floor(($durationSeconds % 3600) / 60);
                            $ds = $durationSeconds % 60;
                            $pastLabel = $dh > 0 ? $dh . 'h ' . $dm . 'm ' . $ds . 's' : ($dm > 0 ? $dm . 'm ' . $ds . 's' : $ds . 's');
                        @endphp
                        <div class="relative pl-14 sm:pl-16">
                            <div class="absolute left-[13px] sm:left-[15px] top-3 w-4 h-4 rounded-full border-2 bg-white border-gray-300"></div>
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 sm:p-4">
                                <div class="flex items-start gap-3">
                                    @if($entry->user->profile_picture_url)
                                        <img src="{{ $entry->user->profile_picture_url }}" alt="" class="h-8 w-8 sm:h-10 sm:w-10 rounded-full object-cover shrink-0"  style="-webkit-user-drag: none; user-select: none;">
                                    @else
                                        <div class="h-8 w-8 sm:h-10 sm:w-10 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-500 shrink-0">
                                            {{ $entry->user->initials }}
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $entry->user->full_name ?? $entry->user->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $entry->user->office->name ?? $entry->user->department->name ?? '' }}</p>
                                            </div>
                                            <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">Finished</span>
                                        </div>
                                        <div class="mt-2 text-xs text-gray-500 space-y-1">
                                            <p><span class="text-gray-400">Date:</span> {{ $entry->received_at->format('M d, Y') }}</p>
                                            <p><span class="text-gray-400">Time:</span> {{ $entry->received_at->format('h:i A') }}</p>
                                            <p><span class="text-gray-400">Released:</span> {{ $entry->released_at->format('M d, Y') }} at {{ $entry->released_at->format('h:i A') }}</p>
                                            <p><span class="text-gray-400">Office:</span> {{ $entry->user->office->name ?? $entry->user->department->name ?? 'N/A' }}</p>
                                            <p class="text-gray-500"><span class="text-gray-400">Duration:</span> {{ $pastLabel }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach

                        @if($document->status === 'finished')
                        <div class="relative pl-14 sm:pl-16">
                            <div class="absolute left-[13px] sm:left-[15px] top-3 w-4 h-4 rounded-full border-[3px] bg-gray-400 border-gray-500"></div>
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-3 sm:p-4">
                                <div class="flex items-start gap-3">
                                    <div class="h-8 w-8 sm:h-10 sm:w-10 rounded-full bg-gray-100 flex items-center justify-center shrink-0 border border-gray-300">
                                        <svg class="h-4 w-4 sm:h-5 sm:w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2">
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">Document Finished</p>
                                            </div>
                                            <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-200 text-gray-800">Finished</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        @if($document->status === 'terminated')
                        <div class="relative pl-14 sm:pl-16">
                            <div class="absolute left-[13px] sm:left-[15px] top-3 w-4 h-4 rounded-full border-[3px] bg-red-400 border-red-500"></div>
                            <div class="bg-red-50 border border-red-200 rounded-lg p-3 sm:p-4">
                                <div class="flex items-start gap-3">
                                    <div class="h-8 w-8 sm:h-10 sm:w-10 rounded-full bg-red-100 flex items-center justify-center shrink-0 border border-red-300">
                                        <svg class="h-4 w-4 sm:h-5 sm:w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2">
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">Document Terminated</p>
                                            </div>
                                            <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-200 text-red-800">Terminated</span>
                                        </div>
                                        @if($document->termination_reason)
                                        <div class="mt-2 text-xs text-gray-500 space-y-1">
                                            <p><span class="text-gray-400">Reason:</span> <span class="text-gray-700">{{ $document->termination_reason }}</span></p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="relative pl-14 sm:pl-16">
                            <div class="absolute left-[13px] sm:left-[15px] top-3 w-4 h-4 rounded-full border-[3px] bg-emerald-400 border-emerald-500"></div>
                            <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-3 sm:p-4">
                                <div class="flex items-start gap-3">
                                    <div class="h-8 w-8 sm:h-10 sm:w-10 rounded-full bg-emerald-100 flex items-center justify-center shrink-0 border border-emerald-300">
                                        <svg class="h-4 w-4 sm:h-5 sm:w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-2">
                                            <div>
                                                <p class="text-sm font-semibold text-gray-900">Document Created</p>
                                                <p class="text-xs text-gray-500">{{ $document->creator->full_name ?? $document->creator->name }}</p>
                                            </div>
                                            <span class="shrink-0 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-200 text-emerald-800">Created</span>
                                        </div>
                                        <div class="mt-2 text-xs text-gray-500 space-y-1">
                                            <p><span class="text-gray-400">Date:</span> {{ $document->created_at->format('M d, Y') }}</p>
                                            <p><span class="text-gray-400">Time:</span> {{ $document->created_at->format('h:i A') }}</p>
                                            <p><span class="text-gray-400">Office:</span> {{ $document->creator->office->name ?? $document->creator->department->name ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(in_array($document->status, ['finished', 'terminated']) && auth()->user()->hasPermission('documents.reopen'))
    <div class="bg-white rounded-xl border border-amber-200 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-amber-200 bg-amber-50">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5 text-amber-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" /></svg>
                <h2 class="text-lg font-semibold text-amber-800">Reopen Document</h2>
            </div>
        </div>
        <div class="px-4 sm:px-6 py-5">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-gray-900">This document is {{ $document->status === 'terminated' ? 'terminated' : 'finished' }}.</p>
                    <p class="text-xs text-gray-500">Reopening will make it available for receiving again.</p>
                </div>
                <button type="button" data-action="reopen" data-id="{{ $document->id }}"
                    class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-white bg-amber-600 rounded-lg hover:bg-amber-700 transition-colors">
                    Reopen Document
                </button>
            </div>
        </div>
    </div>
    @endif

    @if($document->status === 'terminated' && $document->termination_reason)
    <div class="bg-white rounded-xl border border-red-200 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-red-200 bg-red-50">
            <div class="flex items-center gap-2">
                <svg class="h-5 w-5 text-red-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                <h2 class="text-lg font-semibold text-red-800">Terminated</h2>
            </div>
        </div>
        <div class="px-4 sm:px-6 py-5">
            <p class="text-sm text-gray-500">Termination Reason:</p>
            <p class="text-sm text-gray-900 mt-1">{{ $document->termination_reason }}</p>
        </div>
    </div>
    @endif

    @if(auth()->user()->hasPermission('documents.delete') && $timeline->isEmpty())
    <div class="bg-white rounded-xl border border-red-200 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-red-200 bg-red-50">
            <h2 class="text-lg font-semibold text-red-800">Danger Zone</h2>
        </div>
        <div class="px-4 sm:px-6 py-5">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-gray-900">Delete this document</p>
                    <p class="text-xs text-gray-500">Once deleted, this cannot be undone.</p>
                </div>
                <form method="POST" action="{{ route('system.documents.destroy', $document->id) }}" data-confirm="Are you sure you want to permanently delete this document? This cannot be undone.">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full sm:w-auto px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">Delete</button>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
function copyToClipboard(btn, text) {
    var ta = document.createElement('textarea');
    ta.value = text;
    ta.style.position = 'fixed';
    ta.style.left = '-9999px';
    ta.style.top = '-9999px';
    document.body.appendChild(ta);
    ta.select();
    ta.setSelectionRange(0, 99999);
    try {
        document.execCommand('copy');
        var orig = btn.innerHTML;
        btn.innerHTML = '<svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg> Copied!';
        btn.classList.remove('bg-gray-100', 'hover:bg-gray-200', 'text-gray-600');
        btn.classList.add('bg-emerald-100', 'text-emerald-700');
        setTimeout(function () {
            btn.innerHTML = orig;
            btn.classList.remove('bg-emerald-100', 'text-emerald-700');
            btn.classList.add('bg-gray-100', 'hover:bg-gray-200', 'text-gray-600');
        }, 2000);
    } catch (e) {
        alert('Failed to copy');
    }
    document.body.removeChild(ta);
}

function reopenDocument(id) {
    if (!confirm('Reopen this document? It will be available for receiving again.')) return;
    fetch('/system/documents/' + id + '/reopen', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' }
    }).then(function(r) { return r.json(); }).then(function(d) {
        if (d.error) { alert(d.error); return; }
        alert(d.message);
        location.reload();
    }).catch(function() { alert('An error occurred.'); });
}

document.querySelectorAll('.live-timer').forEach(function(el) {
    var receivedAt = new Date(el.getAttribute('data-received'));
    function update() {
        var now = new Date();
        var diff = Math.floor((now - receivedAt) / 1000);
        if (diff < 0) diff = 0;
        var h = Math.floor(diff / 3600);
        var m = Math.floor((diff % 3600) / 60);
        var s = diff % 60;
        var label = '';
        if (h > 0) label += h + 'h ';
        if (m > 0 || h > 0) label += m + 'm ';
        label += s + 's';
        el.textContent = label;
    }
    update();
    setInterval(update, 1000);
});
</script>
@endpush
@endsection
