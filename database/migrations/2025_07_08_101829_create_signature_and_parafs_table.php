<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('signature_and_parafs', function (Blueprint $table) {
            $table->id();
            $table->string('registration_id')->unique();
            $table->string('signature_path')->nullable();
            $table->string('paraf_path')->nullable();
            $table->timestamps();

            $table->foreign('registration_id')->references('registration_id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signature_and_parafs');
    }
};
