<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PengajuanKnowledge;
use App\Models\User;

class KnowledgeStatusTestSeeder extends Seeder
{
    public function run()
    {
        $user = User::first();
        
        // Create pending knowledge sharing
        PengajuanKnowledge::create([
            'kode' => 'KS-TEST-001',
            'created_by' => $user->id,
            'kepada' => 'Test Kepada',
            'dari' => 'Test Dari',
            'perihal' => 'Test Perihal',
            'judul' => 'Test Judul Pending',
            'pemateri' => 'Test Pemateri',
            'tanggal' => now(),
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDays(1),
            'peserta' => json_encode([]),
            'status' => 'pending'
        ]);
        
        // Create rejected knowledge sharing
        PengajuanKnowledge::create([
            'kode' => 'KS-TEST-002',
            'created_by' => $user->id,
            'kepada' => 'Test Kepada',
            'dari' => 'Test Dari',
            'perihal' => 'Test Perihal',
            'judul' => 'Test Judul Rejected',
            'pemateri' => 'Test Pemateri',
            'tanggal' => now(),
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now()->addDays(1),
            'peserta' => json_encode([]),
            'status' => 'rejected',
            'rejection_reason' => 'Test rejection reason'
        ]);
        
        echo "Knowledge status test data created successfully!\n";
    }
}
