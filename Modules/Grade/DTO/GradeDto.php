<?php


namespace Modules\Grade\DTO;

class GradeDto
{
    public array $grades = [];
    public $grade_category_id;
    public $school_id;
    public $is_final;
    public $name;
    public function __construct($request)
    {
        if ($request->isMethod('POST')) {
            if ($request->get('grades'))
                $this->grades = $request->get('grades');
        } else if ($request->isMethod('PUT')) {
            if ($request->get('name'))
                $this->name = $request->get('name');
            if ($request->get('is_final'))
                $this->is_final = isset($request['is_final']) ? 1 : 0;
        }
        if ($request->get('grade_category_id'))
            $this->grade_category_id = $request->get('grade_category_id');
        if (auth('user')->user()->hasRole('Super Admin')) {
            if ($request->get('school_id')) {
                $this->school_id = $request->get('school_id');
            }
        } else if (auth('user')->user()->hasRole('School Manager')) {
            if ($request->isMethod('POST')) {
                $this->school_id = auth('user')->user()->school_id;
            }
        }
    }

    public function dataFromRequest()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->grades == null || count($this->grades) == 0)
            unset($data['grades']);
        if ($this->grade_category_id == null)
            unset($data['grade_category_id']);
        if ($this->school_id == null)
            unset($data['school_id']);
        if ($this->name == null)
            unset($data['name']);
        if ($this->is_final == null)
            unset($data['is_final']);
        return $data;
    }
}

