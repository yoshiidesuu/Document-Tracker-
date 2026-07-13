<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\ArtaSetting;
use App\Models\Document;
use App\Models\DocumentTrack;
use App\Models\DocumentType;
use App\Services\UserActivityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function __construct(
        private readonly UserActivityService $userActivity,
    ) {}

    public function index(Request $request): View
    {
        abort_unless(auth()->user()->hasPermission('documents.list'), 403);

        $query = Document::with('creator');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('document_type', 'like', "%{$search}%");
            });
        }

        if ($type = $request->input('type')) {
            $query->where('document_type', $type);
        }

        $documents = $query->orderByDesc('created_at')->get();
        $documentTypes = DocumentType::where('is_active', true)->orderBy('name')->get();

        return view('system.documents.index', compact('documents', 'documentTypes'));
    }

    public function create(): View
    {
        abort_unless(auth()->user()->hasPermission('documents.create'), 403);

        $artaSettings = ArtaSetting::where('is_active', true)
            ->orderBy('category')
            ->orderBy('title')
            ->get();

        $documentTypes = DocumentType::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('system.documents.create', compact('artaSettings', 'documentTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('documents.create'), 403);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'document_type' => ['required', 'string', 'max:255'],
            'processing_hours' => ['required', 'numeric', 'min:0', 'max:99999'],
            'arta_setting_id' => ['required', 'integer', Rule::exists('arta_settings', 'id')->where('is_active', true)],
            'is_private' => ['nullable', 'boolean'],
            'access_key' => ['nullable', 'string', 'max:255', 'required_if:is_private,1'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $data['is_private'] = (bool) ($data['is_private'] ?? false);
        if (!$data['is_private']) {
            $data['access_key'] = null;
        }

        $artaSetting = ArtaSetting::select('id', 'category', 'days', 'hours', 'minutes')
            ->find($data['arta_setting_id']);
        if ($artaSetting) {
            $data['arta_category'] = $artaSetting->category;
            $totalMinutes = ((int) ($artaSetting->days ?? 0) * 24 * 60)
                + ((int) ($artaSetting->hours ?? 0) * 60)
                + (int) ($artaSetting->minutes ?? 0);
            $data['processing_hours'] = round($totalMinutes / 60, 2);
        }

        $data['creator_id'] = auth()->id();
        $data['qr_value'] = (string) \Illuminate\Support\Str::uuid();
        $data['barcode_value'] = strtoupper(bin2hex(random_bytes(8)));

        $document = Document::create($data);

        $this->userActivity->log('document_created', "Document generated: {$document->title}", newData: $document->only(['title', 'document_type', 'qr_value', 'barcode_value']));

        return redirect()->route('system.documents.print', $document->id)
            ->with('success', "Document '{$document->title}' generated successfully.");
    }

    public function view(Document $document): View
    {
        abort_unless(auth()->user()->hasPermission('documents.view'), 403);

        $document->load([
            'creator.department',
            'creator.office',
            'artaSetting',
            'tracks.user.department',
            'tracks.user.office',
        ]);

        $tracks = $document->tracks->sortBy('received_at');
        $currentTrack = $tracks->firstWhere('released_at', null);

        $timeline = $tracks->map(function ($track) {
            $start = $track->received_at;
            $end = $track->released_at ?? now();
            $durationHours = $start->diffInRealHours($end);
            $durationMinutes = $start->diffInRealMinutes($end) % 60;
            return (object) [
                'track' => $track,
                'user' => $track->user,
                'received_at' => $start,
                'released_at' => $track->released_at,
                'is_current' => !$track->released_at,
                'duration_label' => $durationHours > 0
                    ? $durationHours . 'h ' . $durationMinutes . 'm'
                    : $durationMinutes . 'm',
                'duration_minutes' => (int) $start->diffInRealMinutes($end),
            ];
        });

        $officeDurations = collect();
        $tracks->each(function ($track) use ($officeDurations) {
            $user = $track->user;
            $office = $user->office;
            $key = $office ? $office->id : 'none';
            $start = $track->received_at;
            $end = $track->released_at ?? now();
            $mins = (int) $start->diffInRealMinutes($end);

            if ($officeDurations->has($key)) {
                $existing = $officeDurations->get($key);
                $existing->total_minutes += $mins;
                $existing->count++;
            } else {
                $officeDurations->put($key, (object) [
                    'office' => $office,
                    'total_minutes' => $mins,
                    'count' => 1,
                ]);
            }
        });

        $officeDurations = $officeDurations->sortByDesc('total_minutes')->values();

        $userRankings = collect();
        $tracks->each(function ($track) use ($userRankings) {
            $user = $track->user;
            $start = $track->received_at;
            $end = $track->released_at ?? now();
            $mins = (int) $start->diffInRealMinutes($end);

            if ($userRankings->has($user->id)) {
                $existing = $userRankings->get($user->id);
                $existing->total_minutes += $mins;
                $existing->count++;
            } else {
                $userRankings->put($user->id, (object) [
                    'user' => $user,
                    'total_minutes' => $mins,
                    'count' => 1,
                ]);
            }
        });

        $userRankings = $userRankings->sortByDesc('total_minutes')->values();

        return view('system.documents.view', compact(
            'document',
            'tracks',
            'currentTrack',
            'timeline',
            'officeDurations',
            'userRankings',
        ));
    }

    public function edit(Document $document): View
    {
        abort_unless(auth()->user()->hasPermission('documents.edit'), 403);

        if (in_array($document->status, ['finished', 'terminated'])) {
            abort(403, 'This document has been ' . $document->status . ' and cannot be edited.');
        }

        if ($document->tracks()->exists() && !auth()->user()->hasRole('admin')) {
            abort(403, 'This document has already been received and can only be edited by an administrator.');
        }

        $artaSettings = ArtaSetting::where('is_active', true)
            ->orWhere('id', $document->arta_setting_id)
            ->orderBy('category')
            ->orderBy('title')
            ->get();

        $documentTypes = DocumentType::where('is_active', true)
            ->orWhere('name', $document->document_type)
            ->orderBy('name')
            ->get();

        return view('system.documents.edit', compact('document', 'artaSettings', 'documentTypes'));
    }

    public function update(Request $request, Document $document): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('documents.edit'), 403);

        if (in_array($document->status, ['finished', 'terminated'])) {
            abort(403, 'This document has been ' . $document->status . ' and cannot be edited.');
        }

        if ($document->tracks()->exists() && !auth()->user()->hasRole('admin')) {
            abort(403, 'This document has already been received and can only be edited by an administrator.');
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'document_type' => ['required', 'string', 'max:255'],
            'processing_hours' => ['required', 'numeric', 'min:0', 'max:99999'],
            'arta_setting_id' => [
                'required',
                'integer',
                Rule::exists('arta_settings', 'id')->where(function ($query) use ($document) {
                    $query->where('is_active', true)
                        ->orWhere('id', $document->arta_setting_id);
                }),
            ],
            'is_private' => ['nullable', 'boolean'],
            'access_key' => ['nullable', 'string', 'max:255', 'required_if:is_private,1'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $data['is_private'] = (bool) ($data['is_private'] ?? false);
        if (!$data['is_private']) {
            $data['access_key'] = null;
        }

        $artaSetting = ArtaSetting::select('id', 'category', 'days', 'hours', 'minutes')
            ->find($data['arta_setting_id']);
        if ($artaSetting) {
            $data['arta_category'] = $artaSetting->category;
            $totalMinutes = ((int) ($artaSetting->days ?? 0) * 24 * 60)
                + ((int) ($artaSetting->hours ?? 0) * 60)
                + (int) ($artaSetting->minutes ?? 0);
            $data['processing_hours'] = round($totalMinutes / 60, 2);
        }

        $old = $document->only(['title', 'document_type', 'status', 'processing_hours', 'notes']);
        $document->update($data);
        $new = $document->only(['title', 'document_type', 'status', 'processing_hours', 'notes']);

        $this->userActivity->log('document_updated', "Document updated: {$document->title}", oldData: $old, newData: $new);

        return redirect()->route('system.documents.view', $document->id)
            ->with('success', "Document '{$document->title}' updated successfully.");
    }

    public function destroy(Document $document): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermission('documents.delete'), 403);

        $this->userActivity->log('document_deleted', "Document deleted: {$document->title}", oldData: $document->only(['title', 'document_type', 'status', 'qr_value', 'barcode_value']));
        $document->delete();

        return redirect()->route('system.documents.index')
            ->with('success', "Document '{$document->title}' deleted successfully.");
    }

    public function myDocuments(Request $request): View
    {
        abort_unless(auth()->user()->hasPermission('documents.my'), 403);

        $query = Document::with('creator')->where('creator_id', auth()->id());

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('document_type', 'like', "%{$search}%");
            });
        }

        if ($type = $request->input('type')) {
            $query->where('document_type', $type);
        }

        $documents = $query->orderByDesc('created_at')->get();
        $documentTypes = \App\Models\DocumentType::where('is_active', true)->orderBy('name')->get();

        return view('system.documents.my', compact('documents', 'documentTypes'));
    }

    public function myScanned(Request $request): View
    {
        abort_unless(auth()->user()->hasPermission('documents.my-scanned'), 403);

        $documentIds = \App\Models\DocumentTrack::where('user_id', auth()->id())
            ->select('document_id')
            ->distinct()
            ->pluck('document_id');

        $query = Document::with('creator')->whereIn('id', $documentIds);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('document_type', 'like', "%{$search}%");
            });
        }

        if ($type = $request->input('type')) {
            $query->where('document_type', $type);
        }

        $documents = $query->orderByDesc('created_at')->get();
        $documentTypes = \App\Models\DocumentType::where('is_active', true)->orderBy('name')->get();

        return view('system.documents.my-scanned', compact('documents', 'documentTypes'));
    }

    public function print(Document $document): View
    {
        abort_unless(auth()->user()->hasPermission('documents.view'), 403);

        $this->userActivity->log('document_printed', "Document printed: {$document->title}", newData: $document->only(['title', 'document_type', 'qr_value']));

        $document->load('creator.department', 'creator.office');

        $settings = [
            'header_title' => \App\Models\SystemSetting::get('document_header_title', \App\Models\SystemSetting::get('site_long_name', config('app.name', 'Document Tracker'))),
            'emails' => \App\Models\SystemSetting::get('emails', []),
            'contacts' => \App\Models\SystemSetting::get('contacts', []),
            'addresses' => \App\Models\SystemSetting::get('addresses', []),
        ];

        $qrDataUrl = $document->getQrCodeUrl();
        $barcodeDataUrl = $document->getBarcodeUrl();

        return view('system.documents.print', compact('document', 'settings', 'qrDataUrl', 'barcodeDataUrl'));
    }

    public function receiveScanner(): View
    {
        abort_unless(auth()->user()->hasPermission('documents.receive'), 403);

        return view('system.documents.receive');
    }

    public function lookupByCode(Request $request): JsonResponse
    {
        abort_unless(auth()->user()->hasPermission('documents.receive'), 403);

        $request->validate(['code' => ['required', 'string']]);

        $code = trim($request->input('code'));

        $document = Document::where('qr_value', $code)
            ->orWhere('barcode_value', $code)
            ->first();

        if (!$document) {
            return response()->json(['error' => 'Document not found with this code.'], 404);
        }

        if (in_array($document->status, ['finished', 'terminated'])) {
            $label = $document->status === 'terminated' ? 'terminated' : 'finished';
            return response()->json(['error' => "This document has been {$label} and cannot be received."], 403);
        }

        $document->load('creator.department', 'creator.office');

        $currentTrack = DocumentTrack::with('user.department', 'user.office')
            ->where('document_id', $document->id)
            ->whereNull('released_at')
            ->latest()
            ->first();

        $pastTracks = DocumentTrack::with('user.department', 'user.office')
            ->where('document_id', $document->id)
            ->whereNotNull('released_at')
            ->orderByDesc('received_at')
            ->get();

        return response()->json([
            'document' => $document,
            'current_holder' => $currentTrack?->user,
            'current_track' => $currentTrack,
            'past_tracks' => $pastTracks,
            'qr_data_url' => $document->getQrCodeUrl(),
        ]);
    }

    public function receiveDocument(Document $document): JsonResponse
    {
        abort_unless(auth()->user()->hasPermission('documents.receive'), 403);

        if (in_array($document->status, ['finished', 'terminated'])) {
            $label = $document->status === 'terminated' ? 'terminated' : 'finished';
            return response()->json(['error' => "This document has been {$label} and cannot be received."], 403);
        }

        $userId = auth()->id();

        $existing = DocumentTrack::where('document_id', $document->id)
            ->where('user_id', $userId)
            ->whereNull('released_at')
            ->first();

        if ($existing) {
            return response()->json(['error' => 'You are already holding this document.'], 409);
        }

        DocumentTrack::where('document_id', $document->id)
            ->whereNull('released_at')
            ->update(['released_at' => now()]);

        DocumentTrack::create([
            'document_id' => $document->id,
            'user_id' => $userId,
            'received_at' => now(),
        ]);

        $this->userActivity->log('document_received', "Document received: {$document->title}", newData: ['document_title' => $document->title, 'document_id' => $document->id, 'holder' => auth()->user()->full_name]);

        return response()->json(['message' => 'Document received successfully.']);
    }

    public function finishScanner(): View
    {
        abort_unless(auth()->user()->hasPermission('documents.finish'), 403);

        return view('system.documents.finish');
    }

    public function finishDocument(Document $document): JsonResponse
    {
        abort_unless(auth()->user()->hasPermission('documents.finish'), 403);

        if (in_array($document->status, ['finished', 'terminated'])) {
            return response()->json(['error' => 'This document has already been ' . $document->status . '.'], 400);
        }

        DocumentTrack::where('document_id', $document->id)
            ->whereNull('released_at')
            ->update(['released_at' => now()]);

        $old = $document->only(['status']);
        $document->update(['status' => 'finished']);
        $new = $document->only(['status']);

        $this->userActivity->log('document_finished', "Document transaction finished: {$document->title}", oldData: $old, newData: $new);

        return response()->json(['message' => 'Document transaction finished successfully.']);
    }

    public function reopenDocument(Document $document): JsonResponse
    {
        abort_unless(auth()->user()->hasPermission('documents.reopen'), 403);

        if (!in_array($document->status, ['finished', 'terminated'])) {
            return response()->json(['error' => 'Only finished or terminated documents can be reopened.'], 400);
        }

        $old = $document->only(['status', 'termination_reason']);
        $document->update(['status' => null, 'termination_reason' => null]);
        $new = $document->only(['status', 'termination_reason']);

        $this->userActivity->log('document_reopened', "Document reopened: {$document->title}", oldData: $old, newData: $new);

        return response()->json(['message' => 'Document reopened successfully. It can now be received again.']);
    }

    public function terminateScanner(): View
    {
        abort_unless(auth()->user()->hasPermission('documents.terminate'), 403);

        return view('system.documents.terminate');
    }

    public function terminateDocument(Document $document): JsonResponse
    {
        abort_unless(auth()->user()->hasPermission('documents.terminate'), 403);

        if (in_array($document->status, ['finished', 'terminated'])) {
            return response()->json(['error' => 'This document has already been ' . $document->status . '.'], 400);
        }

        $validated = request()->validate(['reason' => ['required', 'string', 'max:5000']]);

        $old = $document->only(['status', 'termination_reason']);
        $document->update(['status' => 'terminated', 'termination_reason' => $validated['reason']]);
        $new = $document->only(['status', 'termination_reason']);

        DocumentTrack::where('document_id', $document->id)
            ->whereNull('released_at')
            ->update(['released_at' => now()]);

        $this->userActivity->log('document_terminated', "Document terminated: {$document->title}", oldData: $old, newData: $new);

        return response()->json(['message' => 'Document terminated successfully.']);
    }
}
