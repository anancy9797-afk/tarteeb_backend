<?php

namespace Modules\Grade\App\Rules;

use Closure;
use Modules\Grade\App\Models\GradeCategory;
use Illuminate\Contracts\Validation\ValidationRule;

class GradeCategoryBelongToSchool implements ValidationRule
{
    private $gradeCategoryId;
    private $schoolId;
    public function __construct($gradeCategoryId, $schoolId)
    {
        $this->gradeCategoryId = $gradeCategoryId;
        $this->schoolId = $schoolId;
    }
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $gradeCategory = GradeCategory::find($this->gradeCategoryId);
        if ($gradeCategory->school_id != $this->schoolId) {
            $fail('The grade category does not belong to your school.');
        }
    }
}
