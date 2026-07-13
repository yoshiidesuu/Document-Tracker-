<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_picture', 2048)->nullable()->after('remember_token');
            $table->string('id_number', 100)->nullable()->unique()->after('profile_picture');
            $table->string('firstname', 255)->nullable()->after('id_number');
            $table->string('middlename', 255)->nullable()->after('firstname');
            $table->string('lastname', 255)->nullable()->after('middlename');
            $table->tinyInteger('age')->nullable()->unsigned()->after('lastname');
            $table->string('gender', 50)->nullable()->after('age');
            $table->date('bday')->nullable()->after('gender');
            $table->boolean('locked')->default(false)->after('bday');
            $table->boolean('banned')->default(false)->after('locked');
            $table->string('status', 50)->default('active')->after('banned');
            $table->string('ip', 45)->nullable()->after('status');
            $table->json('geolocation')->nullable()->after('ip');
        });

        if (Schema::hasColumn('users', 'locked_until')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('locked_until');
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'profile_picture',
                'id_number',
                'firstname',
                'middlename',
                'lastname',
                'age',
                'gender',
                'bday',
                'locked',
                'banned',
                'status',
                'ip',
                'geolocation',
            ]);
            $table->timestamp('locked_until')->nullable()->after('remember_token');
        });
    }
};
