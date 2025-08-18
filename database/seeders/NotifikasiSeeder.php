<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notifikasi;
use App\Models\User;

class NotifikasiSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('registration_id', 'ADM01')->first();
        
        if ($admin) {
            // Beberapa notifikasi untuk testing
            Notifikasi::create([
                'user_id' => $admin->id,
                'judul' => 'Selamat Datang',
                'pesan' => 'Selamat datang di sistem KSP-LMS!',
                'dibaca' => false,
                'created_at' => now()
            ]);

            Notifikasi::create([
                'user_id' => $admin->id,
                'judul' => 'Panduan Penggunaan',
                'pesan' => 'Silakan baca panduan penggunaan sistem di menu Help.',
                'link' => '/help',
                'dibaca' => false,
                'created_at' => now()->addMinutes(5)
            ]);

            Notifikasi::create([
                'user_id' => $admin->id,
                'judul' => 'Notifikasi Lama',
                'pesan' => 'Ini adalah notifikasi yang sudah dibaca.',
                'dibaca' => true,
                'created_at' => now()->subDays(1)
            ]);
        }
    }
}
