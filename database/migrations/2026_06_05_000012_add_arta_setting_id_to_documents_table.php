<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('arta_setting_id')
                ->nullable()
                ->constrained('arta_settings')
                ->nullOnDelete()
                ->after('arta_category');
        });
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['arta_setting_id']);
            $table->dropColumn('arta_setting_id');
        });
    }
};
