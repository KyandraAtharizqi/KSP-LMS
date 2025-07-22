<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daftar_hadir_pelatihan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pelatihan_id'); // FK ke surat_pengajuan_pelatihans
            $table->unsignedBigInteger('participant_id'); // FK ke training_participants
            $table->date('date'); // Hari pelatihan
            $table->time('check_in_time')->nullable(); // Waktu masuk
            $table->timestamp('check_in_timestamp')->nullable(); // Timestamp dari GForm
            $table->string('check_in_photo')->nullable(); // Link foto dari GForm
            $table->time('check_out_time')->nullable(); // Waktu keluar
            $table->timestamp('check_out_timestamp')->nullable(); // Timestamp dari GForm
            $table->string('check_out_photo')->nullable(); // Link foto keluar
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['pelatihan_id', 'participant_id', 'date']);
            $table->foreign('pelatihan_id')->references('id')->on('surat_pengajuan_pelatihans')->onDelete('cascade');
            $table->foreign('participant_id')->references('id')->on('training_participants')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daftar_hadir_pelatihan');
    }
};
