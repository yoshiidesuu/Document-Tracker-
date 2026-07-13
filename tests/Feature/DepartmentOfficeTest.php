<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Office;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartmentOfficeTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected Role $adminRole;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminRole = Role::factory()->create(['name' => 'Administrator', 'slug' => 'admin']);

        $this->admin = User::factory()->create([
            'status' => 'active',
            'email' => 'admin@test.com',
        ]);
        $this->admin->roles()->attach($this->adminRole);
    }

    // @test
    public function testadmin_can_view_departments_index(): void
    {
        Department::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)->get(route('system.departments.index'));

        $response->assertStatus(200);
        $response->assertViewIs('system.departments.index');
    }

    // @test
    public function testadmin_can_create_department(): void
    {
        $data = [
            'name' => 'New Department',
            'description' => 'Department description',
        ];

        $response = $this->actingAs($this->admin)->post(route('system.departments.store'), $data);

        $response->assertRedirect(route('system.departments.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('departments', [
            'name' => 'New Department',
        ]);
    }

    // @test
    public function testdepartment_creation_validates_required_fields(): void
    {
        $response = $this->actingAs($this->admin)->post(route('system.departments.store'), []);

        $response->assertSessionHasErrors('name');
    }

    // @test
    public function testdepartment_creation_validates_unique_name(): void
    {
        Department::factory()->create(['name' => 'IT Department']);

        $response = $this->actingAs($this->admin)->post(route('system.departments.store'), [
            'name' => 'IT Department',
        ]);

        $response->assertSessionHasErrors('name');
    }

    // @test
    public function testadmin_can_view_department(): void
    {
        $department = Department::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('system.departments.view', $department));

        $response->assertStatus(200);
        $response->assertViewIs('system.departments.view');
        $response->assertSee($department->name);
    }

    // @test
    public function testadmin_can_update_department(): void
    {
        $department = Department::factory()->create();

        $response = $this->actingAs($this->admin)->post(route('system.departments.update', $department), [
            'name' => 'Updated Department',
            'description' => 'Updated description',
        ]);

        $response->assertRedirect(route('system.departments.view', $department));
        $response->assertSessionHas('success');

        $department->refresh();
        $this->assertEquals('Updated Department', $department->name);
    }

    // @test
    public function testadmin_can_toggle_department_status(): void
    {
        $department = Department::factory()->create(['is_active' => true]);

        $response = $this->actingAs($this->admin)->post(route('system.departments.toggle-status', $department));

        $response->assertRedirect(route('system.departments.index'));
        $response->assertSessionHas('success');

        $department->refresh();
        $this->assertFalse($department->is_active);
    }

    // @test
    public function testadmin_can_delete_department(): void
    {
        $department = Department::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('system.departments.destroy', $department));

        $response->assertRedirect(route('system.departments.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('departments', ['id' => $department->id]);
    }

    // @test
    public function testadmin_can_view_offices_index(): void
    {
        Office::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)->get(route('system.offices.index'));

        $response->assertStatus(200);
        $response->assertViewIs('system.offices.index');
    }

    // @test
    public function testadmin_can_create_office(): void
    {
        $department = Department::factory()->create();

        $data = [
            'name' => 'New Office',
            'description' => 'Office description',
            'department_id' => $department->id,
        ];

        $response = $this->actingAs($this->admin)->post(route('system.offices.store'), $data);

        $response->assertRedirect(route('system.offices.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('offices', [
            'name' => 'New Office',
            'department_id' => $department->id,
        ]);
    }

    // @test
    public function testoffice_creation_validates_required_fields(): void
    {
        $response = $this->actingAs($this->admin)->post(route('system.offices.store'), []);

        $response->assertSessionHasErrors(['name', 'department_id']);
    }

    // @test
    public function testadmin_can_view_office(): void
    {
        $office = Office::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('system.offices.view', $office));

        $response->assertStatus(200);
        $response->assertViewIs('system.offices.view');
        $response->assertSee($office->name);
    }

    // @test
    public function testadmin_can_update_office(): void
    {
        $office = Office::factory()->create();

        $response = $this->actingAs($this->admin)->post(route('system.offices.update', $office), [
            'name' => 'Updated Office',
            'description' => 'Updated description',
            'department_id' => $office->department_id,
        ]);

        $response->assertRedirect(route('system.offices.view', $office));
        $response->assertSessionHas('success');

        $office->refresh();
        $this->assertEquals('Updated Office', $office->name);
    }

    // @test
    public function testadmin_can_toggle_office_status(): void
    {
        $office = Office::factory()->create(['is_active' => true]);

        $response = $this->actingAs($this->admin)->post(route('system.offices.toggle-status', $office));

        $response->assertRedirect(route('system.offices.index'));
        $response->assertSessionHas('success');

        $office->refresh();
        $this->assertFalse($office->is_active);
    }

    // @test
    public function testadmin_can_delete_office(): void
    {
        $office = Office::factory()->create();

        $response = $this->actingAs($this->admin)->delete(route('system.offices.destroy', $office));

        $response->assertRedirect(route('system.offices.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('offices', ['id' => $office->id]);
    }
}
