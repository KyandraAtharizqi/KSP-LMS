<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('notifikasi')) {
            Schema::create('notifikasi', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('judul');
                $table->text('pesan');
                $table->string('link')->nullable();
                $table->boolean('dibaca')->default(false);
                $table->timestamps();
                
                // Index untuk optimasi query
                $table->index(['user_id', 'dibaca']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
