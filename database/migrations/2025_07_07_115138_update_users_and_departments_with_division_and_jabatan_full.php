<?php

// database/migrations/xxxx_xx_xx_update_users_and_departments_with_division_and_jabatan_full.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Add division_id to departments (nullable)
        Schema::table('departments', function (Blueprint $table) {
            $table->foreignId('division_id')->nullable()->after('directorate_id')->constrained()->onDelete('set null');
        });

        // Add division_id and jabatan_full to users
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('division_id')->nullable()->after('directorate_id')->constrained()->onDelete('set null');
            $table->string('jabatan_full')->nullable()->after('jabatan_id');
        });
    }

    public function down(): void {
        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['division_id']);
            $table->dropColumn('division_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['division_id']);
            $table->dropColumn(['division_id', 'jabatan_full']);
        });
    }
};
