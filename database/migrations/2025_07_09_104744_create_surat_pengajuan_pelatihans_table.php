<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('surat_pengajuan_pelatihans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('kode_pelatihan')->unique();

            $table->string('kompetensi');
            $table->string('judul');
            $table->enum('lokasi', ['Perusahaan', 'Didalam Kota', 'Diluar Kota', 'Diluar Negeri']);
            $table->enum('instruktur', ['Internal', 'Eksternal']);
            $table->enum('sifat', ['Seminar', 'Kursus', 'Sertifikasi', 'Workshop']);
            $table->enum('kompetensi_wajib', ['Wajib', 'Tidak Wajib']);
            $table->text('materi_global');
            $table->enum('program_pelatihan_ksp', ['Termasuk', 'Tidak Termasuk']);
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('durasi');
            $table->string('tempat');
            $table->string('penyelenggara');
            $table->string('biaya');
            $table->enum('per_paket_or_orang', ['Paket', 'Orang']);
            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_pengajuan_pelatihans');
    }
};
