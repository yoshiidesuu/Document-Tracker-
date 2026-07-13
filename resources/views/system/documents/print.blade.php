@extends('layouts.print')

@section('title', $document->title)

@section('content')
<div class="page">
    <div class="watermark">AUTHENTIC</div>
    <div class="header">
        <div class="h-left">
            @php $siteLogo = \App\Models\SystemSetting::get('site_logo'); @endphp
            @if ($siteLogo)
                <img src="{{ route('file.logo') }}" alt="" class="logo"  style="-webkit-user-drag: none; user-select: none;">
            @endif
        </div>
        <div class="h-center">
            <div class="h-title">{{ $settings['header_title'] }}</div>
            @if(!empty($settings['addresses']))
                <div class="h-address">
                    @foreach($settings['addresses'] as $addr)
                        {{ $addr }}@if(!$loop->last)<br>@endif
                    @endforeach
                </div>
            @endif
            <div class="h-contact">
                @if(!empty($settings['emails']))
                    <span>{{ implode(', ', $settings['emails']) }}</span>
                @endif
                @if(!empty($settings['emails']) && !empty($settings['contacts']))
                    <span class="h-sep"> | </span>
                @endif
                @if(!empty($settings['contacts']))
                    <span>{{ implode(', ', $settings['contacts']) }}</span>
                @endif
            </div>
        </div>
        <div class="h-right">
            @php $rightLogo = \App\Models\SystemSetting::get('document_right_logo'); @endphp
            @if ($rightLogo)
                <img src="{{ route('file.document-logo-right') }}" alt="" class="logo"  style="-webkit-user-drag: none; user-select: none;">
            @endif
        </div>
    </div>
    <div class="divider"></div>

    <div class="body">
        <div class="col-left">
            <div class="qr-wrap">
                <div class="qr-label">Scan QR</div>
                <img src="{{ $qrDataUrl }}" alt="QR" class="qr-img"  style="-webkit-user-drag: none; user-select: none;">
            </div>
            <div class="barcode-wrap">
                <div class="barcode-card">
                    <img src="{{ $barcodeDataUrl }}" alt="Barcode" class="barcode-img"  style="-webkit-user-drag: none; user-select: none;">
                    <div class="barcode-label">
                        <span class="barcode-title">Document Type</span>
                        <span class="barcode-value">{{ $document->document_type }}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-right">
            @php
                $departmentName = $document->creator?->department?->name;
                $officeName = $document->creator?->office?->name;
                $departmentOffice = $departmentName && $officeName
                    ? $departmentName . ' / ' . $officeName
                    : ($departmentName ?: ($officeName ?: '—'));
                $accessLabel = $document->is_private ? 'Private' : 'Public';
            @endphp
            <div class="details-title">Document Details</div>
            <div class="field field-title">
                <span class="label">Document Title:</span>
                <span class="value">{!! $document->title !!}</span>
            </div>
            <div class="field">
                <span class="label">Owner:</span>
                <span class="value">{{ $document->creator->full_name ?? $document->creator->name }}</span>
            </div>
            <div class="field">
                <span class="label">Department / Office:</span>
                <span class="value">{{ $departmentOffice }}</span>
            </div>
            <div class="field">
                <span class="label">Created When:</span>
                <span class="value">{{ $document->created_at->format('M d, Y') }}</span>
            </div>
            <div class="field">
                <span class="label">Access:</span>
                <span class="value">{{ $accessLabel }}</span>
            </div>
            <div class="field">
                <span class="label">Processing Time (ARTA):</span>
                <span class="value">{{ $document->arta_duration_label }}</span>
            </div>
        </div>
    </div>

    <div class="footer">
        Generated {{ $document->created_at->format('M d, Y \a\t h:i A') }}
    </div>
</div>
@endsection

@push('styles')
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'Palatino Linotype','Book Antiqua',Palatino,serif;color:#111;background:#fff;font-size:10pt}
.page{width:148mm;min-height:210mm;padding:6mm 7mm;margin:0 auto;background:#fff;display:flex;flex-direction:column;position:relative}
.watermark{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%) rotate(-30deg);font-size:60pt;font-weight:bold;color:#000;opacity:0.04;letter-spacing:8pt;white-space:nowrap;pointer-events:none;z-index:0;font-family:Arial,sans-serif}
.header{display:flex;align-items:center;gap:3mm;position:relative;z-index:1}
.h-left,.h-right{width:20mm;flex-shrink:0;display:flex;justify-content:center;align-items:center}
.logo{max-width:18mm;max-height:18mm;object-fit:contain}
.h-center{flex:1;text-align:center}
.h-title{font-size:12.5pt;font-weight:bold;text-transform:uppercase;letter-spacing:0.6pt;line-height:1.2}
.h-address{font-size:8pt;margin-top:0.7mm;line-height:1.4;color:#333}
.h-contact{font-size:8pt;margin-top:0.7mm;line-height:1.4;color:#333}
.h-sep{color:#999}
.divider{margin-top:2.5mm;border-top:1.5px solid #111}

.body{flex:1;padding-top:4mm;display:flex;gap:6mm;align-items:flex-start}
.col-left{width:45%;display:flex;flex-direction:column;align-items:flex-start;justify-content:flex-start;padding-right:3mm;border-right:1px solid #e1e1e1}
.col-right{width:55%;display:flex;flex-direction:column;justify-content:flex-start;padding-left:4mm;padding-top:1mm}

.qr-wrap{width:100%;text-align:center;border:1px solid #d7d7d7;border-radius:2mm;padding:3mm;background:#f7f7f7;display:flex;flex-direction:column;align-items:center}
.qr-label{font-size:7pt;text-transform:uppercase;letter-spacing:0.4pt;color:#666;margin-bottom:2mm}
.qr-img{width:42mm;height:42mm;image-rendering:pixelated;border:1px solid #bdbdbd;padding:1.5mm;background:#fff}

.barcode-wrap{text-align:left;margin-top:6mm;width:100%}
.barcode-card{border:1px dashed #c6c6c6;border-radius:2mm;padding:2.5mm;background:#fafafa;display:flex;flex-direction:column;align-items:center;text-align:center}
.barcode-img{width:54mm;height:18mm;object-fit:contain;display:block;margin:0}
.barcode-label{font-size:7pt;color:#222;margin-top:1mm;letter-spacing:0.2pt;display:flex;flex-direction:column;gap:0.5mm;align-items:center}
.barcode-title{font-weight:bold;text-transform:uppercase;font-size:6.5pt;color:#444;letter-spacing:0.4pt}
.barcode-value{font-size:9pt;color:#000;font-weight:bold}

.details-title{font-size:8pt;text-transform:uppercase;letter-spacing:0.6pt;color:#666;margin-bottom:3mm}
.field{padding:2.2mm 0;line-height:1.4;border-bottom:1px solid #e6e6e6}
.field:last-child{border-bottom:0}
.label{font-weight:bold;display:block;color:#444;font-size:8pt;text-transform:uppercase;letter-spacing:0.4pt}
.value{color:#111;font-size:10.5pt;margin-top:0.8mm;display:block}
.field-title .value{font-size:12.5pt;font-weight:bold;letter-spacing:0.2pt;line-height:1.2;overflow-wrap:anywhere;word-break:break-word;hyphens:auto}

.footer{border-top:1px solid #bdbdbd;padding-top:2mm;margin-top:3mm;font-size:7pt;text-align:center;color:#666;font-style:italic}

@media print{
html,body{width:148mm;height:210mm}
.page{width:148mm;min-height:210mm;padding:6mm 7mm;margin:0}
@page{size:148mm 210mm;margin:0}
}

@media screen{
body{padding:10mm 0;background:#e5e7eb}
.page{box-shadow:0 8px 18px rgba(0,0,0,0.12);margin:0 auto;border:1px solid #d1d5db}
}
</style>
@endpush
