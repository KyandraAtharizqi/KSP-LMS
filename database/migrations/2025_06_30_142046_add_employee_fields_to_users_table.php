<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'registration_id')) {
                $table->string('registration_id')->unique()->after('id');
            }

            if (!Schema::hasColumn('users', 'jabatan_id')) {
                $table->foreignId('jabatan_id')->nullable()->constrained('jabatans')->after('registration_id');
            }

            if (!Schema::hasColumn('users', 'department_id')) {
                $table->foreignId('department_id')->nullable()->constrained('departments')->after('jabatan_id');
            }

            if (!Schema::hasColumn('users', 'superior_id')) {
                $table->foreignId('superior_id')->nullable()->constrained('users')->after('department_id');
            }

            if (!Schema::hasColumn('users', 'nik')) {
                $table->string('nik')->nullable()->after('superior_id');
            }

            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // drop FK kalau kolomnya memang ada
            if (Schema::hasColumn('users', 'jabatan_id')) {
                $table->dropForeign(['jabatan_id']);
            }
            if (Schema::hasColumn('users', 'department_id')) {
                $table->dropForeign(['department_id']);
            }
            if (Schema::hasColumn('users', 'superior_id')) {
                $table->dropForeign(['superior_id']);
            }

            // drop kolom dengan aman
            $columns = ['registration_id','jabatan_id','department_id','superior_id','nik','address'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
