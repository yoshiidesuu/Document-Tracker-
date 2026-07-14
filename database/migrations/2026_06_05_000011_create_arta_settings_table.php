<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arta_settings', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('days')->nullable();
            $table->unsignedInteger('hours')->nullable();
            $table->unsignedInteger('minutes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('duration_label')->nullable();
            $table->timestamps();

            $table->unique(['category', 'title']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arta_settings');
    }
};
