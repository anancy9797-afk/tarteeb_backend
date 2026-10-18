<?php


namespace Modules\Grade\DTO;

class GradeCategoryDto
{
    public array $names = [];
    public $school_id;
    public $name;

    public function __construct($request)
    {
        if ($request->isMethod('POST')) {
            if ($request->get('names')) {
                $this->names = $request->get('names');
            }
        }else if($request->isMethod('PUT')){
            if ($request->get('name')) {
                $this->name = $request->get('name');
            }
        }
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
        if ($this->names == null || count($this->names) == 0)
            unset($data['names']);
        if ($this->school_id == null)
            unset($data['school_id']);
        if ($this->name == null)
            unset($data['name']);
        return $data;
    }
}
