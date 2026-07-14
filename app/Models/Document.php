<?php

namespace App\Models;

use Database\Factories\DocumentFactory;
use Endroid\QrCode\Builder\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Picqer\Barcode\BarcodeGeneratorPNG;

class Document extends Model
{
    use HasFactory;

    protected static function newFactory(): DocumentFactory
    {
        return DocumentFactory::new();
    }

    protected $fillable = [
        'title',
        'document_type',
        'document_type_id',
        'creator_id',
        'processing_hours',
        'qr_value',
        'barcode_value',
        'is_private',
        'access_key',
        'arta_setting_id',
        'arta_category',
        'notes',
        'status',
        'termination_reason',
    ];

    protected function casts(): array
    {
        return [
            'is_private' => 'boolean',
            'arta_setting_id' => 'integer',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function artaSetting()
    {
        return $this->belongsTo(ArtaSetting::class);
    }

    public function tracks()
    {
        return $this->hasMany(DocumentTrack::class);
    }

    public function currentHolder()
    {
        return $this->hasOne(DocumentTrack::class)->whereNull('released_at')->latest();
    }

    public function pastHolders()
    {
        return $this->hasMany(DocumentTrack::class)
            ->whereNotNull('released_at')
            ->orderByDesc('received_at');
    }

    public function getQrCodeUrl(): string
    {
        $result = (new Builder)->build(
            data: $this->qr_value,
            size: 220,
            margin: 10
        );

        return 'data:image/png;base64,'.base64_encode($result->getString());
    }

    public function getBarcodeUrl(): string
    {
        $generator = new BarcodeGeneratorPNG;

        return 'data:image/png;base64,'.base64_encode(
            $generator->getBarcode($this->barcode_value, $generator::TYPE_CODE_128, 4, 120)
        );
    }

    public function getArtaProcessingDaysAttribute(): int
    {
        if ($this->artaSetting && $this->artaSetting->days !== null) {
            return (int) $this->artaSetting->days;
        }

        return match ($this->arta_category) {
            'complex' => 7,
            'highly_technical' => 20,
            default => 3,
        };
    }

    public function getArtaDurationLabelAttribute(): string
    {
        if ($this->artaSetting) {
            return $this->artaSetting->duration_label;
        }

        $days = $this->arta_processing_days;

        return $days ? $days.' day'.($days > 1 ? 's' : '') : '-';
    }
}
