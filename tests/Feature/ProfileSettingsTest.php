<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Office;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Role $role;
    protected Department $department;
    protected Office $office;

    protected function setUp(): void
    {
        parent::setUp();
        $this->role = Role::factory()->create([
            'name' => 'Staff', 
            'slug' => 'staff',
            'permissions' => ['settings.access']
        ]);

        $this->department = Department::factory()->create();
        $this->office = Office::factory()->create(['department_id' => $this->department->id]);

        $this->user = User::factory()->create([
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'user@test.com',
            'password' => bcrypt('password123'),
            'department_id' => $this->department->id,
            'office_id' => $this->office->id,
            'age' => 30,
            'gender' => 'male',
            'bday' => '1994-01-01',
            'status' => 'active',
        ]);
        $this->user->roles()->attach($this->role);
    }

    // @test
    public function testauthenticated_user_can_view_profile(): void
    {
        $response = $this->actingAs($this->user)->get(route('system.profile'));

        $response->assertStatus(200);
        $response->assertViewIs('system.profile.index');
        $response->assertSee($this->user->full_name);
    }

    // @test
    public function testauthenticated_user_can_update_profile(): void
    {
        $response = $this->actingAs($this->user)->post(route('system.profile.update'), [
            'firstname' => 'Jane',
            'lastname' => 'Smith',
            'email' => 'jane.smith@test.com',
            'age' => 31,
            'gender' => 'female',
            'bday' => '1993-01-01',
            'department_id' => $this->department->id,
            'office_id' => $this->office->id,
        ]);

        $response->assertRedirect(route('system.profile'));
        $response->assertSessionHas('success');

        $this->user->refresh();
        $this->assertEquals('Jane Smith', $this->user->full_name);
        $this->assertEquals('jane.smith@test.com', $this->user->email);
    }

    // @test
    public function testprofile_update_validates_email_format(): void
    {
        $response = $this->actingAs($this->user)->post(route('system.profile.update'), [
            'firstname' => 'Jane',
            'lastname' => 'Smith',
            'email' => 'invalid-email',
            'age' => 31,
            'gender' => 'female',
            'bday' => '1993-01-01',
            'department_id' => $this->department->id,
            'office_id' => $this->office->id,
        ]);

        $response->assertSessionHasErrors('email');
    }

    // @test
    public function testprofile_update_validates_unique_email(): void
    {
        $otherUser = User::factory()->create(['email' => 'other@test.com']);

        $response = $this->actingAs($this->user)->post(route('system.profile.update'), [
            'firstname' => 'Jane',
            'lastname' => 'Smith',
            'email' => 'other@test.com',
            'age' => 31,
            'gender' => 'female',
            'bday' => '1993-01-01',
            'department_id' => $this->department->id,
            'office_id' => $this->office->id,
        ]);

        $response->assertSessionHasErrors('email');
    }

    // @test
    public function testauthenticated_user_can_view_settings(): void
    {
        $response = $this->actingAs($this->user)->get(route('system.settings'));

        $response->assertStatus(200);
        $response->assertViewIs('system.settings');
    }

    // @test
    public function testauthenticated_user_can_update_settings(): void
    {
        $response = $this->actingAs($this->user)->post(route('system.settings.update'), [
            'site_long_name' => 'Document Tracker',
            'site_short_name' => 'DT',
            'site_description' => 'Test description',
            'color_primary' => '#4f46e5',
            'color_secondary' => '#7c3aed',
            'emails' => [],
            'contacts' => [],
            'addresses' => [],
        ]);

        $response->assertRedirect(route('system.settings'));
        $response->assertSessionHas('success');
    }
}