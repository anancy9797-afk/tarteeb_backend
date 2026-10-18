<?php

namespace Modules\Student\App\Rules;

use Closure;
use Modules\Student\App\Models\Student;
use Illuminate\Contracts\Validation\ValidationRule;

class StudentBelongToSchool implements ValidationRule
{
    private $studentIds;
    private $schoolId;
    public function __construct($studentIds, $schoolId)
    {
        $this->studentIds = $studentIds;
        $this->schoolId = $schoolId;
    }
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        foreach ($this->studentIds as $studentId) {
            $student = Student::find($studentId);
            if ($student->school_id != $this->schoolId) {
                $fail('The student does not belong to your school.');
            }
        }
    }
}