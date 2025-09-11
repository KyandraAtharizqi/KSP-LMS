<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PengajuanKnowledge;
use App\Models\User;

class TestJamKnowledgeSeeder extends Seeder
{
    public function run()
    {
        $user = User::first();
        
        $pengajuan = PengajuanKnowledge::create([
            'kode' => 'TEST-JAM-001',
            'created_by' => $user->id,
            'kepada' => 'Test Kepada',
            'dari' => $user->name,
            'perihal' => 'Test Perihal',
            'judul' => 'Test dengan Jam',
            'pemateri' => 'Test Pemateri',
            'tanggal' => '2025-09-10',
            'tanggal_mulai' => '2025-09-10',
            'tanggal_selesai' => '2025-09-10',
            'jam_mulai' => '09:00:00',
            'jam_selesai' => '17:00:00',
            'peserta' => json_encode([]),
            'status' => 'pending'
        ]);
        
        echo "Data berhasil dibuat dengan ID: {$pengajuan->id}\n";
        echo "Jam mulai: {$pengajuan->jam_mulai}\n";
        echo "Jam selesai: {$pengajuan->jam_selesai}\n";
    }
}
