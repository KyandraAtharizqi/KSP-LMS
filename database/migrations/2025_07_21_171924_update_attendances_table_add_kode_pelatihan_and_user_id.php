<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Tambahkan kolom baru
            $table->string('kode_pelatihan')->nullable()->after('pelatihan_id');
            $table->string('registration_id')->nullable()->after('kode_pelatihan');

            // Tambahkan user_id
            $table->unsignedBigInteger('user_id')->nullable()->after('pelatihan_id');

            // Jika ingin ada foreign key
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });

        // Migrasi data participant_id ke user_id (jika ada tabel training_participants)
        if (Schema::hasColumn('attendances', 'participant_id')) {
            DB::statement("
                UPDATE attendances a
                JOIN training_participants tp ON a.participant_id = tp.id
                SET a.user_id = tp.user_id,
                    a.registration_id = tp.registration_id,
                    a.kode_pelatihan = tp.kode_pelatihan
            ");
        }

        // Hapus kolom participant_id setelah migrasi data
        Schema::table('attendances', function (Blueprint $table) {
            if (Schema::hasColumn('attendances', 'participant_id')) {
                $table->dropColumn('participant_id');
            }
        });
    }

    /**
     * Rollback migrasi.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Kembalikan participant_id
            $table->unsignedBigInteger('participant_id')->nullable()->after('pelatihan_id');

            // Drop foreign key dan kolom user_id
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');

            // Drop kolom kode_pelatihan dan registration_id
            $table->dropColumn(['kode_pelatihan', 'registration_id']);
        });
    }
};
