<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pengajuan_knowledge', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->nullable();
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
        });
    }

    public function down()
    {
        Schema::dropIfExists('pengajuan_knowledge');
    }
};
