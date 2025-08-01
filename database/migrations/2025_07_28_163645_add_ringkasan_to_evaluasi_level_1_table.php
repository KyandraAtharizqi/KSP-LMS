<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('evaluasi_level_1', function (Blueprint $table) {
            $table->text('ringkasan')->nullable()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('evaluasi_level_1', function (Blueprint $table) {
            $table->dropColumn('ringkasan');
        });
    }
};
