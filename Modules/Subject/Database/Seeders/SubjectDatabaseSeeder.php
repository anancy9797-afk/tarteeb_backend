<?php

namespace Modules\Subject\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Subject\App\Models\Subject;

class SubjectDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subjects = [];

        // Primary School (Grades 1-6)
        for ($grade = 1; $grade <= 6; $grade++) {
            $subjects = array_merge($subjects, [
                ['name' => "Primary {$grade} - Arabic", 'grade_id' => $grade, 'created_at' => now(), 'updated_at' => now()],
                ['name' => "Primary {$grade} - Mathematics", 'grade_id' => $grade, 'created_at' => now(), 'updated_at' => now()],
                ['name' => "Primary {$grade} - English", 'grade_id' => $grade, 'created_at' => now(), 'updated_at' => now()],
                ['name' => "Primary {$grade} - Science", 'grade_id' => $grade, 'created_at' => now(), 'updated_at' => now()],
                ['name' => "Primary {$grade} - Social Studies", 'grade_id' => $grade, 'created_at' => now(), 'updated_at' => now()],
                ['name' => "Primary {$grade} - Religious Studies", 'grade_id' => $grade, 'created_at' => now(), 'updated_at' => now()],
                ['name' => "Primary {$grade} - Art", 'grade_id' => $grade, 'created_at' => now(), 'updated_at' => now()],
                ['name' => "Primary {$grade} - Physical Education", 'grade_id' => $grade, 'created_at' => now(), 'updated_at' => now()]
            ]);
        }

        // Secondary School (Grades 7-9)
        for ($grade = 7; $grade <= 9; $grade++) {
            $subjects = array_merge($subjects, [
                ['name' => "Secondary {$grade} - Arabic", 'grade_id' => $grade, 'created_at' => now(), 'updated_at' => now()],
                ['name' => "Secondary {$grade} - Mathematics", 'grade_id' => $grade, 'created_at' => now(), 'updated_at' => now()],
                ['name' => "Secondary {$grade} - English", 'grade_id' => $grade, 'created_at' => now(), 'updated_at' => now()],
                ['name' => "Secondary {$grade} - Physics", 'grade_id' => $grade, 'created_at' => now(), 'updated_at' => now()],
                ['name' => "Secondary {$grade} - Chemistry", 'grade_id' => $grade, 'created_at' => now(), 'updated_at' => now()],
                ['name' => "Secondary {$grade} - Biology", 'grade_id' => $grade, 'created_at' => now(), 'updated_at' => now()],
                ['name' => "Secondary {$grade} - Social Studies", 'grade_id' => $grade, 'created_at' => now(), 'updated_at' => now()],
                ['name' => "Secondary {$grade} - Religious Studies", 'grade_id' => $grade, 'created_at' => now(), 'updated_at' => now()],
                ['name' => "Secondary {$grade} - Computer Science", 'grade_id' => $grade, 'created_at' => now(), 'updated_at' => now()]
            ]);
        }

        // Secondary School (Grades 10-12)
        for ($grade = 10; $grade <= 12; $grade++) {
            $subjects = array_merge($subjects, [
                ['name' => "Secondary {$grade} - Arabic", 'grade_id' => $grade, 'created_at' => now(), 'updated_at' => now()],
                ['name' => "Secondary {$grade} - Mathematics", 'grade_id' => $grade, 'created_at' => now(), 'updated_at' => now()],
                ['name' => "Secondary {$grade} - English", 'grade_id' => $grade, 'created_at' => now(), 'updated_at' => now()],
                ['name' => "Secondary {$grade} - Physics", 'grade_id' => $grade, 'created_at' => now(), 'updated_at' => now()],
                ['name' => "Secondary {$grade} - Chemistry", 'grade_id' => $grade, 'created_at' => now(), 'updated_at' => now()],
                ['name' => "Secondary {$grade} - Biology", 'grade_id' => $grade, 'created_at' => now(), 'updated_at' => now()],
                ['name' => "Secondary {$grade} - History", 'grade_id' => $grade, 'created_at' => now(), 'updated_at' => now()],
                ['name' => "Secondary {$grade} - Geography", 'grade_id' => $grade, 'created_at' => now(), 'updated_at' => now()],
                ['name' => "Secondary {$grade} - Philosophy", 'grade_id' => $grade, 'created_at' => now(), 'updated_at' => now()],
                ['name' => "Secondary {$grade} - Religious Studies", 'grade_id' => $grade, 'created_at' => now(), 'updated_at' => now()],
                ['name' => "Secondary {$grade} - Computer Science", 'grade_id' => $grade, 'created_at' => now(), 'updated_at' => now()]
            ]);
        }

        Subject::insert($subjects);
    }
}
