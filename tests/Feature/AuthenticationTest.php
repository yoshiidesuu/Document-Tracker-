<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
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
            'password' => bcrypt('password123'),
        ]);
        $this->admin->roles()->attach($this->adminRole);
    }

    // @test
    public function testuser_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'user@test.com',
            'password' => bcrypt('password123'),
            'status' => 'active',
        ]);

        $response = $this->post(route('login'), [
            'email' => 'user@test.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('system.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    // @test
    public function testuser_cannot_login_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'email' => 'user@test.com',
            'password' => bcrypt('password123'),
            'status' => 'active',
        ]);

        $response = $this->post(route('login'), [
            'email' => 'user@test.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    // @test
    public function testuser_cannot_login_with_invalid_email(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'nonexistent@test.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    // @test
    public function testlogin_rate_limits_after_max_attempts(): void
    {
        for ($i = 0; $i < 6; $i++) {
            $this->post(route('login'), [
                'email' => 'user@test.com',
                'password' => 'wrongpassword',
            ]);
        }

        $response = $this->post(route('login'), [
            'email' => 'user@test.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(429);
    }

    // @test
    public function testauthenticated_user_can_logout(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('login.form'));
        $this->assertGuest();
    }

    // @test
    public function testuser_can_register_with_valid_data(): void
    {
        $data = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'newuser@test.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'id_number' => 'ID12345',
            'department_id' => null,
            'office_id' => null,
        ];

        $response = $this->post(route('register'), $data);

        $response->assertRedirect(route('system.dashboard'));
        $this->assertDatabaseHas('users', ['email' => 'newuser@test.com']);
    }

    // @test
    public function testregistration_validates_required_fields(): void
    {
        $response = $this->post(route('register'), []);

        $response->assertSessionHasErrors([
            'firstname',
            'lastname',
            'email',
            'password',
            'id_number',
        ]);
    }

    // @test
    public function testregistration_validates_email_format(): void
    {
        $data = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'invalid-email',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'id_number' => 'ID12345',
        ];

        $response = $this->post(route('register'), $data);

        $response->assertSessionHasErrors('email');
    }

    // @test
    public function testregistration_validates_unique_email(): void
    {
        User::factory()->create(['email' => 'existing@test.com']);

        $data = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'existing@test.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'id_number' => 'ID12345',
        ];

        $response = $this->post(route('register'), $data);

        $response->assertSessionHasErrors('email');
    }

    // @test
    public function testregistration_validates_password_strength(): void
    {
        $data = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'newuser@test.com',
            'password' => 'weak',
            'password_confirmation' => 'weak',
            'id_number' => 'ID12345',
        ];

        $response = $this->post(route('register'), $data);

        $response->assertSessionHasErrors('password');
    }

    // @test
    public function testregistration_validates_password_confirmation(): void
    {
        $data = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'newuser@test.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Different123!',
            'id_number' => 'ID12345',
        ];

        $response = $this->post(route('register'), $data);

        $response->assertSessionHasErrors('password');
    }

    // @test
    public function testauthenticated_user_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('system.dashboard'));

        $response->assertStatus(200);
        $response->assertViewIs('system.dashboard');
    }

    // @test
    public function testguest_cannot_access_dashboard(): void
    {
        $response = $this->get(route('system.dashboard'));

        $response->assertRedirect(route('login.form'));
    }

    // @test
    public function testuser_can_request_password_reset(): void
    {
        $user = User::factory()->create(['email' => 'user@test.com', 'status' => 'active']);

        $response = $this->post(route('password.email'), ['email' => 'user@test.com']);

        $response->assertRedirect(route('login.form'));
        $response->assertSessionHas('status');
    }

    // @test
    public function testuser_can_change_password_when_authenticated(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('password.change'), [
            'current_password' => 'password123',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertRedirect(route('system.profile'));
        $response->assertSessionHas('success');

        $this->assertTrue($this->admin->fresh()->checkPassword('NewPassword123!'));
    }

    // @test
    public function testpassword_change_validates_current_password(): void
    {
        $this->actingAs($this->admin);

        $response = $this->post(route('password.change'), [
            'current_password' => 'wrongpassword',
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ]);

        $response->assertSessionHasErrors('current_password');
    }

    // @test
    public function testcheck_credential_endpoint_works(): void
    {
        $user = User::factory()->create([
            'email' => 'check@test.com',
            'id_number' => 'CHECK123',
            'firstname' => 'Check',
            'lastname' => 'User',
        ]);

        $response = $this->get(route('check-credential', ['q' => 'check@test.com']));

        $response->assertStatus(200);
        $response->assertJson([
            'exists' => true,
            'field' => 'email',
            'user' => [
                'name' => 'Check User',
                'firstname' => 'Check',
                'lastname' => 'User',
            ],
        ]);
    }

    // @test
    public function testcheck_credential_endpoint_returns_false_for_unknown(): void
    {
        $response = $this->get(route('check-credential', ['q' => 'unknown@test.com']));

        $response->assertStatus(200);
        $response->assertJson(['exists' => false]);
    }

    // @test
    public function testcheck_credential_endpoint_rate_limits(): void
    {
        for ($i = 0; $i < 31; $i++) {
            $this->get(route('check-credential', ['q' => 'test@test.com']));
        }

        $response = $this->get(route('check-credential', ['q' => 'test@test.com']));

        $response->assertStatus(429);
    }

    // @test
    public function testsession_verification_endpoint_works(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get(route('user.verify-session'));

        $response->assertStatus(200);
        $response->assertJson(['valid' => true]);
    }

    // @test
    public function testsession_verification_returns_false_for_guest(): void
    {
        $response = $this->get(route('user.verify-session'));

        $response->assertStatus(200);
        $response->assertJson(['valid' => false]);
    }
}