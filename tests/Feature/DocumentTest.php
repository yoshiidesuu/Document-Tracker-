<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentTrack;
use App\Models\DocumentType;
use App\Models\Office;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Role $adminRole;
    protected User $staff;
    protected Department $department;
    protected Office $office;
    protected DocumentType $documentType;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminRole = Role::factory()->create([
            'name' => 'Administrator', 
            'slug' => 'admin',
            'permissions' => [
                'documents.list', 'documents.create', 'documents.view', 'documents.edit', 
                'documents.delete', 'documents.my', 'documents.my-scanned', 
                'documents.receive', 'documents.finish', 'documents.terminate', 'documents.reopen'
            ]
        ]);
        $this->staffRole = Role::factory()->create([
            'name' => 'Staff', 
            'slug' => 'staff',
            'permissions' => [
                'documents.receive', 'documents.finish', 'documents.terminate', 'documents.my', 'documents.my-scanned'
            ]
        ]);

        $this->admin = User::factory()->create([
            'status' => 'active',
            'email' => 'admin@test.com',
        ]);
        $this->admin->roles()->attach($this->adminRole);

        $this->staff = User::factory()->create([
            'status' => 'active',
            'email' => 'staff@test.com',
        ]);
        $this->staff->roles()->attach($this->staffRole);

        $this->department = Department::factory()->create();
        $this->office = Office::factory()->create(['department_id' => $this->department->id]);
        $this->documentType = DocumentType::factory()->create();
    }

    // @test
    public function testadmin_can_view_documents_index(): void
    {
        Document::factory()->count(10)->create(['creator_id' => $this->admin->id]);

        $response = $this->actingAs($this->admin)->get(route('system.documents.index'));

        $response->assertStatus(200);
        $response->assertViewIs('system.documents.index');
    }

    // @test
    public function testadmin_can_create_document(): void
    {
        $data = [
            'title' => 'Test Document',
            'document_type' => 'Memorandum',
            'processing_hours' => 8,
            'is_private' => false,
            'notes' => 'Test notes',
        ];

        $response = $this->actingAs($this->admin)->post(route('system.documents.store'), $data);

        $response->assertRedirect(route('system.documents.print', Document::where('title', 'Test Document')->first()->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('documents', [
            'title' => 'Test Document',
            'creator_id' => $this->admin->id,
        ]);
    }

    // @test
    public function testdocument_creation_validates_required_fields(): void
    {
        $response = $this->actingAs($this->admin)->post(route('system.documents.store'), []);

        $response->assertSessionHasErrors(['title', 'document_type']);
    }

    // @test
    public function testadmin_can_view_document(): void
    {
        $document = Document::factory()->create(['creator_id' => $this->admin->id]);

        $response = $this->actingAs($this->admin)->get(route('system.documents.view', $document));

        $response->assertStatus(200);
        $response->assertViewIs('system.documents.view');
        $response->assertSee($document->title);
    }

    // @test
    public function testadmin_can_edit_document(): void
    {
        $document = Document::factory()->create(['creator_id' => $this->admin->id]);

        $response = $this->actingAs($this->admin)->get(route('system.documents.edit', $document));

        $response->assertStatus(200);
        $response->assertViewIs('system.documents.edit');
    }

    // @test
    public function testadmin_can_update_document(): void
    {
        $document = Document::factory()->create([
            'creator_id' => $this->admin->id,
            'status' => 'pending',
        ]);

        $this->assertFalse($document->tracks()->exists());

        $response = $this->actingAs($this->admin)->post(route('system.documents.update', $document), [
            'title' => 'Updated Document',
            'document_type' => 'Memorandum',
            'processing_hours' => 4,
            'is_private' => true,
            'notes' => 'Updated notes',
        ]);

        $response->assertRedirect(route('system.documents.view', $document));
        $response->assertSessionHas('success');

        $document->refresh();
        $this->assertEquals('Updated Document', $document->title);
        $this->assertTrue($document->is_private);
    }

    // @test
    public function testadmin_can_delete_document(): void
    {
        $document = Document::factory()->create(['creator_id' => $this->admin->id]);

        $response = $this->actingAs($this->admin)->delete(route('system.documents.destroy', $document));

        $response->assertRedirect(route('system.documents.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('documents', ['id' => $document->id]);
    }

    // @test
    public function testadmin_can_view_my_documents(): void
    {
        Document::factory()->count(3)->create(['creator_id' => $this->admin->id]);
        Document::factory()->count(2)->create(['creator_id' => $this->staff->id]);

        $response = $this->actingAs($this->admin)->get(route('system.documents.my'));

        $response->assertStatus(200);
        $response->assertViewIs('system.documents.my');
    }

// @test
    public function testdocument_receive_workflow(): void
    {
        $document = Document::factory()->create([
            'creator_id' => $this->admin->id,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->staff)->post(route('system.documents.receive.store', $document));

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Document received successfully.']);

        $document->refresh();
        // Status is not updated by the controller, only track is created

        $track = DocumentTrack::where('document_id', $document->id)
            ->where('user_id', $this->staff->id)
            ->whereNull('released_at')
            ->first();

        $this->assertNotNull($track);
        $this->assertNotNull($track->received_at);
    }

    // @test
    public function testdocument_finish_workflow(): void
    {
        $document = Document::factory()->create([
            'creator_id' => $this->admin->id,
            'status' => 'in_transit',
        ]);

        DocumentTrack::factory()->create([
            'document_id' => $document->id,
            'user_id' => $this->staff->id,
            'received_at' => now(),
            'released_at' => null,
        ]);

        $response = $this->actingAs($this->staff)->post(route('system.documents.finish.store', $document));

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Document transaction finished successfully.']);

        $document->refresh();
        $this->assertEquals('finished', $document->status);

        $track = DocumentTrack::where('document_id', $document->id)
            ->where('user_id', $this->staff->id)
            ->whereNotNull('released_at')
            ->first();

        $this->assertNotNull($track);
        $this->assertNotNull($track->released_at);
    }

// @test
    public function testdocument_terminate_workflow(): void
    {
        $document = Document::factory()->create([
            'creator_id' => $this->admin->id,
            'status' => 'in_transit',
        ]);

        DocumentTrack::factory()->create([
            'document_id' => $document->id,
            'user_id' => $this->staff->id,
            'received_at' => now(),
            'released_at' => null,
        ]);

        $response = $this->actingAs($this->staff)->post(route('system.documents.terminate.store', $document), [
            'reason' => 'Document no longer needed',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Document terminated successfully.']);

        $document->refresh();
        $this->assertEquals('terminated', $document->status);
        $this->assertEquals('Document no longer needed', $document->termination_reason);

        $track = DocumentTrack::where('document_id', $document->id)
            ->where('user_id', $this->staff->id)
            ->whereNotNull('released_at')
            ->first();

        $this->assertNotNull($track);
    }

    // @test
    public function testdocument_reopen_workflow(): void
    {
        $document = Document::factory()->create([
            'creator_id' => $this->admin->id,
            'status' => 'terminated',
            'termination_reason' => 'Error',
        ]);

        DocumentTrack::factory()->create([
            'document_id' => $document->id,
            'user_id' => $this->staff->id,
            'received_at' => now()->subDay(),
            'released_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->post(route('system.documents.reopen', $document));

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Document reopened successfully. It can now be received again.']);

        $document->refresh();
        $this->assertEquals('pending', $document->status);
        $this->assertNull($document->termination_reason);
    }

    // @test
    public function testdocument_print_view_works(): void
    {
        $document = Document::factory()->create([
            'creator_id' => $this->admin->id,
            'qr_value' => 'QR123',
            'barcode_value' => 'BAR123',
        ]);

        $response = $this->actingAs($this->admin)->get(route('system.documents.print', $document));

        $response->assertStatus(200);
        $response->assertViewIs('system.documents.print');
        $response->assertSee($document->title);
    }

    // @test
    public function testdocument_lookup_by_code_works(): void
    {
        $document = Document::factory()->create([
            'creator_id' => $this->admin->id,
            'qr_value' => 'LOOKUP-QR-123',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($this->staff)->post(route('system.documents.receive.lookup'), [
            'code' => 'LOOKUP-QR-123',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'document' => ['id', 'title'],
            'current_holder',
            'current_track',
            'past_tracks',
            'qr_data_url',
        ]);
    }

// @test
    public function testdocument_lookup_returns_not_found_for_invalid_code(): void
    {
        $response = $this->actingAs($this->staff)->post(route('system.documents.receive.lookup'), [
            'code' => 'INVALID-CODE',
        ]);

        $response->assertStatus(404);
        $response->assertJson(['error' => 'Document not found with this code.']);
    }

    // @test
    public function testdocument_scanner_pages_work(): void
    {
        $response = $this->actingAs($this->staff)->get(route('system.documents.receive'));
        $response->assertStatus(200);
        $response->assertViewIs('system.documents.receive');

        $response = $this->actingAs($this->staff)->get(route('system.documents.finish'));
        $response->assertStatus(200);
        $response->assertViewIs('system.documents.finish');

        $response = $this->actingAs($this->staff)->get(route('system.documents.terminate'));
        $response->assertStatus(200);
        $response->assertViewIs('system.documents.terminate');
    }

    // @test
    public function testdocument_track_relationships_work(): void
    {
        $document = Document::factory()->create();
        $track1 = DocumentTrack::factory()->create([
            'document_id' => $document->id,
            'user_id' => $this->admin->id,
            'received_at' => now()->subDay(),
            'released_at' => now(),
        ]);
        $track2 = DocumentTrack::factory()->create([
            'document_id' => $document->id,
            'user_id' => $this->staff->id,
            'received_at' => now(),
            'released_at' => null,
        ]);

        $currentHolder = $document->currentHolder()->first();
        $this->assertEquals($this->staff->id, $currentHolder->user_id);

        $pastHolders = $document->tracks()->whereNotNull('released_at')->get();
        $this->assertCount(1, $pastHolders);
        $this->assertEquals($this->admin->id, $pastHolders->first()->user_id);
    }

    // @test
    public function testdocument_arta_category_affects_processing_days(): void
    {
        $document = Document::factory()->create([
            'arta_category' => 'simple',
            'arta_setting_id' => null,
        ]);
        $this->assertEquals(3, $document->arta_processing_days);

        $document->update(['arta_category' => 'complex']);
        $this->assertEquals(7, $document->fresh()->arta_processing_days);

        $document->update(['arta_category' => 'highly_technical']);
        $this->assertEquals(20, $document->fresh()->arta_processing_days);
    }
}