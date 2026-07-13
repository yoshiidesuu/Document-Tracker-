<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $roles = Role::where('is_system', true)->get();

        foreach ($roles as $role) {
            $perms = $role->permissions ?? [];
            $add = ['activity-logs', 'activity-logs.access'];

            foreach ($add as $p) {
                if (!in_array($p, $perms)) {
                    $perms[] = $p;
                }
            }

            $role->update(['permissions' => $perms]);
        }
    }

    public function down(): void
    {
        $roles = Role::where('is_system', true)->get();

        foreach ($roles as $role) {
            $perms = $role->permissions ?? [];
            $perms = array_values(array_filter($perms, fn ($p) => !in_array($p, ['activity-logs', 'activity-logs.access'])));
            $role->update(['permissions' => $perms]);
        }
    }
};
