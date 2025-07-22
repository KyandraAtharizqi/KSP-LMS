<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daftar_hadir_pelatihan_status', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pelatihan_id'); // FK ke surat_pengajuan_pelatihans
            $table->date('date'); // Tanggal pelatihan (per hari)
            $table->boolean('is_submitted')->default(false);
            $table->timestamp('submitted_at')->nullable();
            $table->unsignedBigInteger('submitted_by')->nullable(); // FK ke users
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['pelatihan_id', 'date']);
            $table->foreign('pelatihan_id')->references('id')->on('surat_pengajuan_pelatihans')->onDelete('cascade');
            $table->foreign('submitted_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daftar_hadir_pelatihan_status');
    }
};
