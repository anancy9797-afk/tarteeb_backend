<?php

namespace Modules\Student\App\Rules;

use Closure;
use Modules\Grade\App\Models\Grade;
use Modules\Student\App\Models\Student;
use Illuminate\Contracts\Validation\ValidationRule;

class StudentInFinalGrade implements ValidationRule
{
    private $studentIds;
    public function __construct($studentIds)
    {
        $this->studentIds = $studentIds;
    }
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        foreach ($this->studentIds as $studentId) {
            $student = Student::find($studentId);
            $grade = Grade::find($student->grade_id);
            if ($grade->is_final == 0) {
                $fail('The student is not in a final grade.');
            }
        }
    }
}