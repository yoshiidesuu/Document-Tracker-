<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('password_changed_at')->nullable()->after('password');
            $table->timestamp('locked_until')->nullable()->after('remember_token');
            $table->string('email_hash', 64)->nullable()->unique()->after('email');
            $table->timestamp('last_login_at')->nullable()->after('email_hash');
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
            $table->text('mfa_secret')->nullable()->after('last_login_ip');
            $table->boolean('mfa_enabled')->default(false)->after('mfa_secret');
            $table->timestamp('mfa_recovery_codes_generated_at')->nullable()->after('mfa_enabled');
            $table->timestamp('terms_accepted_at')->nullable()->after('mfa_recovery_codes_generated_at');
            $table->timestamp('privacy_accepted_at')->nullable()->after('terms_accepted_at');
            $table->integer('login_count')->default(0)->after('privacy_accepted_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'password_changed_at',
                'locked_until',
                'email_hash',
                'last_login_at',
                'last_login_ip',
                'mfa_secret',
                'mfa_enabled',
                'mfa_recovery_codes_generated_at',
                'terms_accepted_at',
                'privacy_accepted_at',
                'login_count',
            ]);
        });
    }
};
