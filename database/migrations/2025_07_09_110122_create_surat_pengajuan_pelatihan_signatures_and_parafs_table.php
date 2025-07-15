<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('surat_pengajuan_pelatihan_signatures_and_parafs', function (Blueprint $table) {
            $table->id();

            // FK with custom constraint name
            $table->unsignedBigInteger('pelatihan_id');
            $table->foreign('pelatihan_id', 'fk_signatures_and_parafs_pelatihan')
                ->references('id')
                ->on('surat_pengajuan_pelatihans')
                ->onDelete('cascade');

            $table->foreignId('user_id')->constrained('users');

            // Snapshot fields
            $table->string('kode_pelatihan');      // From surat_pengajuan_pelatihans
            $table->string('registration_id');     // From users

            // Approval process
            $table->unsignedInteger('round')->default(1);
            $table->unsignedInteger('sequence');
            $table->enum('type', ['paraf', 'signature']);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->timestamp('signed_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_pengajuan_pelatihan_signatures_and_parafs');
    }
};
