<?php

namespace Modules\Subject\App\Rules;

use Closure;
use Modules\Class\App\Models\Classroom;
use Modules\Subject\App\Models\Subject;
use Illuminate\Contracts\Validation\ValidationRule;

class SubjectBelongToClassGrade implements ValidationRule
{
    private $classId;

    public function __construct($classId)
    {
        $this->classId = $classId;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
       $subject = Subject::find($value);
        if (!$subject) {
            $fail('The subject does not exist.');
            return;
        }
        $class = Classroom::find($this->classId);
        if (!$class) {
            $fail('The class does not exist.');
            return;
        }
        if ($subject->grade_id != $class->grade_id) {
            $fail('The subject does not belong to your class grade.');
        }
    }
}