<?php

namespace Modules\Session\App\Rules;

use Closure;
use Modules\Class\App\Models\Classroom;
use Modules\Session\App\Models\Session;
use Illuminate\Contracts\Validation\ValidationRule;

class SessionLimit implements ValidationRule
{
    private $classId;
    private $semester;
    private $year;
    private $day;
    /**
     * Run the validation rule.
     */

    public function __construct($classId, $semester, $year, $day)
    {
        $this->classId = $classId;
        $this->semester = $semester;
        $this->year = $year;
        $this->day = $day;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $classroom = Classroom::find($this->classId);
        if (!$classroom) {
            $fail('The class does not exist.');
            return;
        }
        $sessionMaxNumber = $classroom->session_number;

        $currentSessionCount = Session::where('class_id', $this->classId)
            ->where('semester', $this->semester)
            ->where('year', $this->year)
            ->where('day', $this->day)
            ->count();

        if ($currentSessionCount >= $sessionMaxNumber) {
            $fail("The session limit ({$sessionMaxNumber}) for this class on {$this->day} in {$this->semester} semester {$this->year} has been exceeded.");
        }
    }
}