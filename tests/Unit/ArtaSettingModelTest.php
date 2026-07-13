<?php

namespace Tests\Unit;

use App\Models\ArtaSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArtaSettingModelTest extends TestCase
{
    use RefreshDatabase;

    // @test
    public function testarta_setting_documents_relationship(): void
    {
        $artaSetting = ArtaSetting::factory()->create(['category' => 'simple', 'days' => 3]);
        $doc1 = \App\Models\Document::factory()->create(['arta_setting_id' => $artaSetting->id]);
        $doc2 = \App\Models\Document::factory()->create(['arta_setting_id' => $artaSetting->id]);

        $this->assertCount(2, $artaSetting->documents);
    }

    // @test
    public function testarta_setting_fillable_attributes(): void
    {
        $arta = new ArtaSetting();
        $fillable = $arta->getFillable();

        $this->assertContains('category', $fillable);
        $this->assertContains('days', $fillable);
        $this->assertContains('description', $fillable);
        $this->assertContains('is_active', $fillable);
    }

    // @test
    public function testarta_setting_casts(): void
    {
        $arta = new ArtaSetting();
        $casts = $arta->getCasts();

        $this->assertEquals('integer', $casts['days']);
        $this->assertEquals('boolean', $casts['is_active']);
    }
}