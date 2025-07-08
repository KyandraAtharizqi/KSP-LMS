<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Directorate;

class DirectorateSeeder extends Seeder
{
    public function run(): void
    {
        Directorate::insert([
            ['name' => 'Operational'],
            ['name' => 'Supporting'],
            ['name' => 'Human Capital & Finance'],
        ]);
    }
}

