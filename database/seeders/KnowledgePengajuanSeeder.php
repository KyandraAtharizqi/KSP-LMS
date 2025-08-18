<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\PengajuanKnowledge;

class KnowledgePengajuanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::first();

        PengajuanKnowledge::create([
            'kode' => 'KSH-2025-001',
            'created_by' => $admin->id,
            'kepada' => 'Kepala Divisi SDM',
            'dari' => 'Staff IT',
            'perihal' => 'Knowledge Sharing Laravel',
            'judul' => 'Pengenalan Framework Laravel untuk Pemula',
            'pemateri' => 'John Doe',
            'tanggal_mulai' => now()->addDays(7),
            'tanggal_selesai' => now()->addDays(7)->addHours(2),
            'peserta' => json_encode(['Divisi IT', 'Divisi SDM']),
            'lampiran' => 'materi-laravel.pdf',
            'status' => 'pending'
        ]);

        PengajuanKnowledge::create([
            'kode' => 'KSH-2025-002',
            'created_by' => $admin->id,
            'kepada' => 'Kepala Divisi IT',
            'dari' => 'Staff SDM',
            'perihal' => 'Knowledge Sharing HR System',
            'judul' => 'Pengenalan Sistem HRIS Terbaru',
            'pemateri' => 'Jane Smith',
            'tanggal_mulai' => now()->addDays(14),
            'tanggal_selesai' => now()->addDays(14)->addHours(3),
            'peserta' => json_encode(['Divisi IT', 'Divisi SDM', 'Divisi Keuangan']),
            'lampiran' => 'materi-hris.pdf',
            'status' => 'approved'
        ]);
    }
}
