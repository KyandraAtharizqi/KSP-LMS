<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('daftar_hadir', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nota_dinas_id')->constrained('knowledge_pengajuan')->onDelete('cascade');
            $table->date('tanggal');
            $table->json('peserta')->nullable();
            $table->timestamps();

            // Index untuk optimasi query
            $table->index(['nota_dinas_id', 'tanggal'], 'daftar_hadir_nota_tgl_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('daftar_hadir');
    }
};