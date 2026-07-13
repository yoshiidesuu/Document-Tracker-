<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Office;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;
    protected Department $department;
    protected Office $office;
    protected Role $adminRole;
    protected Role $staffRole;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminRole = Role::factory()->create([
            'name' => 'Administrator', 
            'slug' => 'admin',
            'permissions' => [
                'users.list', 'users.create', 'users.view', 'users.edit', 
                'users.delete', 'users.bulk-ban', 'users.ban', 'users.unban', 
                'users.lock', 'users.unlock', 'users.force-logout',
                'users.reset-password'
            ]
        ]);
        $this->staffRole = Role::factory()->create(['name' => 'Staff', 'slug' => 'staff']);

        $this->department = Department::factory()->create(['name' => 'IT Department']);
        $this->office = Office::factory()->create(['name' => 'IT Office', 'department_id' => $this->department->id]);

        $this->admin = User::factory()->create([
            'department_id' => $this->department->id,
            'office_id' => $this->office->id,
            'status' => 'active',
            'email' => 'admin@test.com',
        ]);
        $this->admin->roles()->attach($this->adminRole);

        $this->staff = User::factory()->create([
            'department_id' => $this->department->id,
            'office_id' => $this->office->id,
            'status' => 'active',
            'email' => 'staff@test.com',
        ]);
        $this->staff->roles()->attach($this->staffRole);
    }

    // @test
    public function testadmin_can_view_users_index(): void
    {
        User::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)->get(route('system.users.index'));

        $response->assertStatus(200);
        $response->assertViewIs('system.users.index');
    }

    // @test
public function teststaff_cannot_view_users_index(): void
    {
        $response = $this->actingAs($this->staff)->get(route('system.users.index'));

        $response->assertStatus(403);
    }

    // @test
    public function testuser_creation_validates_unique_email(): void
    {
        $data = [
            'firstname' => 'Jane',
            'lastname' => 'Smith',
            'email' => 'admin@test.com', // Already exists
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'department_id' => $this->department->id,
            'office_id' => $this->office->id,
            'age' => 30,
            'gender' => 'female',
            'bday' => '1994-01-01',
            'roles' => [$this->staffRole->id],
            'status' => 'active',
        ];

        $response = $this->actingAs($this->admin)->post(route('system.users.store'), $data);

        $response->assertSessionHasErrors('email');
    }

    // @test
    public function testadmin_can_create_user(): void
    {
        $data = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'john.doe@test.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'department_id' => $this->department->id,
            'office_id' => $this->office->id,
            'age' => 25,
            'gender' => 'male',
            'bday' => '1999-01-01',
            'roles' => [$this->staffRole->id],
            'status' => 'active',
        ];

        $response = $this->actingAs($this->admin)->post(route('system.users.store'), $data);

        $response->assertRedirect(route('system.users.view', User::where('email', 'john.doe@test.com')->first()->id));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'email' => 'john.doe@test.com',
            'firstname' => 'John',
            'lastname' => 'Doe',
        ]);
    }

    // @test
    public function testadmin_can_view_user(): void
    {
        $response = $this->actingAs($this->admin)->get(route('system.users.view', $this->staff));

        $response->assertStatus(200);
        $response->assertViewIs('system.users.view');
        $response->assertSee($this->staff->full_name);
    }

// @test
    public function testadmin_can_update_user(): void
    {
        $response = $this->actingAs($this->admin)->post(route('system.users.update', $this->staff), [
            'firstname' => 'Jane',
            'lastname' => 'Updated',
            'id_number' => 'ID-99999',
            'department_id' => $this->department->id,
            'office_id' => $this->office->id,
            'age' => 31,
            'gender' => 'female',
            'bday' => '1993-01-01',
            'roles' => [$this->adminRole->id],
        ]);

        $response->assertRedirect(route('system.users.view', $this->staff));
        $response->assertSessionHas('success');

        $this->staff->refresh();
        $this->assertEquals('Jane Updated', $this->staff->full_name);
        $this->assertEquals('staff@test.com', $this->staff->email); // Email not updated by this endpoint
    }

    // @test
    public function testadmin_can_update_user_password(): void
    {
        $response = $this->actingAs($this->admin)->post(route('system.users.password', $this->staff), [
            'new_password' => 'NewPassword123!',
        ]);

        $response->assertRedirect(route('system.users.view', $this->staff));
        $response->assertSessionHas('success');

        $this->staff->refresh();
        $this->assertTrue(password_verify('NewPassword123!', $this->staff->password));
    }

    // @test
    public function testadmin_can_ban_user(): void
    {
        $response = $this->actingAs($this->admin)->post(route('system.users.ban', $this->staff));

        $response->assertRedirect(route('system.users.view', $this->staff));
        $response->assertSessionHas('success');

        $this->staff->refresh();
        $this->assertTrue($this->staff->isBanned());
    }

    // @test
    public function testadmin_can_unban_user(): void
    {
        $this->staff->update(['banned' => true]);

        $response = $this->actingAs($this->admin)->post(route('system.users.unban', $this->staff));

        $response->assertRedirect(route('system.users.view', $this->staff));
        $response->assertSessionHas('success');

        $this->staff->refresh();
        $this->assertFalse($this->staff->isBanned());
    }

    // @test
    public function testadmin_can_lock_user(): void
    {
        $response = $this->actingAs($this->admin)->post(route('system.users.lock', $this->staff));

        $response->assertRedirect(route('system.users.view', $this->staff));
        $response->assertSessionHas('success');

        $this->staff->refresh();
        $this->assertTrue($this->staff->locked);
    }

    // @test
    public function testadmin_can_unlock_user(): void
    {
        $this->staff->update(['locked' => true]);

        $response = $this->actingAs($this->admin)->post(route('system.users.unlock', $this->staff));

        $response->assertRedirect(route('system.users.view', $this->staff));
        $response->assertSessionHas('success');

$this->staff->refresh();
        $this->assertFalse($this->staff->locked);
    }

    // @test
    public function testadmin_can_force_logout_user(): void
    {
        $response = $this->actingAs($this->admin)->post(route('system.users.force-logout', $this->staff));

        $response->assertRedirect(route('system.users.view', $this->staff));
        $response->assertSessionHas('success');
    }

    // @test
    public function testadmin_can_delete_user(): void
    {
        $userToDelete = User::factory()->create(['email' => 'delete@test.com']);

        $response = $this->actingAs($this->admin)->delete(route('system.users.destroy', $userToDelete));

        $response->assertRedirect(route('system.users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $userToDelete->id]);
    }

    // @test
    public function testadmin_cannot_delete_self(): void
    {
        $response = $this->actingAs($this->admin)->delete(route('system.users.destroy', $this->admin));

        $response->assertRedirect(route('system.users.index'));
        $response->assertSessionHas('errors');

        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }

    // @test
    public function testadmin_can_bulk_action_users(): void
    {
        $users = User::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->post(route('system.users.bulk'), [
            'action' => 'ban',
            'selected' => $users->pluck('id')->toArray(),
        ]);

        $response->assertRedirect(route('system.users.index'));
        $response->assertSessionHas('success');

        foreach ($users as $user) {
            $user->refresh();
            $this->assertTrue($user->isBanned());
        }
    }

    // @test
    public function testuser_full_name_accessor_works(): void
    {
        $user = User::factory()->create([
            'firstname' => 'John',
            'middlename' => 'Middle',
            'lastname' => 'Doe',
        ]);

        $this->assertEquals('John Middle Doe', $user->full_name);
    }

    // @test
    public function testuser_initials_accessor_works(): void
    {
        $user = User::factory()->create([
            'firstname' => 'John',
            'lastname' => 'Doe',
        ]);

        $this->assertEquals('JD', $user->initials);
    }

    // @test
    public function testuser_has_role_method_works(): void
    {
        $this->assertTrue($this->admin->hasRole('admin'));
        $this->assertFalse($this->admin->hasRole('staff'));
        $this->assertTrue($this->staff->hasRole('staff'));
    }

    // @test
    public function testuser_has_permission_method_works(): void
    {
        $permission = 'documents.create';
        $this->staffRole->update(['permissions' => [$permission]]);

        $this->assertTrue($this->staff->hasPermission($permission));
        $this->assertFalse($this->admin->hasPermission($permission));
    }
}