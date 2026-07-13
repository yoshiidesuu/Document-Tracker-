@php
    $title = 'Statistics';
    $breadcrumbs = [
        ['label' => 'System', 'url' => route('system.dashboard')],
        ['label' => 'Statistics'],
    ];
@endphp

@extends('layouts.system')
@section('content')
<div class="space-y-6">

    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-7 gap-2">
        <div class="bg-white rounded-lg border border-gray-200 p-3 text-center">
            <p class="text-xl font-bold text-gray-900">{{ $overview['totalDocuments'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Total Documents</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-3 text-center">
            <p class="text-xl font-bold text-emerald-600">{{ $overview['activeDocuments'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Active</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-3 text-center">
            <p class="text-xl font-bold text-blue-600">{{ $overview['currentlyHeld'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Held</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-3 text-center">
            <p class="text-xl font-bold text-gray-600">{{ $overview['finishedDocuments'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Finished</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-3 text-center">
            <p class="text-xl font-bold text-red-600">{{ $overview['terminatedDocuments'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Terminated</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-3 text-center">
            <p class="text-xl font-bold text-indigo-600">{{ $overview['unclaimedDocuments'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Unclaimed</p>
        </div>
        <div class="bg-white rounded-lg border border-gray-200 p-3 text-center">
            <p class="text-xl font-bold text-amber-600">{{ $overview['totalTracks'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Tracks</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200">
                <h2 class="text-base font-semibold text-gray-900">Overall Processing Time</h2>
            </div>
            <div class="px-5 py-4">
                @if($processingStats['total_processed'] > 0)
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-4 text-center">
                    <div>
                        <p class="text-lg font-bold text-gray-900">{{ $processingStats['total_processed'] }}</p>
                        <p class="text-xs text-gray-500">Completed</p>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-indigo-600">{{ $processingStats['mean_label'] }}</p>
                        <p class="text-xs text-gray-500">Mean</p>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-emerald-600">{{ $processingStats['median_label'] }}</p>
                        <p class="text-xs text-gray-500">Median</p>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-blue-600">{{ $processingStats['min_label'] }}</p>
                        <p class="text-xs text-gray-500">Fastest</p>
                    </div>
                    <div>
                        <p class="text-lg font-bold text-red-600">{{ $processingStats['max_label'] }}</p>
                        <p class="text-xs text-gray-500">Slowest</p>
                    </div>
                </div>
                @else
                <p class="text-sm text-gray-500 text-center py-4">No completed tracks yet.</p>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200">
                <h2 class="text-base font-semibold text-gray-900">Document Status</h2>
            </div>
            <div class="px-4 py-2">
                <canvas id="statusChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200">
                <h2 class="text-base font-semibold text-gray-900">Documents by Type</h2>
            </div>
            <div class="px-4 py-3">
                @if(!empty($documentTypeStats))
                <canvas id="typeChart" height="120"></canvas>
                @else
                <p class="text-sm text-gray-500 text-center py-6">No documents yet.</p>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-200">
                <h2 class="text-base font-semibold text-gray-900">Documents by ARTA Category</h2>
            </div>
            <div class="px-4 py-3">
                @if(!empty($artaCategoryStats))
                <canvas id="artaChart" height="120"></canvas>
                @else
                <p class="text-sm text-gray-500 text-center py-6">No documents yet.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200">
            <h2 class="text-base font-semibold text-gray-900">Monthly Trend</h2>
        </div>
        <div class="px-4 py-3">
            @if(!empty($monthlyTrends))
            <canvas id="trendChart" height="120"></canvas>
            @else
            <p class="text-sm text-gray-500 text-center py-6">No monthly data yet.</p>
            @endif
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200">
            <h2 class="text-base font-semibold text-gray-900">Office Performance</h2>
        </div>
        <div class="px-4 py-3">
            @if(!empty($perOffice))
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-200 text-left text-xs text-gray-500 uppercase tracking-wider">
                            <th class="py-2 pr-4 font-medium">Office</th>
                            <th class="py-2 px-4 font-medium text-right">Handled</th>
                            <th class="py-2 px-4 font-medium text-right">Held</th>
                            <th class="py-2 px-4 font-medium text-right">Mean</th>
                            <th class="py-2 px-4 font-medium text-right">Median</th>
                            <th class="py-2 px-4 font-medium text-right">Fastest</th>
                            <th class="py-2 px-4 font-medium text-right">Slowest</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($perOffice as $stat)
                        <tr class="hover:bg-gray-50">
                            <td class="py-2.5 pr-4 font-medium text-gray-900">{{ $stat['office']->name }}</td>
                            <td class="py-2.5 px-4 text-right text-gray-700">{{ $stat['total_handled'] }}</td>
                            <td class="py-2.5 px-4 text-right">
                                @if($stat['currently_held'] > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-100 text-amber-800">{{ $stat['currently_held'] }}</span>
                                @else
                                <span class="text-gray-400">0</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-4 text-right text-gray-700">{{ $stat['avg_label'] }}</td>
                            <td class="py-2.5 px-4 text-right font-medium text-gray-900">{{ $stat['median_label'] }}</td>
                            <td class="py-2.5 px-4 text-right text-emerald-600">{{ $stat['min_label'] }}</td>
                            <td class="py-2.5 px-4 text-right text-red-600">{{ $stat['max_label'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                <canvas id="officeChart" height="140"></canvas>
            </div>
            @else
            <p class="text-sm text-gray-500 text-center py-8">No office data available. Assign users to offices to see performance.</p>
            @endif
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script id="stats-data" type="application/json">{!! $chartData !!}</script>
@vite(['resources/js/statistics.js'])
@endpush
