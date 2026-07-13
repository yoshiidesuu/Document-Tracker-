<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->boolean('is_private')->default(false)->after('barcode_value');
            $table->string('access_key', 255)->nullable()->after('is_private');
            $table->string('arta_category', 50)->default('simple')->after('access_key');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['is_private', 'access_key', 'arta_category']);
        });
    }
};
