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
            $new = ['messages', 'messages.access', 'messages.send'];

            foreach ($new as $p) {
                if (! in_array($p, $perms)) {
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
            $perms = array_values(array_filter($perms, fn ($p) => ! in_array($p, ['messages', 'messages.access', 'messages.send'])));
            $role->update(['permissions' => $perms]);
        }
    }
};
