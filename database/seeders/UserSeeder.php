<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Director
        User::create([
            'registration_id' => 'DIR001',
            'name' => 'Dewi Director',
            'email' => null,
            'phone' => '08111111001',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'jabatan_id' => 1, // Director
            'directorate_id' => 1, // Presiden Direction
            'superior_id' => null,
            'nik' => '3173000000000001',
            'address' => 'Jl. Direksi No.1',
            'is_active' => true,
        ]);

        // 2. Assistant Director
        User::create([
            'registration_id' => 'ADIR001',
            'name' => 'Arief Assistant',
            'email' => null,
            'phone' => '08111111002',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'jabatan_id' => 2, // Assistant Director
            'directorate_id' => 2, // Human Capital & Finance
            'superior_id' => null,
            'nik' => '3173000000000002',
            'address' => 'Jl. HC No.1',
            'is_active' => true,
        ]);

        // 3. General Manager - Commercial Property
        User::create([
            'registration_id' => 'GM001',
            'name' => 'Gita GM CP',
            'email' => null,
            'phone' => '08111111003',
            'password' => Hash::make('password'),
            'role' => 'department_admin',
            'jabatan_id' => 3, // General Manager
            'directorate_id' => 2,
            'division_id' => 1, // Commercial Property
            'superior_id' => null,
            'nik' => '3173000000000003',
            'address' => 'Jl. Komersial No.1',
            'is_active' => true,
        ]);

        // 4. General Manager - Hotel
        User::create([
            'registration_id' => 'GM002',
            'name' => 'Hendra GM Hotel',
            'email' => null,
            'phone' => '08111111004',
            'password' => Hash::make('password'),
            'role' => 'department_admin',
            'jabatan_id' => 3, // General Manager
            'directorate_id' => 2,
            'division_id' => 2, // Hotel
            'superior_id' => null,
            'nik' => '3173000000000004',
            'address' => 'Jl. Hotel No.1',
            'is_active' => true,
        ]);

        // 5. Executive Assistant (under Hotel GM)
        User::create([
            'registration_id' => 'EA001',
            'name' => 'Ela Exec Hotel',
            'email' => null,
            'phone' => '08111111005',
            'password' => Hash::make('password'),
            'role' => 'upper_staff',
            'jabatan_id' => 4, // Executive Assistant
            'directorate_id' => 2,
            'division_id' => 2,
            'superior_id' => null,
            'nik' => '3173000000000005',
            'address' => 'Jl. Hotel No.2',
            'is_active' => true,
        ]);

        // 6+. Manager for each department
        $departments = [
            [1, 'Corporate Secretary', 1, null],
            [2, 'Legal & Compliance', 1, null],
            [3, 'Internal Audit', 1, null],
            [4, 'Business Development', 1, null],
            [5, 'Engineering Planning', 2, null],
            [6, 'Project Control', 2, null],
            [7, 'Security Fire & SHE Manager', 2, null],
            [8, 'Finance', 3, null],
            [9, 'Accounting', 3, null],
            [10, 'Procurement', 3, null],
            [11, 'IT & Management System', 3, null],
            [12, 'Human Capital', 3, null],
            [13, 'Marketing Industrial Estate & Housing', 2, 1],
            [14, 'Industrial Estate & Housing', 2, 1],
            [15, 'Building Management & Office Rent', 2, 1],
            [16, 'Real Estate', 2, 1],
            [17, 'Golf & Sport Center Manager', 2, 1],
            [18, 'Executive Marketing & Sales Hotel', 2, 2],
            [19, 'Front Office', 2, 2],
            [20, 'Housekeeping', 2, 2],
            [21, 'Food & Beverage', 2, 2],
            [22, 'Executive Chef', 2, 2],
            [23, 'Engineering', 2, 2],
        ];

        foreach ($departments as $index => [$id, $name, $directorateId, $divisionId]) {
            User::create([
                'registration_id' => 'MAN' . str_pad($id, 3, '0', STR_PAD_LEFT),
                'name' => 'Manager ' . $name,
                'email' => null,
                'phone' => '0811111' . str_pad($id + 100, 4, '0', STR_PAD_LEFT),
                'password' => Hash::make('password'),
                'role' => 'department_admin',
                'jabatan_id' => 5, // Manager
                'department_id' => $id,
                'directorate_id' => $directorateId,
                'division_id' => $divisionId,
                'superior_id' => null,
                'nik' => '3173000000000' . str_pad($id + 5, 4, '0', STR_PAD_LEFT),
                'address' => 'Jl. ' . $name,
                'is_active' => true,
            ]);
        }
    }
}
