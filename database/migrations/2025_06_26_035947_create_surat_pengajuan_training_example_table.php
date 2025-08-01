<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('surat_pengajuan_training_example', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->date('training_date');
            $table->unsignedBigInteger('submitted_by');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_pengajuan_training_example');
    }
};