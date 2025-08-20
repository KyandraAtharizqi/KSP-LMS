<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Insert default signature config rows if they don't exist
        $configs = [
            'pengajuan_pelatihan_signature_2_default',
            'pengajuan_pelatihan_signature_3_default'
        ];
        
        foreach ($configs as $code) {
            if (!DB::table('configs')->where('code', $code)->exists()) {
                DB::table('configs')->insert([
                    'code' => $code,
                    'value' => '', // you can update this later from UI
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('configs')->whereIn('code', [
            'pengajuan_pelatihan_signature_2_default',
            'pengajuan_pelatihan_signature_3_default',
        ])->delete();
    }
};
