<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        Department::insert([
            ['name' => 'Hotel', 'directorate_id' => 1],
            ['name' => 'Golf & Sports Center', 'directorate_id' => 1],
            ['name' => 'Internal Audit', 'directorate_id' => 2],
            ['name' => 'Human Capital', 'directorate_id' => 3],
            ['name' => 'Finance', 'directorate_id' => 3],
        ]);
    }
}
