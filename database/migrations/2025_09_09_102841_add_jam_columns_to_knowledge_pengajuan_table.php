<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('knowledge_pengajuan', function (Blueprint $table) {
            $table->time('jam_mulai')->nullable()->after('tanggal_selesai');
            $table->time('jam_selesai')->nullable()->after('jam_mulai');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('knowledge_pengajuan', function (Blueprint $table) {
            $table->dropColumn(['jam_mulai', 'jam_selesai']);
        });
    }
};
