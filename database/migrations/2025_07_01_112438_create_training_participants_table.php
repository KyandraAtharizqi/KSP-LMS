<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingParticipantsTable extends Migration
{
    public function up(): void
    {
        Schema::create('training_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_id')->constrained('surat_pengajuan_pelatihan')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Snapshot of employee at time of submission
            $table->string('registration_id');
            $table->foreignId('jabatan_id')->constrained('jabatans');
            $table->foreignId('department_id')->constrained('departments');
            $table->foreignId('superior_id')->nullable()->constrained('users');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_participants');
    }
}
