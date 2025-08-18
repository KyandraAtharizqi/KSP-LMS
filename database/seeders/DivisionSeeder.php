<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $divisions = [
            ['id' => 1, 'name' => 'Commercial Property', 'directorate_id' => 2],
            ['id' => 2, 'name' => 'Hotel', 'directorate_id' => 2],
            ['id' => 3, 'name' => 'Construction', 'directorate_id' => 2],
        ];

        foreach ($divisions as $division) {
            \App\Models\Division::create($division);
        }
    }
}
