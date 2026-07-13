<?php

namespace Tests\Unit;

use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentTrack;
use App\Models\DocumentType;
use App\Models\Office;
use App\Models\Role;
use App\Models\User;
use App\Models\ArtaSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    public function testUserFullNameAccessorWorks(): void
    {
        $user = User::factory()->create([
            'firstname' => 'John',
            'middlename' => 'Middle',
            'lastname' => 'Doe',
        ]);

        $this->assertEquals('John Middle Doe', $user->full_name);
    }

    public function testUserFullNameWithoutMiddlename(): void
    {
        $user = User::factory()->create([
            'firstname' => 'Jane',
            'lastname' => 'Smith',
        ]);

        $this->assertEquals('Jane Smith', $user->full_name);
    }

    public function testUserInitialsAccessorWorks(): void
    {
        $user = User::factory()->create([
            'firstname' => 'John',
            'lastname' => 'Doe',
        ]);

        $this->assertEquals('JD', $user->initials);
    }

    public function testUserInitialsWithSingleName(): void
    {
        $user = User::factory()->create([
            'name' => 'Single',
        ]);

        $this->assertEquals('SI', $user->initials);
    }

    public function testUserProfilePictureUrlReturnsNullWhenEmpty(): void
    {
        $user = User::factory()->create(['profile_picture' => null]);

        $this->assertNull($user->profile_picture_url);
    }

    public function testUserProfilePictureUrlReturnsRouteWhenLocal(): void
    {
        $user = User::factory()->create(['profile_picture' => 'profile.jpg']);

        $expectedUrl = route('file.profile', ['filename' => 'profile.jpg']);
        $this->assertEquals($expectedUrl, $user->profile_picture_url);
    }

    public function testUserProfilePictureUrlReturnsFullUrlWhenHttp(): void
    {
        $user = User::factory()->create(['profile_picture' => 'https://example.com/image.jpg']);

        $this->assertEquals('https://example.com/image.jpg', $user->profile_picture_url);
    }

    public function testUserIsBannedMethod(): void
    {
        $bannedUser = User::factory()->create(['banned' => true]);
        $activeUser = User::factory()->create(['banned' => false]);

        $this->assertTrue($bannedUser->isBanned());
        $this->assertFalse($activeUser->isBanned());
    }

    public function testUserIsActiveMethod(): void
    {
        $activeUser = User::factory()->create([
            'status' => 'active',
            'banned' => false,
            'locked' => false,
        ]);

        $inactiveUser = User::factory()->create(['status' => 'inactive']);
        $bannedUser = User::factory()->create(['status' => 'active', 'banned' => true]);
        $lockedUser = User::factory()->create(['status' => 'active', 'locked' => true]);

        $this->assertTrue($activeUser->isActive());
        $this->assertFalse($inactiveUser->isActive());
        $this->assertFalse($bannedUser->isActive());
        $this->assertFalse($lockedUser->isActive());
    }

    public function testUserRolesRelationship(): void
    {
        $user = User::factory()->create();
        $role = Role::factory()->create(['name' => 'Test Role', 'slug' => 'test-role']);

        $user->roles()->attach($role);

        $this->assertCount(1, $user->roles);
        $this->assertEquals('test-role', $user->roles->first()->slug);
    }

    public function testUserDepartmentRelationship(): void
    {
        $user = User::factory()->create();
        $department = Department::factory()->create();

        $user->department()->associate($department);
        $user->save();

        $this->assertEquals($department->id, $user->department->id);
    }

    public function testUserOfficeRelationship(): void
    {
        $user = User::factory()->create();
        $office = Office::factory()->create();

        $user->office()->associate($office);
        $user->save();

        $this->assertEquals($office->id, $user->office->id);
    }

    public function testUserHasRoleMethod(): void
    {
        $user = User::factory()->create();
        $role = Role::factory()->create(['slug' => 'admin']);
        $user->roles()->attach($role);

        $this->assertTrue($user->hasRole('admin'));
        $this->assertFalse($user->hasRole('staff'));
        $this->assertTrue($user->hasRole(['admin', 'staff']));
    }

    public function testUserHasPermissionMethod(): void
    {
        $user = User::factory()->create();
        $role = Role::factory()->create();
        $user->roles()->attach($role);

        $permission = 'documents.create';
        $role->permissions()->create(['name' => $permission]);

        $this->assertTrue($user->hasPermission($permission));
        $this->assertFalse($user->hasPermission('documents.delete'));
    }

    public function testUserAdminHasAllPermissions(): void
    {
        $user = User::factory()->create();
        $adminRole = Role::factory()->create(['slug' => 'admin']);
        $user->roles()->attach($adminRole);

        $this->assertTrue($user->hasPermission('any.permission'));
    }
}