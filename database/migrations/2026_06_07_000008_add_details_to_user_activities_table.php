<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_activities', function (Blueprint $table) {
            $table->json('geolocation')->nullable()->after('ip_address');
            $table->json('old_data')->nullable()->after('geolocation');
            $table->json('new_data')->nullable()->after('old_data');
            $table->string('user_agent', 500)->nullable()->after('new_data');
        });
    }

    public function down(): void
    {
        Schema::table('user_activities', function (Blueprint $table) {
            $table->dropColumn(['geolocation', 'old_data', 'new_data', 'user_agent']);
        });
    }
};
