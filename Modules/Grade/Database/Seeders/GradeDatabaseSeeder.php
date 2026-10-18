<?php

namespace Modules\Grade\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Grade\App\Models\Grade;
use Modules\Grade\App\Models\GradeCategory;

class GradeDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $gradeCategories = [
            ['name' => 'Primary', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Secondary', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'High School', 'created_at' => now(), 'updated_at' => now()],
        ];
        GradeCategory::insert($gradeCategories);

        $grades = [
            // Primary grades (1-6)
            ['name' => 'Grade 1', 'grade_category_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Grade 2', 'grade_category_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Grade 3', 'grade_category_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Grade 4', 'grade_category_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Grade 5', 'grade_category_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Grade 6', 'grade_category_id' => 1, 'created_at' => now(), 'updated_at' => now()],

            // Secondary grades (7-9)
            ['name' => 'Grade 1', 'grade_category_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Grade 2', 'grade_category_id' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Grade 3', 'grade_category_id' => 2, 'created_at' => now(), 'updated_at' => now()],

            // High School grades (10-12)
            ['name' => 'Grade 1', 'grade_category_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Grade 2', 'grade_category_id' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Grade 3', 'grade_category_id' => 3, 'created_at' => now(), 'updated_at' => now()],
        ];

        Grade::insert($grades);
    }
}
