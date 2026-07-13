<?php

namespace Tests\Feature;

use App\Models\ArtaSetting;
use App\Models\Document;
use App\Models\DocumentType;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentTypeArtaTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Role $adminRole;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminRole = Role::factory()->create([
            'name' => 'Administrator',
            'slug' => 'admin',
            'permissions' => [
                'document-types.list', 'document-types.create', 'document-types.view',
                'document-types.edit', 'document-types.delete', 'document-types.toggle-status',
                'arta.list', 'arta.create', 'arta.view', 'arta.edit', 'arta.delete', 'arta.toggle-status',
            ],
        ]);

        $this->admin = User::factory()->create([
            'status' => 'active',
            'email' => 'admin@test.com',
        ]);
        $this->admin->roles()->attach($this->adminRole);
    }

    // @test
    public function testadmin_can_view_document_types_index(): void
    {
        DocumentType::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)->get(route('system.document-types.index'));

        $response->assertStatus(200);
        $response->assertViewIs('system.document-types.index');
    }

    // @test
    public function testadmin_can_create_document_type(): void
    {
        $data = [
            'name' => 'New Document Type',
            'description' => 'Description for document type',
        ];

        $response = $this->actingAs($this->admin)->post(route('system.document-types.store'), $data);

        $response->assertRedirect(route('system.document-types.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('document_types', [
            'name' => 'New Document Type',
        ]);
    }

    // @test
    public function testdocument_type_creation_validates_required_fields(): void
    {
        $response = $this->actingAs($this->admin)->post(route('system.document-types.store'), []);

        $response->assertSessionHasErrors('name');
    }

    // @test
    public function testdocument_type_creation_validates_unique_code(): void
    {
        DocumentType::factory()->create(['code' => 'memorandum']);

        $response = $this->actingAs($this->admin)->post(route('system.document-types.store'), [
            'name' => 'New Type',
            'code' => 'memorandum',
        ]);

        $response->assertSessionHasErrors('code');
    }

    // @test
    public function testadmin_can_view_document_type(): void
    {
        $documentType = DocumentType::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('system.document-types.view', $documentType));

        $response->assertStatus(200);
        $response->assertViewIs('system.document-types.view');
        $response->assertSee($documentType->name);
    }

    // @test
    public function testadmin_can_update_document_type(): void
    {
        $documentType = DocumentType::factory()->create();

        $response = $this->actingAs($this->admin)->post(route('system.document-types.update', $documentType), [
            'name' => 'Updated Document Type',
            'description' => 'Updated description',
        ]);

        $response->assertRedirect(route('system.document-types.view', $documentType));
        $response->assertSessionHas('success');

        $documentType->refresh();
        $this->assertEquals('Updated Document Type', $documentType->name);
    }

    // @test
    public function testadmin_can_toggle_document_type_status(): void
    {
        $documentType = DocumentType::factory()->create(['is_active' => true]);

        $response = $this->actingAs($this->admin)->post(route('system.document-types.toggle-status', $documentType));

        $response->assertRedirect(route('system.document-types.view', $documentType));
        $response->assertSessionHas('success');

        $documentType->refresh();
        $this->assertFalse($documentType->is_active);
    }

    // @test
    public function testadmin_can_delete_document_type(): void
    {
        $documentType = DocumentType::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('system.document-types.destroy', $documentType));

        $response->assertRedirect(route('system.document-types.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('document_types', ['id' => $documentType->id]);
    }

    // @test
    public function testadmin_can_view_arta_settings_index(): void
    {
        ArtaSetting::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)->get(route('system.arta-settings.index'));

        $response->assertStatus(200);
        $response->assertViewIs('system.arta-settings.index');
    }

    // @test
    public function testadmin_can_create_arta_setting(): void
    {
        $data = [
            'category' => 'simple',
            'title' => 'Simple Transaction Setting',
            'days' => 3,
        ];

        $response = $this->actingAs($this->admin)->post(route('system.arta-settings.store'), $data);

        $response->assertRedirect(route('system.arta-settings.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('arta_settings', [
            'category' => 'simple',
            'title' => 'Simple Transaction Setting',
            'days' => 3,
        ]);
    }

    // @test
    public function testarta_setting_creation_validates_required_fields(): void
    {
        $response = $this->actingAs($this->admin)->post(route('system.arta-settings.store'), []);

        $response->assertSessionHasErrors(['category', 'title']);
    }

    // @test
    public function testarta_setting_creation_validates_unique_title_per_category(): void
    {
        $artaSetting = ArtaSetting::factory()->create(['category' => 'simple', 'title' => 'Simple Setting']);

        $response = $this->actingAs($this->admin)->post(route('system.arta-settings.store'), [
            'category' => 'simple',
            'title' => 'Simple Setting',
            'days' => 5,
        ]);

        $response->assertSessionHasErrors('title');
    }

    // @test
    public function testadmin_can_view_arta_setting(): void
    {
        $artaSetting = ArtaSetting::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('system.arta-settings.view', $artaSetting));

        $response->assertStatus(200);
        $response->assertViewIs('system.arta-settings.view');
        $response->assertSee($artaSetting->category);
    }

    // @test
    public function testadmin_can_update_arta_setting(): void
    {
        $artaSetting = ArtaSetting::factory()->create();

        $response = $this->actingAs($this->admin)->post(route('system.arta-settings.update', $artaSetting), [
            'category' => 'complex',
            'title' => 'Updated Setting Title',
            'days' => 7,
        ]);

        $response->assertRedirect(route('system.arta-settings.view', $artaSetting));
        $response->assertSessionHas('success');

        $artaSetting->refresh();
        $this->assertEquals('complex', $artaSetting->category);
        $this->assertEquals(7, $artaSetting->days);
    }

    // @test
    public function testadmin_can_toggle_arta_setting_status(): void
    {
        $artaSetting = ArtaSetting::factory()->create(['is_active' => true]);

        $response = $this->actingAs($this->admin)->post(route('system.arta-settings.toggle-status', $artaSetting));

        $response->assertRedirect(route('system.arta-settings.view', $artaSetting));
        $response->assertSessionHas('success');

        $artaSetting->refresh();
        $this->assertFalse($artaSetting->is_active);
    }

    // @test
    public function testadmin_can_delete_arta_setting(): void
    {
        $artaSetting = ArtaSetting::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('system.arta-settings.destroy', $artaSetting));

        $response->assertRedirect(route('system.arta-settings.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('arta_settings', ['id' => $artaSetting->id]);
    }

    // @test
    public function testdocument_model_generates_qr_code_url(): void
    {
        $document = Document::factory()->create([
            'qr_value' => 'TEST-QR-123',
        ]);

        $qrUrl = $document->getQrCodeUrl();

        $this->assertStringStartsWith('data:image/png;base64,', $qrUrl);
    }

    // @test
    public function testdocument_model_generates_barcode_url(): void
    {
        $document = Document::factory()->create([
            'barcode_value' => 'TEST-BARCODE-123',
        ]);

        $barcodeUrl = $document->getBarcodeUrl();

        $this->assertStringStartsWith('data:image/png;base64,', $barcodeUrl);
    }

    // @test
    public function testdocument_model_calculates_arta_processing_days(): void
    {
        $artaSetting = ArtaSetting::factory()->create(['category' => 'simple', 'days' => 3]);

        $document = Document::factory()->create([
            'arta_setting_id' => $artaSetting->id,
            'arta_category' => 'simple',
        ]);

        $this->assertEquals(3, $document->arta_processing_days);
    }

    // @test
    public function testdocument_model_uses_default_days_when_no_setting(): void
    {
        $document = Document::factory()->create([
            'arta_setting_id' => null,
            'arta_category' => 'complex',
        ]);

        $this->assertEquals(7, $document->arta_processing_days);
    }

    // @test
    public function testdocument_model_uses_highly_technical_days(): void
    {
        $document = Document::factory()->create([
            'arta_setting_id' => null,
            'arta_category' => 'highly_technical',
        ]);

        $this->assertEquals(20, $document->arta_processing_days);
    }
}
