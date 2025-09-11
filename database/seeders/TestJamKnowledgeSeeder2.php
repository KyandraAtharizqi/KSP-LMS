<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PengajuanKnowledge;
use App\Models\User;

class TestJamKnowledgeSeeder2 extends Seeder
{
    public function run()
    {
        $user = User::first();
        
        $pengajuan = PengajuanKnowledge::create([
            'kode' => 'TEST-JAM-002',
            'created_by' => $user->id,
            'kepada' => 'Test Kepada',
            'dari' => $user->name,
            'perihal' => 'Test Perihal Tanpa Kolom Tanggal',
            'judul' => 'Test dengan Jam Setelah Remove Tanggal',
            'pemateri' => 'Test Pemateri',
            'tanggal_mulai' => '2025-09-10',
            'tanggal_selesai' => '2025-09-10',
            'jam_mulai' => '08:30:00',
            'jam_selesai' => '16:30:00',
            'peserta' => json_encode([]),
            'status' => 'pending'
        ]);
        
        echo "Data berhasil dibuat dengan ID: {$pengajuan->id}\n";
        echo "Tanggal mulai: {$pengajuan->tanggal_mulai}\n";
        echo "Tanggal selesai: {$pengajuan->tanggal_selesai}\n";
        echo "Jam mulai: {$pengajuan->jam_mulai}\n";
        echo "Jam selesai: {$pengajuan->jam_selesai}\n";
        
        // Test data lama masih bisa diakses
        $dataLama = PengajuanKnowledge::where('kode', 'TEST-JAM-001')->first();
        if ($dataLama) {
            echo "\nData lama masih ada:\n";
            echo "Jam mulai lama: {$dataLama->jam_mulai}\n";
            echo "Jam selesai lama: {$dataLama->jam_selesai}\n";
        }
    }
}
