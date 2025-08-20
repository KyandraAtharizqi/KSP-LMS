<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('evaluasi_level_1')) {
            Schema::create('evaluasi_level_1', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pelatihan_id')->constrained('surat_pengajuan_pelatihans')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            // Index untuk optimasi query
            $table->index(['pelatihan_id', 'user_id'], 'eval_pel_user_idx');
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('evaluasi_level_1');
    }
};
