<?php

namespace Modules\Teacher\App\Rules;

use Closure;
use Modules\Teacher\App\Models\TeacherProfile;
use Illuminate\Contracts\Validation\ValidationRule;

class TeacherBelongToSchool implements ValidationRule
{
    private $teacherId;
    private $schoolId;
    public function __construct($teacherId, $schoolId)
    {
        $this->teacherId = $teacherId;
        $this->schoolId = $schoolId;
    }
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $teacher = TeacherProfile::find($this->teacherId);
        if (!$teacher) {
            $fail('The teacher does not exist.');
            return;
        }
        if ($teacher->teacher->school_id != $this->schoolId) {
            $fail('The teacher does not belong to your school.');
        }
    }
}