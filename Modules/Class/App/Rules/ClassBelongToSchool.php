<?php

namespace Modules\Class\App\Rules;

use Closure;
use Modules\Class\App\Models\Classroom;
use Illuminate\Contracts\Validation\ValidationRule;

class ClassBelongToSchool implements ValidationRule
{
    private $classId;
    private $schoolId;
    public function __construct($classId, $schoolId)
    {
        $this->classId = $classId;
        $this->schoolId = $schoolId;
    }
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $class = Classroom::find($this->classId);
        if (!$class) {
            $fail('The class does not exist.');
        } else if ($class->school_id != $this->schoolId) {
            $fail('The class does not belong to your school.');
        }
    }
}