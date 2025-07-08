<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add custom registration ID
            $table->string('registration_id')->unique()->after('id');

            // Foreign keys
            $table->foreignId('jabatan_id')->nullable()->constrained('jabatans')->after('registration_id');
            $table->foreignId('department_id')->nullable()->constrained('departments')->after('jabatan_id');
            $table->foreignId('superior_id')->nullable()->constrained('users')->after('department_id');

            // Optional profile info
            $table->string('nik')->nullable()->after('superior_id');
            $table->text('address')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['jabatan_id']);
            $table->dropForeign(['department_id']);
            $table->dropForeign(['superior_id']);

            $table->dropColumn([
                'registration_id',
                'jabatan_id',
                'department_id',
                'superior_id',
                'nik',
                'address',
            ]);
        });
    }
};