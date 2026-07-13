<?php

namespace Tests\Unit;

use App\Models\Department;
use App\Models\Office;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DepartmentModelTest extends TestCase
{
    use RefreshDatabase;

    // @test
    public function testdepartment_users_relationship(): void
    {
        $department = Department::factory()->create(['name' => 'IT']);
        $user1 = User::factory()->create(['department_id' => $department->id]);
        $user2 = User::factory()->create(['department_id' => $department->id]);

        $this->assertCount(2, $department->users);
    }

    // @test
    public function testdepartment_offices_relationship(): void
    {
        $department = Department::factory()->create();
        $office1 = Office::factory()->create(['department_id' => $department->id]);
        $office2 = Office::factory()->create(['department_id' => $department->id]);

        $this->assertCount(2, $department->offices);
    }

    // @test
    public function testdepartment_fillable_attributes(): void
    {
        $department = new Department;
        $fillable = $department->getFillable();

        $this->assertContains('name', $fillable);
        $this->assertContains('description', $fillable);
        $this->assertContains('is_active', $fillable);
    }

    // @test
    public function testdepartment_casts(): void
    {
        $department = new Department;
        $casts = $department->getCasts();

        $this->assertEquals('boolean', $casts['is_active']);
    }
}
