<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('surat_tugas_pelatihans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pelatihan_id'); // FK to surat_pengajuan_pelatihans.id
            $table->string('kode_pelatihan');           // duplicate from surat_pengajuan_pelatihans
            $table->string('judul');                    // duplicate from surat_pengajuan_pelatihans
            $table->date('tanggal');                    // surat tugas creation date
            $table->string('tempat');                   // duplicate from surat_pengajuan_pelatihans
            $table->date('tanggal_pelatihan');          // duplicate from surat_pengajuan_pelatihans.tanggal_mulai
            $table->unsignedInteger('durasi');          // duplicate from surat_pengajuan_pelatihans.durasi
            $table->unsignedBigInteger('created_by')->nullable(); // creator of surat tugas
            $table->string('status')->default('draft');
            $table->boolean('is_accepted')->default(false);
            $table->timestamps();

            $table->foreign('pelatihan_id')
                ->references('id')
                ->on('surat_pengajuan_pelatihans')
                ->onDelete('cascade');

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_tugas_pelatihans');
    }
};
