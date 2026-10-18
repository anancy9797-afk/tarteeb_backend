<?php


namespace Modules\School\DTO;

class SchoolGradeDto
{
    public $grades = [];

    public function __construct($request)
    {
        if ($request->get('grades'))
            $this->grades = (array) $request->get('grades');
    }

    public function dataFromRequest()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->grades == null || count($this->grades) == 0)
            unset($data['grades']);
        return $data;
    }
}
