<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('knowledge_pengajuan')) {
            Schema::create('knowledge_pengajuan', function (Blueprint $table) {
            $table->id();
            $table->string('kode');
            $table->foreignId('created_by')->constrained('users');
            $table->string('kepada');
            $table->string('dari');
            $table->string('perihal');
            $table->string('judul');
            $table->string('pemateri');
            $table->datetime('tanggal_mulai');
            $table->datetime('tanggal_selesai');
            $table->json('peserta')->nullable();
            $table->string('lampiran')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            // Index untuk optimasi query
            $table->index('status');
            $table->index('tanggal_mulai');
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('knowledge_pengajuan');
    }
};
