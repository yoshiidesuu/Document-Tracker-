# Testing Guide

## Overview

The Document Tracker uses **PHPUnit** for testing with Laravel's testing utilities. Tests are organized into Feature and Unit test suites.

## Test Structure

```
tests/
├── Feature/           # Integration/Feature tests
│   ├── AuthenticationTest.php
│   ├── DocumentManagementTest.php
│   ├── DocumentTest.php
│   ├── DocumentTypeArtaTest.php
│   ├── DepartmentOfficeTest.php
│   ├── ProfileSettingsTest.php
│   ├── RolePermissionTest.php
│   └── UserManagementTest.php
├── Unit/              # Unit tests
│   ├── ArtaSettingModelTest.php
│   ├── DepartmentModelTest.php
│   ├── DocumentModelTest.php
│   ├── DocumentTypeModelTest.php
│   ├── OfficeModelTest.php
│   ├── RoleModelTest.php
│   └── UserModelTest.php
├── TestCase.php       # Base test case
└── creates-application.php
```

## Running Tests

### All Tests

```bash
php artisan test
```

### Specific Test Suite

```bash
# Feature tests only
php artisan test --testsuite=Feature

# Unit tests only
php artisan test --testsuite=Unit
```

### Specific Test Class

```bash
php artisan test --filter=DocumentManagementTest
```

### Specific Test Method

```bash
php artisan test --filter=testadmin_can_create_document
```

### With Coverage

```bash
php artisan test --coverage
php artisan test --coverage --min=80
```

### Verbose Output

```bash
php artisan test --verbose
```

## Test Environment

### Configuration

Tests use `.env.testing` or `phpunit.xml` environment variables:

```xml
<!-- phpunit.xml -->
<php>
    <env name="APP_ENV" value="testing"/>
    <env name="DB_CONNECTION" value="mysql"/>
    <env name="DB_DATABASE" value="document_tracker_test"/>
    <env name="DB_USERNAME" value="root"/>
    <env name="DB_PASSWORD" value="041601"/>
    <env name="CACHE_STORE" value="array"/>
    <env name="SESSION_DRIVER" value="array"/>
    <env name="QUEUE_CONNECTION" value="sync"/>
    <env name="MAIL_MAILER" value="array"/>
</php>
```

### Database

- Uses MySQL test database (`document_tracker_test`)
- Migrations run before each test suite
- `RefreshDatabase` trait resets database per test

### Key Traits

```php
use RefreshDatabase;  // Reset database per test
use WithFaker;        // Access to Faker instance
```

## Writing Tests

### Feature Test Template

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Document;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create roles
        $adminRole = Role::factory()->create(['slug' => 'admin']);
        $staffRole = Role::factory()->create(['slug' => 'staff']);

        // Create users with roles
        $this->admin = User::factory()->create(['email' => 'admin@test.com']);
        $this->admin->roles()->attach($adminRole);

        $this->staff = User::factory()->create(['email' => 'staff@test.com']);
        $this->staff->roles()->attach($staffRole);
    }

    /** @test */
    public function admin_can_create_document(): void
    {
        $data = [
            'title' => 'Test Document',
            'document_type' => 'Memorandum',
            'processing_hours' => 24,
            'notes' => 'Test notes',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('system.documents.store'), $data);

        $response->assertRedirect(route('system.documents.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('documents', [
            'title' => 'Test Document',
            'creator_id' => $this->admin->id,
        ]);
    }

    /** @test */
    public function guest_cannot_access_documents(): void
    {
        $response = $this->get(route('system.documents.index'));
        $response->assertRedirect(route('login.form'));
    }
}
```

### Unit Test Template

```php
<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_full_name_accessor_works(): void
    {
        $user = User::factory()->create([
            'firstname' => 'John',
            'middlename' => 'Middle',
            'lastname' => 'Doe',
        ]);

        $this->assertEquals('John Middle Doe', $user->full_name);
    }

    /** @test */
    public function user_has_role_method_works(): void
    {
        $user = User::factory()->create();
        $role = Role::factory()->create(['slug' => 'admin']);
        $user->roles()->attach($role);

        $this->assertTrue($user->hasRole('admin'));
        $this->assertFalse($user->hasRole('staff'));
    }
}
```

## Testing Patterns

### 1. Authentication Testing

```php
// Test authenticated access
$response = $this->actingAs($user)->get(route('system.documents.index'));
$response->assertStatus(200);

// Test guest redirect
$response = $this->get(route('system.documents.index'));
$response->assertRedirect(route('login.form'));

// Test role-based access
$response = $this->actingAs($staff)->get(route('system.users.index'));
$response->assertStatus(403);
```

### 2. Form Validation Testing

```php
// Valid data
$response = $this->actingAs($admin)->post(route('system.documents.store'), [
    'title' => 'Valid Title',
    'document_type' => 'Memorandum',
    'processing_hours' => 24,
]);
$response->assertRedirect();

// Invalid data - missing required fields
$response = $this->actingAs($admin)->post(route('system.documents.store'), []);
$response->assertSessionHasErrors(['title', 'document_type', 'processing_hours']);

// Invalid data - unique constraint
$response = $this->actingAs($admin)->post(route('system.users.store'), [
    'email' => 'existing@test.com', // Already exists
]);
$response->assertSessionHasErrors('email');
```

### 3. Database Assertions

```php
// Record exists
$this->assertDatabaseHas('documents', [
    'title' => 'Test Document',
    'creator_id' => $admin->id,
]);

// Record doesn't exist
$this->assertDatabaseMissing('documents', [
    'title' => 'Deleted Document',
]);

// Count records
$this->assertDatabaseCount('documents', 5);

// Soft deleted
$this->assertSoftDeleted('documents', ['id' => $document->id]);
```

### 4. JSON API Testing

```php
$response = $this->actingAs($user)
    ->postJson(route('api.documents.lookup'), ['code' => 'QR123']);

$response->assertStatus(200)
    ->assertJsonStructure([
        'found',
        'document' => ['id', 'title'],
    ])
    ->assertJson(['found' => true]);
```

### 5. File Upload Testing

```php
$file = UploadedFile::fake()->image('document.jpg', 100, 100);

$response = $this->actingAs($admin)
    ->post(route('system.documents.store'), [
        'title' => 'Doc with Image',
        'document_type' => 'Memorandum',
        'processing_hours' => 24,
        'attachment' => $file,
    ]);

$response->assertRedirect();
$this->assertDatabaseHas('documents', ['title' => 'Doc with Image']);
Storage::disk('local')->assertExists('documents/' . $file->hashName());
```

### 6. Event Testing

```php
// Assert event dispatched
Event::fake();

$response = $this->actingAs($admin)->post(route('system.documents.store'), $data);

Event::assertDispatched(DocumentCreated::class, function ($event) {
    return $event->document->title === 'Test Document';
});
```

### 7. Notification Testing

```php
Notification::fake();

$response = $this->actingAs($admin)->post(route('password.email'), [
    'email' => 'user@test.com'
]);

Notification::assertSentTo($user, ResetPasswordNotification::class);
```

### 8. Queue Job Testing

```php
Queue::fake();

$response = $this->actingAs($admin)->post(route('system.documents.store'), $data);

Queue::assertPushed(SendDocumentNotification::class, function ($job) {
    return $job->document->title === 'Test Document';
});
```

### 9. Time Testing

```php
// Freeze time
Carbon::setTestNow(now()->addDays(5));

// Test time-dependent logic
$document = Document::factory()->create(['due_date' => now()->addDays(3)]);
$this->assertTrue($document->isOverdue());

// Restore
Carbon::setTestNow();
```

## Test Data Factories

### Using Factories

```php
// Simple creation
$user = User::factory()->create();

// With specific attributes
$user = User::factory()->create([
    'email' => 'specific@test.com',
    'status' => 'active',
]);

// Using states
$bannedUser = User::factory()->banned()->create();
$lockedUser = User::factory()->locked()->create();

// Relationships
$document = Document::factory()->for($user, 'creator')->create();

// Multiple
$users = User::factory()->count(5)->create();
```

### Creating Related Data

```php
// Has many
$documents = Document::factory()->count(3)->create([
    'creator_id' => $user->id,
]);

// Belongs to many
$role = Role::factory()->create();
$user->roles()->attach($role);

// Has one
$track = DocumentTrack::factory()->create([
    'document_id' => $document->id,
    'user_id' => $user->id,
]);
```

## Running Tests in CI

### GitHub Actions

```yaml
- name: Run tests
  run: php artisan test --env=testing
  env:
    DB_CONNECTION: mysql
    DB_HOST: 127.0.0.1
    DB_PORT: 3306
    DB_DATABASE: document_tracker_test
    DB_USERNAME: root
    DB_PASSWORD: 041601
```

### Parallel Testing (Optional)

```bash
# Install parallel testing
composer require --dev brianium/paratest

# Run in parallel
./vendor/bin/paratest --processes=4
```

## Debugging Tests

### Dump Variables

```php
/** @test */
public function test_something(): void
{
    $response = $this->actingAs($admin)->post(route('system.documents.store'), $data);
    
    // Dump response
    dd($response->getContent());
    
    // Dump session
    dd(session()->all());
    
    // Dump database
    dd(DB::table('documents')->get());
}
```

### Debug Test Database

```bash
# Run single test with database output
php artisan test --filter=testadmin_can_create_document --verbose
```

### SQL Query Logging

```php
DB::enableQueryLog();

// ... run test code ...

dd(DB::getQueryLog());
```

## Common Issues

### Test Database Locked

```bash
# Reset test database
php artisan migrate:fresh --env=testing --force
```

### Foreign Key Constraints

```php
// Disable foreign keys in test setup
DB::statement('SET FOREIGN_KEY_CHECKS=0;');
// ... test code ...
DB::statement('SET FOREIGN_KEY_CHECKS=1;');
```

### Slow Tests

```php
// Use --filter to run specific tests
php artisan test --filter=DocumentManagementTest

// Disable refresh database for specific tests
// (only if tests don't modify database)
```

### Faker Locale

```php
// In test setup
$this->faker = \Faker\Factory::create('en_PH');
```

## Test Coverage Goals

| Component | Target |
|-----------|--------|
| Models | 90%+ |
| Controllers | 80%+ |
| Services | 90%+ |
| Middleware | 70%+ |
| Overall | 80%+ |

## Continuous Integration

### Pre-commit Hooks

```bash
# .git/hooks/pre-commit
#!/bin/bash
php artisan test --filter="!Integration"
./vendor/bin/pint --test
./vendor/bin/phpstan analyse
composer audit
```

### Coverage Reports

```bash
# Generate HTML coverage report
php artisan test --coverage --coverage-html=coverage-report

# View in browser
open coverage-report/index.html
```

## Best Practices

1. **One assertion per test** - Keep tests focused
2. **Descriptive names** - `test_admin_can_create_document_with_valid_data`
3. **AAA Pattern** - Arrange, Act, Assert
4. **Isolate tests** - Use `RefreshDatabase` trait
5. **Test behavior, not implementation** - Test what user sees
6. **Use factories** - Don't hardcode test data
7. **Test edge cases** - Empty data, boundaries, errors
8. **Keep tests fast** - Avoid external API calls
9. **Group related tests** - Use `describe()` or test classes
10. **Document complex tests** - Add comments for clarity