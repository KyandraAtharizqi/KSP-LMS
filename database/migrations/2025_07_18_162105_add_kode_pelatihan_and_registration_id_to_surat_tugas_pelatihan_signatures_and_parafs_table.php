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
        // Add kode_pelatihan if it doesn't exist
        if (!Schema::hasColumn('surat_tugas_pelatihan_signatures_and_parafs', 'kode_pelatihan')) {
            Schema::table('surat_tugas_pelatihan_signatures_and_parafs', function (Blueprint $table) {
                $table->string('kode_pelatihan', 50)->after('surat_tugas_id');
            });
        }

        // Add registration_id if it doesn't exist
        if (!Schema::hasColumn('surat_tugas_pelatihan_signatures_and_parafs', 'registration_id')) {
            Schema::table('surat_tugas_pelatihan_signatures_and_parafs', function (Blueprint $table) {
                $table->string('registration_id', 50)->nullable()->after('user_id');
            });
        }

        // Add indexes if they don't exist
        Schema::table('surat_tugas_pelatihan_signatures_and_parafs', function (Blueprint $table) {
            $indexes = Schema::getConnection()->getDoctrineSchemaManager()->listTableIndexes('surat_tugas_pelatihan_signatures_and_parafs');
            
            if (!isset($indexes['stp_sig_kode_idx'])) {
                $table->index('kode_pelatihan', 'stp_sig_kode_idx');
            }
            
            if (!isset($indexes['stp_sig_reg_id_idx'])) {
                $table->index('registration_id', 'stp_sig_reg_id_idx');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_tugas_pelatihan_signatures_and_parafs', function (Blueprint $table) {
            // Drop indexes first
            if ($this->hasIndex('surat_tugas_pelatihan_signatures_and_parafs', 'stp_sig_kode_idx')) {
                $table->dropIndex('stp_sig_kode_idx');
            }
            if ($this->hasIndex('surat_tugas_pelatihan_signatures_and_parafs', 'stp_sig_reg_id_idx')) {
                $table->dropIndex('stp_sig_reg_id_idx');
            }

            // Drop columns
            if (Schema::hasColumn('surat_tugas_pelatihan_signatures_and_parafs', 'kode_pelatihan')) {
                $table->dropColumn('kode_pelatihan');
            }
            if (Schema::hasColumn('surat_tugas_pelatihan_signatures_and_parafs', 'registration_id')) {
                $table->dropColumn('registration_id');
            }
        });
    }

    /**
     * Check if an index exists
     */
    private function hasIndex($table, $index)
    {
        $conn = Schema::getConnection();
        $dbSchemaManager = $conn->getDoctrineSchemaManager();
        $doctrineTable = $dbSchemaManager->listTableDetails($table);
        
        return $doctrineTable->hasIndex($index);
    }
};
