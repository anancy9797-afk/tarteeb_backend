<?php


namespace Modules\School\DTO;

class SchoolDto
{
    public $name;

    public function __construct($request)
    {
        if ($request->get('name'))
            $this->name = $request->get('name');
    }

    public function dataFromRequest()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->name == null)
            unset($data['name']);
        return $data;
    }
}
