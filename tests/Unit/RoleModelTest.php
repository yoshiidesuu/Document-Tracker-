<?php

namespace Tests\Unit;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleModelTest extends TestCase
{
    use RefreshDatabase;

    // @test
    public function testrole_has_permissions_relationship(): void
    {
        $role = Role::factory()->create(['name' => 'Test Role', 'slug' => 'test-role']);
        $permission1 = $role->permissions()->create(['name' => 'documents.create']);
        $permission2 = $role->permissions()->create(['name' => 'documents.view']);

        $this->assertCount(2, $role->permissions);
        $this->assertEquals('documents.create', $permission1->name);
    }

    // @test
    public function testrole_has_permission_method(): void
    {
        $role = Role::factory()->create();
        $role->permissions()->create(['name' => 'documents.create']);

        $this->assertTrue($role->hasPermission('documents.create'));
        $this->assertFalse($role->hasPermission('documents.delete'));
    }

    // @test
    public function testrole_users_relationship(): void
    {
        $role = Role::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $role->users()->attach([$user1->id, $user2->id]);

        $this->assertCount(2, $role->users);
    }

    // @test
    public function testrole_is_system_flag(): void
    {
        $systemRole = Role::factory()->create(['is_system' => true]);
        $normalRole = Role::factory()->create(['is_system' => false]);

        $this->assertTrue($systemRole->is_system);
        $this->assertFalse($normalRole->is_system);
    }

    // @test
    public function testrole_fillable_attributes(): void
    {
        $role = new Role();
        $fillable = $role->getFillable();

        $this->assertContains('name', $fillable);
        $this->assertContains('description', $fillable);
        $this->assertContains('is_system', $fillable);
    }
}