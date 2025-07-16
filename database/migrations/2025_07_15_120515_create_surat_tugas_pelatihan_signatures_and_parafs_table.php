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
        Schema::create('surat_tugas_pelatihan_signatures_and_parafs', function (Blueprint $table) {
            $table->id();
            
            // Menggunakan cara manual untuk foreign key dengan nama yang lebih pendek
            $table->unsignedBigInteger('surat_tugas_id');
            $table->foreign('surat_tugas_id', 'fk_stp_signatures_surat_tugas') // <-- Nama pendek untuk constraint
                  ->references('id')
                  ->on('surat_tugas_pelatihans')
                  ->onDelete('cascade');

            // Foreign key untuk user_id biasanya tidak masalah, tapi kita bisa membuatnya konsisten
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id', 'fk_stp_signatures_user') // <-- Nama pendek untuk constraint
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->enum('type', ['paraf', 'signature']);
            $table->integer('sequence');
            $table->integer('round')->default(1);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_tugas_pelatihan_signatures_and_parafs');
    }
};
