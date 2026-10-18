<?php


namespace Modules\Class\DTO;


class ClassDto
{
    public $name;
    public $grade_id;
    public $school_id;
    public $max_students;
    public $session_number;

    public function __construct($request) {
        if($request->get('name')) $this->name = $request->get('name');
        if($request->get('grade_id')) $this->grade_id = $request->get('grade_id');
        if (auth('user')->user()->hasRole('Super Admin')) {
            if ($request->get('school_id')) {
                $this->school_id = $request->get('school_id');
            }
        } else if (auth('user')->user()->hasRole('School Manager')) {
            if ($request->isMethod('POST')) {
                $this->school_id = auth('user')->user()->school_id;
            }
        }
        if($request->get('max_students')) $this->max_students = $request->get('max_students');
        if($request->get('session_number')) $this->session_number = $request->get('session_number');
    }

    public function dataFromRequest()
    {
        $data =  json_decode(json_encode($this), true);
        if($this->name == null) unset($data['name']);
        if($this->grade_id == null) unset($data['grade_id']);
        if($this->school_id == null) unset($data['school_id']);
        if($this->max_students == null) unset($data['max_students']);
        if($this->session_number == null) unset($data['session_number']);
        return $data;
    }
}
