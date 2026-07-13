<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolePermissionTest extends TestCase
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
            'permissions' => ['roles.list', 'roles.create', 'roles.view', 'roles.edit', 'roles.delete', 'permissions.manage'],
        ]);

        $this->admin = User::factory()->create([
            'status' => 'active',
            'email' => 'admin@test.com',
        ]);
        $this->admin->roles()->attach($this->adminRole);
    }

    // @test
    public function testadmin_can_view_roles_index(): void
    {
        Role::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)->get(route('system.roles.index'));

        $response->assertStatus(200);
        $response->assertViewIs('system.roles.index');
    }

    // @test
    public function testadmin_can_create_role(): void
    {
        $data = [
            'name' => 'New Role',
            'slug' => 'new-role',
            'description' => 'Role description',
        ];

        $response = $this->actingAs($this->admin)->post(route('system.roles.store'), $data);

        $response->assertRedirect(route('system.roles.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('roles', [
            'name' => 'New Role',
            'slug' => 'new-role',
        ]);
    }

    // @test
    public function testrole_creation_validates_required_fields(): void
    {
        $response = $this->actingAs($this->admin)->post(route('system.roles.store'), []);

        $response->assertSessionHasErrors(['name', 'slug']);
    }

    // @test
    public function testrole_creation_validates_unique_slug(): void
    {
        Role::factory()->create(['slug' => 'existing-role']);

        $response = $this->actingAs($this->admin)->post(route('system.roles.store'), [
            'name' => 'New Role',
            'slug' => 'existing-role',
        ]);

        $response->assertSessionHasErrors('slug');
    }

    // @test
    public function testadmin_can_edit_role(): void
    {
        $role = Role::factory()->create();

        $response = $this->actingAs($this->admin)->get(route('system.roles.edit', $role));

        $response->assertStatus(200);
        $response->assertViewIs('system.roles.edit');
    }

    // @test
    public function testadmin_can_update_role(): void
    {
        $role = Role::factory()->create();

        $response = $this->actingAs($this->admin)->post(route('system.roles.update', $role), [
            'name' => 'Updated Role',
            'slug' => 'updated-role',
            'description' => 'Updated description',
        ]);

        $response->assertRedirect(route('system.roles.index'));
        $response->assertSessionHas('success');

        $role->refresh();
        $this->assertEquals('Updated Role', $role->name);
        $this->assertEquals('updated-role', $role->slug);
    }

    // @test
    public function testadmin_can_delete_role(): void
    {
        $role = Role::factory()->create(['is_system' => false]);

        $response = $this->actingAs($this->admin)->delete(route('system.roles.destroy', $role));

        $response->assertRedirect(route('system.roles.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    // @test
    public function testadmin_cannot_delete_system_role(): void
    {
        $role = Role::factory()->create(['is_system' => true, 'slug' => 'system-role']);

        $response = $this->actingAs($this->admin)->delete(route('system.roles.destroy', $role));

        $response->assertRedirect(route('system.roles.index'));
        $response->assertSessionHas('errors');

        $this->assertDatabaseHas('roles', ['id' => $role->id]);
    }

    // @test
    public function testadmin_can_view_permissions(): void
    {
        $response = $this->actingAs($this->admin)->get(route('system.permissions.index'));

        $response->assertStatus(200);
        $response->assertViewIs('system.permissions.index');
    }

    // @test
    public function testadmin_can_update_permissions(): void
    {
        $role = Role::factory()->create();

        $permissions = [
            'documents.create' => true,
            'documents.view' => true,
            'documents.edit' => false,
            'users.manage' => true,
        ];

        $response = $this->actingAs($this->admin)->post(route('system.permissions.update'), [
            'role_id' => $role->id,
            'permissions' => array_keys(array_filter($permissions)),
        ]);

        $response->assertRedirect(route('system.permissions.index', ['role' => $role->id]));
        $response->assertSessionHas('success');

        $role->refresh();
        $this->assertTrue($role->hasPermission('documents.create'));
        $this->assertTrue($role->hasPermission('documents.view'));
        $this->assertFalse($role->hasPermission('documents.edit'));
        $this->assertTrue($role->hasPermission('users.manage'));
    }

    // @test
    public function testadmin_can_toggle_permission(): void
    {
        $role = Role::factory()->create();
        $permission = 'documents.create';

        $response = $this->actingAs($this->admin)->post(route('system.permissions.toggle'), [
            'role_id' => $role->id,
            'permission' => $permission,
            'enabled' => true,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $role->refresh();
        $this->assertTrue($role->hasPermission($permission));

        // Toggle off
        $response = $this->actingAs($this->admin)->post(route('system.permissions.toggle'), [
            'role_id' => $role->id,
            'permission' => $permission,
            'enabled' => false,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $role->refresh();
        $this->assertFalse($role->hasPermission($permission));
    }

    // @test
    public function testrole_has_permission_method_works(): void
    {
        $permission = 'documents.create';
        $this->adminRole->update(['permissions' => [$permission]]);

        $this->assertTrue($this->adminRole->hasPermission($permission));
        $this->assertFalse($this->adminRole->hasPermission('non.existent.permission'));
    }

    // @test
    public function testuser_has_permission_through_role(): void
    {
        $permission = 'documents.create';
        $this->adminRole->update(['permissions' => [$permission]]);

        $this->assertTrue($this->admin->hasPermission($permission));
    }

    // @test
    public function testuser_does_not_have_permission_without_role(): void
    {
        $user = User::factory()->create(['status' => 'active']);
        $user->roles()->detach();

        $this->assertFalse($user->hasPermission('any.permission'));
    }

    // @test
    public function testadmin_has_permissions_manage(): void
    {
        // Admin role should have permissions.manage permission by default
        $this->assertTrue($this->admin->hasPermission('permissions.manage'));
    }

    // @test
    public function testrole_permissions_json_field_works(): void
    {
        $role = Role::factory()->create();
        $permission = 'test.permission';

        $role->update(['permissions' => [$permission]]);

        $this->assertCount(1, $role->permissions);
        $this->assertEquals($permission, $role->permissions[0]);
    }
}
