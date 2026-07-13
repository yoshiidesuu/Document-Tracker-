<?php

namespace Tests\Unit;

use App\Models\Office;
use App\Models\User;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OfficeModelTest extends TestCase
{
    use RefreshDatabase;

    // @test
    public function testoffice_department_relationship(): void
    {
        $department = Department::factory()->create();
        $office = Office::factory()->create(['department_id' => $department->id]);

        $this->assertEquals($department->id, $office->department->id);
    }

    // @test
    public function testoffice_users_relationship(): void
    {
        $office = Office::factory()->create();
        $user1 = User::factory()->create(['office_id' => $office->id]);
        $user2 = User::factory()->create(['office_id' => $office->id]);

        $this->assertCount(2, $office->users);
    }

    // @test
    public function testoffice_fillable_attributes(): void
    {
        $office = new Office();
        $fillable = $office->getFillable();

        $this->assertContains('name', $fillable);
        $this->assertContains('description', $fillable);
        $this->assertContains('is_active', $fillable);
        $this->assertContains('department_id', $fillable);
    }

    // @test
    public function testoffice_casts(): void
    {
        $office = new Office();
        $casts = $office->getCasts();

        $this->assertEquals('boolean', $casts['is_active']);
    }
}