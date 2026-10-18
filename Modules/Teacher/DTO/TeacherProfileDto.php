<?php


namespace Modules\Teacher\DTO;

use Illuminate\Support\Facades\Hash;

class TeacherProfileDto
{
    public $gender;
    public $subjects;
    // public $grade_id;

    public function __construct($request)
    {
        if ($request->get('gender'))
            $this->gender = $request->get('gender');
        if ($request->get('subjects'))
            $this->subjects = $request->get('subjects');
        // if ($request->get('grade_id'))
        //     $this->grade_id = $request->get('grade_id');
    }

    public function dataFromRequest()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->gender == null)
            unset($data['gender']);
        // if ($this->grade_id == null)
        //     unset($data['grade_id']);
        if ($this->subjects == null)
            unset($data['subjects']);

        return $data;
    }
}
