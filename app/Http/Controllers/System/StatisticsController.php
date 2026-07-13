<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\DocumentTrack;
use App\Models\Office;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StatisticsController extends Controller
{
    public function index(): View
    {
        abort_unless(auth()->user()->hasPermission('statistics.access'), 403);

        $overview = $this->getOverview();
        $perOffice = $this->getPerOfficeStats();
        $documentTypeStats = $this->getDocumentTypeStats();
        $statusDistribution = $this->getStatusDistribution();
        $artaCategoryStats = $this->getArtaCategoryStats();
        $monthlyTrends = $this->getMonthlyTrends();
        $processingStats = $this->getProcessingStats();

        $chartData = $this->buildChartJson(
            $statusDistribution,
            $documentTypeStats,
            $artaCategoryStats,
            $monthlyTrends,
            $perOffice,
        );

        return view('system.statistics', compact(
            'overview',
            'perOffice',
            'documentTypeStats',
            'statusDistribution',
            'artaCategoryStats',
            'monthlyTrends',
            'processingStats',
            'chartData',
        ));
    }

    private function getOverview(): array
    {
        return [
            'totalDocuments' => Document::count(),
            'totalTracks' => DocumentTrack::count(),
            'activeDocuments' => Document::whereNull('status')->orWhere('status', 'active')->count(),
            'finishedDocuments' => Document::where('status', 'finished')->count(),
            'terminatedDocuments' => Document::where('status', 'terminated')->count(),
            'unclaimedDocuments' => Document::whereDoesntHave('tracks')->count(),
            'currentlyHeld' => DocumentTrack::whereNull('released_at')->count(),
        ];
    }

    private function getPerOfficeStats(): array
    {
        $users = User::with('office')->whereNotNull('office_id')->get()->groupBy('office_id');
        $officeIds = $users->keys();
        $offices = Office::whereIn('id', $officeIds)->get()->keyBy('id');

        $stats = [];

        foreach ($users as $officeId => $officeUsers) {
            $office = $offices->get($officeId);
            if (! $office) {
                continue;
            }

            $userIds = $officeUsers->pluck('id');

            $handledTracks = DocumentTrack::whereIn('user_id', $userIds)
                ->whereNotNull('received_at')
                ->whereNotNull('released_at')
                ->get(['received_at', 'released_at']);

            $totalHandled = $handledTracks->count();
            $minutes = $handledTracks->map(fn ($t) => (int) $t->received_at->diffInRealMinutes($t->released_at))->toArray();

            $avgMinutes = $totalHandled > 0 ? round(array_sum($minutes) / $totalHandled, 1) : 0;
            $medianMinutes = $totalHandled > 0 ? $this->median($minutes) : 0;
            $minMinutes = $totalHandled > 0 ? min($minutes) : 0;
            $maxMinutes = $totalHandled > 0 ? max($minutes) : 0;

            $currentlyHeld = DocumentTrack::whereNull('released_at')
                ->whereIn('user_id', $userIds)
                ->count();

            $stats[] = [
                'office' => $office,
                'total_handled' => $totalHandled,
                'avg_minutes' => $avgMinutes,
                'avg_label' => $this->minutesToLabel($avgMinutes),
                'median_minutes' => $medianMinutes,
                'median_label' => $this->minutesToLabel($medianMinutes),
                'min_minutes' => $minMinutes,
                'min_label' => $this->minutesToLabel($minMinutes),
                'max_minutes' => $maxMinutes,
                'max_label' => $this->minutesToLabel($maxMinutes),
                'currently_held' => $currentlyHeld,
            ];
        }

        return $stats;
    }

    private function getDocumentTypeStats(): array
    {
        $raw = Document::select('document_type', DB::raw('COUNT(*) as count'))
            ->groupBy('document_type')
            ->orderByDesc('count')
            ->get();

        return $raw->map(fn ($r) => [
            'label' => $r->document_type,
            'count' => (int) $r->count,
        ])->toArray();
    }

    private function getStatusDistribution(): array
    {
        $raw = Document::select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        $labels = [
            null => 'Active',
            'active' => 'Active',
            'finished' => 'Finished',
            'terminated' => 'Terminated',
        ];

        $result = [];
        $seenLabels = [];

        foreach ($raw as $r) {
            $label = $labels[$r->status] ?? ucfirst($r->status ?? 'active');
            if (isset($seenLabels[$label])) {
                $idx = $seenLabels[$label];
                $result[$idx]['count'] += (int) $r->count;
            } else {
                $seenLabels[$label] = count($result);
                $result[] = ['label' => $label, 'count' => (int) $r->count];
            }
        }

        if (empty($result)) {
            $result[] = ['label' => 'Active', 'count' => 0];
        }

        return $result;
    }

    private function getArtaCategoryStats(): array
    {
        $raw = Document::select('arta_category', DB::raw('COUNT(*) as count'))
            ->groupBy('arta_category')
            ->orderByDesc('count')
            ->get();

        return $raw->map(fn ($r) => [
            'label' => $r->arta_category ? ucfirst(str_replace('_', ' ', $r->arta_category)) : 'Uncategorized',
            'count' => (int) $r->count,
        ])->toArray();
    }

    private function getMonthlyTrends(): array
    {
        $raw = Document::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as count")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return $raw->map(fn ($r) => [
            'month' => $r->month,
            'count' => (int) $r->count,
        ])->toArray();
    }

    private function getProcessingStats(): array
    {
        $allTracks = DocumentTrack::whereNotNull('received_at')
            ->whereNotNull('released_at')
            ->get(['received_at', 'released_at']);

        $minutes = $allTracks->map(fn ($t) => (int) $t->received_at->diffInRealMinutes($t->released_at))->toArray();
        $count = count($minutes);

        if ($count === 0) {
            return [
                'total_processed' => 0,
                'mean_label' => '0m',
                'median_label' => '0m',
                'min_label' => '0m',
                'max_label' => '0m',
            ];
        }

        return [
            'total_processed' => $count,
            'mean_label' => $this->minutesToLabel(round(array_sum($minutes) / $count, 1)),
            'median_label' => $this->minutesToLabel($this->median($minutes)),
            'min_label' => $this->minutesToLabel(min($minutes)),
            'max_label' => $this->minutesToLabel(max($minutes)),
        ];
    }

    private function buildChartJson(
        array $statusDistribution,
        array $documentTypeStats,
        array $artaCategoryStats,
        array $monthlyTrends,
        array $perOffice,
    ): string {
        $data = [];

        if (! empty($statusDistribution)) {
            $data['statusLabels'] = array_column($statusDistribution, 'label');
            $data['statusValues'] = array_column($statusDistribution, 'count');
        }

        if (! empty($documentTypeStats)) {
            $data['typeLabels'] = array_column($documentTypeStats, 'label');
            $data['typeValues'] = array_column($documentTypeStats, 'count');
        }

        if (! empty($artaCategoryStats)) {
            $data['artaLabels'] = array_column($artaCategoryStats, 'label');
            $data['artaValues'] = array_column($artaCategoryStats, 'count');
        }

        if (! empty($monthlyTrends)) {
            $data['trendLabels'] = array_column($monthlyTrends, 'month');
            $data['trendValues'] = array_column($monthlyTrends, 'count');
        }

        if (! empty($perOffice)) {
            $data['officeLabels'] = array_map(fn ($s) => $s['office']->name, $perOffice);
            $data['officeHandled'] = array_map(fn ($s) => $s['total_handled'], $perOffice);
            $data['officeAvgHours'] = array_map(fn ($s) => round($s['avg_minutes'] / 60, 1), $perOffice);
        }

        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function median(array $numbers): float
    {
        sort($numbers);
        $count = count($numbers);
        $mid = floor($count / 2);

        if ($count % 2 === 0) {
            return round(($numbers[$mid - 1] + $numbers[$mid]) / 2, 1);
        }

        return round($numbers[$mid], 1);
    }

    private function minutesToLabel(float $minutes): string
    {
        if ($minutes <= 0) {
            return '0m';
        }
        $hours = floor($minutes / 60);
        $mins = round($minutes % 60);

        if ($hours > 0) {
            return $hours.'h '.$mins.'m';
        }

        return $mins.'m';
    }
}
