<?php


namespace Modules\Session\DTO;

class SessionDto
{
    public $day;
    public $session_number;
    public $semester;
    public $year;
    public $class_id;
    public $subject_id;
    public $school_id;
    public $teacher_id;
    public $is_final;

    public function __construct($request)
    {
        if ($request->get('day'))
            $this->day = $request->get('day');
        if ($request->get('session_number'))
            $this->session_number = $request->get('session_number');
        if ($request->get('semester'))
            $this->semester = $request->get('semester');
        if ($request->get('year'))
            $this->year = $request->get('year');
        if ($request->get('class_id'))
            $this->class_id = $request->get('class_id');
        if ($request->get('subject_id'))
            $this->subject_id = $request->get('subject_id');
        if (auth('user')->user()->hasRole('Super Admin')) {
            if ($request->get('school_id')) {
                $this->school_id = $request->get('school_id');
            }
        } else if (auth('user')->user()->hasRole('School Manager')) {
            if ($request->isMethod('POST')) {
                $this->school_id = auth('user')->user()->school_id;
            }
        }
        if ($request->get('teacher_id'))
            $this->teacher_id = $request->get('teacher_id');
        if ($request->get('is_final'))
            $this->is_final = $request->get('is_final');
    }

    public function dataFromRequest()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->day == null)
            unset($data['day']);
        if ($this->session_number == null)
            unset($data['session_number']);
        if ($this->semester == null)
            unset($data['semester']);
        if ($this->year == null)
            unset($data['year']);
        if ($this->class_id == null)
            unset($data['class_id']);
        if ($this->subject_id == null)
            unset($data['subject_id']);
        if ($this->school_id == null)
            unset($data['school_id']);
        if ($this->teacher_id == null)
            unset($data['teacher_id']);
        if ($this->is_final == null)
            unset($data['is_final']);
        return $data;
    }
}
