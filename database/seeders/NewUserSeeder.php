<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'id' => 1,
                'registration_id' => 'ADM01',
                'name' => 'Administrator',
                'email' => 'admin@admin.com',
                'email_verified_at' => '2025-06-23 04:55:06',
                'password' => '$2y$10$TRhzLAV1wP.IjrpLAe6T6eeg2uw9J3fNwlc/xF/KlZklkeWpcKSd6',
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'phone' => '082121212121',
                'address' => null,
                'signature_path' => null,
                'paraf_path' => null,
                'role' => 'admin',
                'is_active' => 1,
                'profile_picture' => null,
                'remember_token' => 'b0KlSFqz7aDQudhyzTHVAQp1lhE98MgSmlqWET4scah78nwe5lLJyZrzQopG',
                'created_at' => '2025-06-23 04:55:06',
                'updated_at' => '2025-06-23 04:55:06',
                'jabatan_id' => null,
                'jabatan_full' => null,
                'department_id' => null,
                'directorate_id' => null,
                'division_id' => null,
                'superior_id' => null,
                'golongan' => null,
                'nik' => null
            ],
            [
                'id' => 48,
                'registration_id' => 'DIR001',
                'name' => 'Dewi Director',
                'email' => 'TEST@GMAIL.COM',
                'email_verified_at' => null,
                'password' => '$2y$10$DjeExht8y6bI2lMEYJP1UOmGqacRPa4jL.hf1A/NjiBDBa3pX6iR2',
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'phone' => '08111111001',
                'address' => 'Jl. Direksi No.1',
                'signature_path' => null,
                'paraf_path' => null,
                'role' => 'admin',
                'is_active' => 1,
                'profile_picture' => null,
                'remember_token' => null,
                'created_at' => '2025-07-07 06:13:17',
                'updated_at' => '2025-07-11 07:45:04',
                'jabatan_id' => 1,
                'jabatan_full' => null,
                'department_id' => null,
                'directorate_id' => 1,
                'division_id' => null,
                'superior_id' => null,
                'golongan' => '00',
                'nik' => '3173000000000001'
            ],
            [
                'id' => 49,
                'registration_id' => 'ASDIR001',
                'name' => 'Arief Assistant',
                'email' => 'asdir@gmail.com',
                'email_verified_at' => null,
                'password' => '$2y$10$BhayvALSwTi.vUXmICMK9eYkNoZCYzCQubfU7zwSrKN8z8MZ.9rHO',
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'phone' => '08111111002',
                'address' => 'Jl. HC No.1',
                'signature_path' => null,
                'paraf_path' => null,
                'role' => 'admin',
                'is_active' => 1,
                'profile_picture' => null,
                'remember_token' => null,
                'created_at' => '2025-07-07 06:13:17',
                'updated_at' => '2025-07-11 07:45:20',
                'jabatan_id' => 2,
                'jabatan_full' => null,
                'department_id' => null,
                'directorate_id' => 1,
                'division_id' => null,
                'superior_id' => 48,
                'golongan' => '01',
                'nik' => '3173000000000002'
            ]
            // Tambahkan data user lainnya dari users.sql di sini
        ];

        foreach ($users as $user) {
            DB::table('users')->insert($user);
        }
    }
    }
}
