<?php

namespace Tests\Unit;

use App\Models\ArtaSetting;
use App\Models\Document;
use App\Models\DocumentTrack;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentModelTest extends TestCase
{
    use RefreshDatabase;

    // @test
    public function testdocument_fillable_attributes(): void
    {
        $document = new Document;
        $fillable = $document->getFillable();

        $this->assertContains('title', $fillable);
        $this->assertContains('document_type', $fillable);
        $this->assertContains('creator_id', $fillable);
        $this->assertContains('processing_hours', $fillable);
        $this->assertContains('qr_value', $fillable);
        $this->assertContains('barcode_value', $fillable);
        $this->assertContains('is_private', $fillable);
        $this->assertContains('access_key', $fillable);
        $this->assertContains('arta_setting_id', $fillable);
        $this->assertContains('arta_category', $fillable);
        $this->assertContains('notes', $fillable);
        $this->assertContains('status', $fillable);
        $this->assertContains('termination_reason', $fillable);
    }

    // @test
    public function testdocument_casts(): void
    {
        $document = new Document;
        $casts = $document->getCasts();

        $this->assertEquals('boolean', $casts['is_private']);
        $this->assertEquals('integer', $casts['arta_setting_id']);
    }

    // @test
    public function testdocument_creator_relationship(): void
    {
        $user = User::factory()->create();
        $document = Document::factory()->create(['creator_id' => $user->id]);

        $this->assertEquals($user->id, $document->creator->id);
    }

    // @test
    public function testdocument_arta_setting_relationship(): void
    {
        $artaSetting = ArtaSetting::factory()->create();
        $document = Document::factory()->create(['arta_setting_id' => $artaSetting->id]);

        $this->assertEquals($artaSetting->id, $document->artaSetting->id);
    }

    // @test
    public function testdocument_tracks_relationship(): void
    {
        $document = Document::factory()->create();
        $track1 = DocumentTrack::factory()->create(['document_id' => $document->id]);
        $track2 = DocumentTrack::factory()->create(['document_id' => $document->id]);

        $this->assertCount(2, $document->tracks);
    }

    // @test
    public function testdocument_current_holder(): void
    {
        $document = Document::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        DocumentTrack::factory()->create([
            'document_id' => $document->id,
            'user_id' => $user1->id,
            'action' => 'received',
            'received_at' => now()->subDay(),
            'released_at' => now()->subHour(),
        ]);

        $currentTrack = DocumentTrack::factory()->create([
            'document_id' => $document->id,
            'user_id' => $user2->id,
            'action' => 'received',
            'received_at' => now(),
            'released_at' => null,
        ]);

        $currentHolder = $document->currentHolder->first();
        $this->assertNotNull($currentHolder);
        $this->assertEquals($user2->id, $currentHolder->user_id);
    }

    // @test
    public function testdocument_past_holders(): void
    {
        $document = Document::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        DocumentTrack::factory()->create([
            'document_id' => $document->id,
            'user_id' => $user1->id,
            'received_at' => now()->subDays(2),
            'released_at' => now()->subDay(),
        ]);

        DocumentTrack::factory()->create([
            'document_id' => $document->id,
            'user_id' => $user2->id,
            'received_at' => now()->subDay(),
            'released_at' => null,
        ]);

        $pastHolders = $document->pastHolders()->get();
        $this->assertCount(1, $pastHolders);
        $this->assertEquals($user1->id, $pastHolders->first()->user_id);
    }

    // @test
    public function testdocument_generates_qr_code(): void
    {
        $document = Document::factory()->create(['qr_value' => 'TEST-QR-123']);

        $qrUrl = $document->getQrCodeUrl();

        $this->assertStringStartsWith('data:image/png;base64,', $qrUrl);
    }

    // @test
    public function testdocument_generates_barcode(): void
    {
        $document = Document::factory()->create(['barcode_value' => 'TEST-BAR-123']);

        $barcodeUrl = $document->getBarcodeUrl();

        $this->assertStringStartsWith('data:image/png;base64,', $barcodeUrl);
    }

    // @test
    public function testdocument_arta_processing_days_from_setting(): void
    {
        $artaSetting = ArtaSetting::factory()->create(['category' => 'simple', 'days' => 3]);
        $document = Document::factory()->create([
            'arta_setting_id' => $artaSetting->id,
            'arta_category' => 'simple',
        ]);

        $this->assertEquals(3, $document->arta_processing_days);
    }

    // @test
    public function testdocument_arta_processing_days_defaults(): void
    {
        $simple = Document::factory()->create(['arta_category' => 'simple', 'arta_setting_id' => null]);
        $complex = Document::factory()->create(['arta_category' => 'complex', 'arta_setting_id' => null]);
        $technical = Document::factory()->create(['arta_category' => 'highly_technical', 'arta_setting_id' => null]);
        $default = Document::factory()->create(['arta_category' => 'unknown', 'arta_setting_id' => null]);

        $this->assertEquals(3, $simple->arta_processing_days);
        $this->assertEquals(7, $complex->arta_processing_days);
        $this->assertEquals(20, $technical->arta_processing_days);
        $this->assertEquals(3, $default->arta_processing_days);
    }

    // @test
    public function testdocument_arta_duration_label(): void
    {
        $artaSetting = ArtaSetting::factory()->create(['duration_label' => '3 days']);
        $document = Document::factory()->create(['arta_setting_id' => $artaSetting->id]);

        $this->assertEquals('3 days', $document->arta_duration_label);
    }

    // @test
    public function testdocument_arta_duration_label_without_setting(): void
    {
        $document = Document::factory()->create([
            'arta_category' => 'complex',
            'arta_setting_id' => null,
        ]);

        $this->assertEquals('7 days', $document->arta_duration_label);
    }
}
