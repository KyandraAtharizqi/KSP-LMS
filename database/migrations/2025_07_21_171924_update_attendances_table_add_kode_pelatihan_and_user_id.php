<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('daftar_hadir_pelatihan', function (Blueprint $table) {
            // Drop the foreign key first
            $table->dropForeign(['participant_id']);

            // Then drop the column
            $table->dropColumn('participant_id');

            // Add new columns
            $table->string('kode_pelatihan')->nullable()->after('pelatihan_id');
            $table->unsignedBigInteger('user_id')->nullable()->after('kode_pelatihan');
            $table->string('registration_id')->nullable()->after('user_id');

            // Add foreign key to user table
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('daftar_hadir_pelatihan', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['kode_pelatihan', 'user_id', 'registration_id']);
            $table->unsignedBigInteger('participant_id')->nullable();
            // Note: you'd need to restore the old foreign key if necessary
        });
    }
};
