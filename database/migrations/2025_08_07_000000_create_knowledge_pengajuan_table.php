<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('knowledge_pengajuan', function (Blueprint $table) {
            $table->id();
            $table->string('kode');
            $table->foreignId('created_by')->constrained('users');
            $table->string('kepada');
            $table->string('dari');
            $table->string('perihal');
            $table->string('judul');
            $table->string('pemateri');
            $table->date('tanggal');
            $table->json('peserta')->nullable();
            $table->string('lampiran')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            // Index untuk optimasi query
            $table->index('status');
            $table->index('tanggal');
        });
    }

    public function down()
    {
        Schema::dropIfExists('knowledge_pengajuan');
    }
};
