<?php

namespace Modules\Student\DTO;

class StudentParentDto
{
    public $parent_name;
    public $parent_nationality;
    public $parent_identity_number;
    public $parent_job;
    public $parent_job_address;
    public $parent_education_level;
    public $mother_name;
    public $mother_nationality;
    public $mother_identity_number;
    public $mother_job;
    public $mother_job_address;
    public $mother_education_level;
    public $mother_phone;
    public $parents_status;
    public $relative_name;
    public $relative_relation;
    public $relative_phone;

    public function __construct($request) {
        if($request->get('parent_name')) $this->parent_name = $request->get('parent_name');
        if($request->get('parent_nationality')) $this->parent_nationality = $request->get('parent_nationality');
        if($request->get('parent_identity_number')) $this->parent_identity_number = $request->get('parent_identity_number');
        if($request->get('parent_job')) $this->parent_job = $request->get('parent_job');
        if($request->get('parent_job_address')) $this->parent_job_address = $request->get('parent_job_address');
        if($request->get('parent_education_level')) $this->parent_education_level = $request->get('parent_education_level');
        if($request->get('mother_name')) $this->mother_name = $request->get('mother_name');
        if($request->get('mother_nationality')) $this->mother_nationality = $request->get('mother_nationality');
        if($request->get('mother_identity_number')) $this->mother_identity_number = $request->get('mother_identity_number');
        if($request->get('mother_job')) $this->mother_job = $request->get('mother_job');
        if($request->get('mother_job_address')) $this->mother_job_address = $request->get('mother_job_address');
        if($request->get('mother_education_level')) $this->mother_education_level = $request->get('mother_education_level');
        if($request->get('mother_phone')) $this->mother_phone = $request->get('mother_phone');
        if($request->get('parents_status')) $this->parents_status = $request->get('parents_status');
        if($request->get('relative_name')) $this->relative_name = $request->get('relative_name');
        if($request->get('relative_relation')) $this->relative_relation = $request->get('relative_relation');
        if($request->get('relative_phone')) $this->relative_phone = $request->get('relative_phone');
    }

    public function dataFromRequest()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->parent_name == null)
            unset($data['parent_name']);
        if ($this->parent_nationality == null)
            unset($data['parent_nationality']);
        if ($this->parent_identity_number == null)
            unset($data['parent_identity_number']);
        if ($this->parent_job == null)
            unset($data['parent_job']);
        if ($this->parent_job_address == null)
            unset($data['parent_job_address']);
        if ($this->parent_education_level == null)
            unset($data['parent_education_level']);
        if ($this->mother_name == null)
            unset($data['mother_name']);
        if ($this->mother_nationality == null)
            unset($data['mother_nationality']);
        if ($this->mother_identity_number == null)
            unset($data['mother_identity_number']);
        if ($this->mother_job == null)
            unset($data['mother_job']);
        if ($this->mother_job_address == null)
            unset($data['mother_job_address']);
        if ($this->mother_education_level == null)
            unset($data['mother_education_level']);
        if ($this->mother_phone == null)
            unset($data['mother_phone']);
        if ($this->parents_status == null)
            unset($data['parents_status']);
        if ($this->relative_name == null)
            unset($data['relative_name']);
        if ($this->relative_relation == null)
            unset($data['relative_relation']);
        if ($this->relative_phone == null)
            unset($data['relative_phone']);

        return $data;
    }
}
