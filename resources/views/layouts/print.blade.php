<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - {{ config('app.name', 'Document Tracker') }}</title>
    @stack('styles')
</head>
<body>
    @yield('content')

    @if(auth()->user()->hasPermission('documents.view'))
    <div style="text-align: center; padding: 10px; background: #fff; border-top: 1px solid #ddd; position: fixed; bottom: 0; left: 0; right: 0; z-index: 1000;" class="no-print">
        <button id="printBtn" style="padding: 10px 24px; font-size: 14px; background: #4f46e5; color: #fff; border: none; border-radius: 8px; cursor: pointer;">Print Document</button>
        <a href="{{ route('system.documents.view', $document->id) }}" style="padding: 10px 24px; font-size: 14px; background: #fff; color: #333; border: 1px solid #ccc; border-radius: 8px; text-decoration: none; margin-left: 8px;">Back to Document</a>
    </div>
    @endif

    @stack('scripts')

    <script nonce="{{ $cspNonce ?? '' }}">
    document.addEventListener('DOMContentLoaded', function () {
        var btn = document.getElementById('printBtn');
        if (btn) {
            btn.addEventListener('click', function () {
                window.print();
            });
        }
    });
    </script>

    <style>
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</body>
</html>
