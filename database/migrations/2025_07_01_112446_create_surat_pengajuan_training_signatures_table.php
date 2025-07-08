<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuratPengajuanTrainingSignaturesTable extends Migration
{
    public function up(): void
    {
        Schema::create('surat_pengajuan_training_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surat_id')->constrained('surat_pengajuan_pelatihan')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');

            $table->enum('role_type', ['mengusulkan', 'mengetahui', 'menyetujui']);
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->timestamp('signed_at')->nullable();
            $table->text('catatan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_pengajuan_training_signatures');
    }
}
