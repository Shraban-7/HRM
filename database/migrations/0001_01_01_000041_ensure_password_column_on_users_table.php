<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users')) {
            if (! Schema::hasColumn('users', 'password')) {
                if (Schema::hasColumn('users', 'password_hash')) {
                    Schema::table('users', function (Blueprint $table) {
                        $table->string('password')->after('username')->nullable();
                    });
                    DB::statement('UPDATE users SET password = password_hash WHERE password IS NULL');
                    Schema::table('users', function (Blueprint $table) {
                        $table->string('password')->nullable(false)->change();
                    });
                } else {
                    Schema::table('users', function (Blueprint $table) {
                        $table->string('password')->after('username');
                    });
                }
            }

            if (Schema::hasColumn('users', 'salt')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('salt')->nullable()->change();
                });
            }

            if (Schema::hasColumn('users', 'password_hash')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('password_hash')->nullable()->change();
                });
            }
        }
    }

    public function down(): void
    {
        // No-op
    }
};
