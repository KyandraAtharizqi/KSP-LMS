<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Insert default signature config rows
        DB::table('configs')->insert([
            [
                'code' => 'pengajuan_pelatihan_signature_2_default',
                'value' => '', // you can update this later from UI
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'pengajuan_pelatihan_signature_3_default',
                'value' => '', // you can update this later from UI
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('configs')->whereIn('code', [
            'pengajuan_pelatihan_signature_2_default',
            'pengajuan_pelatihan_signature_3_default',
        ])->delete();
    }
};
