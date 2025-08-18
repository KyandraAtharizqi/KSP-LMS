<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['id' => 1, 'name' => 'Corporate Secretary', 'directorate_id' => 1],
            ['id' => 2, 'name' => 'Legal & Compliance', 'directorate_id' => 1],
            ['id' => 3, 'name' => 'Internal Audit', 'directorate_id' => 1],
            ['id' => 4, 'name' => 'Business Development', 'directorate_id' => 1],
            ['id' => 5, 'name' => 'Engineering Planning', 'directorate_id' => 2],
            ['id' => 6, 'name' => 'Project Control', 'directorate_id' => 2],
            ['id' => 7, 'name' => 'Security Fire & SHE Manager', 'directorate_id' => 2],
            ['id' => 8, 'name' => 'Finance', 'directorate_id' => 3],
            ['id' => 9, 'name' => 'Accounting', 'directorate_id' => 3],
            ['id' => 10, 'name' => 'Procurement', 'directorate_id' => 3],
            ['id' => 11, 'name' => 'IT & Management System', 'directorate_id' => 3],
            ['id' => 12, 'name' => 'Human Capital', 'directorate_id' => 3],
            ['id' => 13, 'name' => 'Marketing Industrial Estate & Housing', 'directorate_id' => 2],
            ['id' => 14, 'name' => 'Industrial Estate & Housing', 'directorate_id' => 2],
            ['id' => 15, 'name' => 'Building Management & Office Rent', 'directorate_id' => 2],
            ['id' => 16, 'name' => 'Real Estate', 'directorate_id' => 2],
            ['id' => 17, 'name' => 'Golf & Sport Center Manager', 'directorate_id' => 2],
            ['id' => 18, 'name' => 'Executive Marketing & Sales Hotel', 'directorate_id' => 2],
            ['id' => 19, 'name' => 'Front Office', 'directorate_id' => 2],
            ['id' => 20, 'name' => 'Housekeeping', 'directorate_id' => 2],
            ['id' => 21, 'name' => 'Food & Beverage', 'directorate_id' => 2],
            ['id' => 22, 'name' => 'Executive Chef', 'directorate_id' => 2],
            ['id' => 23, 'name' => 'Engineering', 'directorate_id' => 2],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
