<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('surat_tugas_pelatihan_signatures_and_parafs', function (Blueprint $table) {
            // Add kode_pelatihan after surat_tugas_id
            $table->string('kode_pelatihan', 50)->after('surat_tugas_id');

            // Add registration_id after user_id (nullable for flexibility)
            $table->string('registration_id', 50)->nullable()->after('user_id');

            // Add indexes for faster filtering/search with custom shorter names
            $table->index('kode_pelatihan', 'stp_sig_kode_idx');
            $table->index('registration_id', 'stp_sig_reg_id_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_tugas_pelatihan_signatures_and_parafs', function (Blueprint $table) {
            // Drop columns and indexes using custom names
            $table->dropIndex('stp_sig_kode_idx');
            $table->dropIndex('stp_sig_reg_id_idx');
            $table->dropColumn(['kode_pelatihan', 'registration_id']);
        });
    }
};
