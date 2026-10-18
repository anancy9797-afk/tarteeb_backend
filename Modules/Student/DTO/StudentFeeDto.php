<?php


namespace Modules\Student\DTO;


class StudentFeeDto
{
    public $student_id;
    public $payment_method;
    public $amount;

    public function __construct($request) {
        $this->student_id = auth('user')->user()->student->id;
        if($request->get('payment_method')) $this->payment_method = $request->get('payment_method');
        if($request->get('amount')) $this->amount = $request->get('amount');
    }

    public function dataFromRequest()
    {
        $data =  json_decode(json_encode($this), true);
        if($this->student_id == null) unset($data['student_id']);
        if($this->payment_method == null) unset($data['payment_method']);
        if($this->amount == null) unset($data['amount']);
        return $data;
    }
}
