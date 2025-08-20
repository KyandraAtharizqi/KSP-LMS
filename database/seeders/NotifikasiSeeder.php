<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Notifikasi;
use App\Models\User;

class NotifikasiSeeder extends Seeder
{
    public function run(): void
    {
        // Get all users
        $users = User::all();
        
        foreach($users as $user) {
            // Notifikasi selamat datang untuk setiap user
            Notifikasi::create([
                'user_id' => $user->id,
                'judul' => 'Selamat Datang',
                'pesan' => 'Selamat datang di sistem KSP-LMS!',
                'dibaca' => false,
                'created_at' => now()
            ]);

            Notifikasi::create([
                'user_id' => $user->id,
                'judul' => 'Panduan Penggunaan',
                'pesan' => 'Silakan baca panduan penggunaan sistem di menu Help.',
                'link' => '/help',
                'dibaca' => false,
                'created_at' => now()->addMinutes(5)
            ]);

            Notifikasi::create([
                'user_id' => $user->id,
                'judul' => 'Notifikasi Lama',
                'pesan' => 'Ini adalah notifikasi yang sudah dibaca.',
                'dibaca' => true,
                'created_at' => now()->subDays(1)
            ]);
        }
    }
}
