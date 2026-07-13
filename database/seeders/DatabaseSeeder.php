<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $allPermissions = collect(config('permissions'))->flatMap(function ($item, $key) {
            return array_merge([$key], array_keys($item['features']));
        })->values()->all();

        $adminRole = Role::firstOrCreate(['slug' => 'admin'], [
            'name' => 'Administrator',
            'description' => 'Full system access',
            'is_system' => true,
            'permissions' => $allPermissions,
        ]);
        $adminRole->update(['permissions' => $allPermissions]);

        Role::firstOrCreate(['slug' => 'staff'], [
            'name' => 'Staff',
            'description' => 'Standard staff access',
            'is_system' => true,
            'permissions' => ['dashboard', 'dashboard.access', 'messages', 'messages.access', 'messages.send', 'users', 'users.list', 'users.view'],
        ]);

        Role::firstOrCreate(['slug' => 'viewer'], [
            'name' => 'Viewer',
            'description' => 'Read-only access',
            'is_system' => true,
            'permissions' => ['dashboard', 'dashboard.access'],
        ]);

        $admin = User::firstOrCreate(
            ['email' => 'admin@document-tracker.com'],
            [
                'firstname' => 'Admin',
                'lastname' => 'User',
                'name' => 'Admin User',
                'id_number' => 'ADM-0001',
                'email_verified_at' => now(),
                'password' => Hash::make('Str0ng!Admin#2026'),
                'password_changed_at' => now(),
                'status' => 'active',
                'locked' => false,
                'banned' => false,
                'gender' => 'prefer-not-to-say',
                'age' => 30,
                'bday' => '1996-01-01',
            ]
        );
        if (!$admin->roles()->where('role_id', $adminRole->id)->exists()) {
            $admin->roles()->attach($adminRole);
        }

        $sysAdmin = User::where('email', 'admin@gmail.com')->first();
        if ($sysAdmin && !$sysAdmin->roles()->where('role_id', $adminRole->id)->exists()) {
            $sysAdmin->roles()->attach($adminRole);
        }

        $staffRole = Role::where('slug', 'staff')->first();

        if (!User::where('email', 'test@example.com')->exists()) {
            $testUser = User::factory()->create([
                'firstname' => 'Test',
                'lastname' => 'User',
                'name' => 'Test User',
                'email' => 'test@example.com',
                'id_number' => 'TST-0001',
            ]);
            $testUser->roles()->attach($staffRole);
        }

        if (User::count() < 3) {
            User::factory(5)->create()->each(function ($user) use ($staffRole) {
                $user->roles()->attach($staffRole);
            });
        }
    }
}
