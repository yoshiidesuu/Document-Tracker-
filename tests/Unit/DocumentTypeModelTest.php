<?php

namespace Tests\Unit;

use App\Models\Document;
use App\Models\DocumentType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentTypeModelTest extends TestCase
{
    use RefreshDatabase;

    // @test
    public function testdocument_type_documents_relationship(): void
    {
        $documentType = DocumentType::factory()->create();
        $doc1 = Document::factory()->create(['document_type_id' => $documentType->id]);
        $doc2 = Document::factory()->create(['document_type_id' => $documentType->id]);

        $this->assertCount(2, $documentType->documents);
    }

    // @test
    public function testdocument_type_fillable_attributes(): void
    {
        $docType = new DocumentType;
        $fillable = $docType->getFillable();

        $this->assertContains('name', $fillable);
        $this->assertContains('description', $fillable);
        $this->assertContains('is_active', $fillable);
    }

    // @test
    public function testdocument_type_casts(): void
    {
        $docType = new DocumentType;
        $casts = $docType->getCasts();

        $this->assertEquals('boolean', $casts['is_active']);
    }
}
