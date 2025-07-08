<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jabatan;

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        Jabatan::insert([
            ['name' => 'Director'],
            ['name' => 'Assistant Director'],
            ['name' => 'General Manager'],
            ['name' => 'Manager'],
            ['name' => 'Superintendent'],
            ['name' => 'Senior Engineer'],
            ['name' => 'Senior Officer'],
            ['name' => 'Supervisor'],
            ['name' => 'Engineer'],
            ['name' => 'Officer'],
            ['name' => 'Foreman'],
            ['name' => 'Junior Engineer'],
            ['name' => 'Junior Officer'],
            ['name' => 'Hotel Staff'],
        ]);
    }
}


