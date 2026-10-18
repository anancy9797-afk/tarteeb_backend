<?php

namespace Modules\Salary\DTO;

class SalaryDto
{
    public $created_by;
    public $user_id;
    public $school_id;
    public $salary;
    public $month;
    public $year;
    public $deduction;
    public $deduction_reason;
    public $bonus;
    public $bonus_reason;
    public function __construct($request, $store = false)
    {
        if ($store) {
            $this->created_by = auth('user')->id();
            $this->school_id = auth('user')->user()->school_id;
        }
        if ($request->get('user_id'))
            $this->user_id = $request->get('user_id');
        if ($request->get('salary'))
            $this->salary = $request->get('salary');
        if ($request->get('month'))
            $this->month = $request->get('month');
        if ($request->get('year'))
            $this->year = $request->get('year');
        if ($request->get('deduction'))
            $this->deduction = $request->get('deduction');
        if ($request->get('deduction_reason'))
            $this->deduction_reason = $request->get('deduction_reason');
        if ($request->get('bonus'))
            $this->bonus = $request->get('bonus');
        if ($request->get('bonus_reason'))
            $this->bonus_reason = $request->get('bonus_reason');
    }

    public function dataFromRequest()
    {
        $data = json_decode(json_encode($this), true);
        if($data['created_by'] == null) unset($data['created_by']);
        if($data['school_id'] == null) unset($data['school_id']);
        if($data['deduction'] == null) unset($data['deduction']);
        if($data['deduction_reason'] == null) unset($data['deduction_reason']);
        if($data['bonus'] == null) unset($data['bonus']);
        if($data['bonus_reason'] == null) unset($data['bonus_reason']);
        return $data;
    }
}
