<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePelatihanPresentersTable extends Migration
{
    public function up(): void
    {
        Schema::create('pelatihan_presenters', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('pelatihan_id'); // Replaces daftar_hadir_pelatihan_id
            $table->unsignedBigInteger('presenter_id')->nullable(); // External presenters
            $table->unsignedBigInteger('user_id')->nullable(); // Internal presenters

            $table->enum('type', ['internal', 'external']);
            $table->date('date'); // Renamed from 'tanggal' to 'date'
            $table->timestamps();

            $table->foreign('pelatihan_id')
                ->references('id')->on('surat_pengajuan_pelatihans')
                ->onDelete('cascade');

            $table->foreign('presenter_id')
                ->references('id')->on('presenters')
                ->onDelete('set null');

            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('pelatihan_presenters');
        Schema::enableForeignKeyConstraints();
    }
}
