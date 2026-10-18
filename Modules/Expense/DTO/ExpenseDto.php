<?php


namespace Modules\Expense\DTO;


class ExpenseDto
{
    public $user_id;
    public $school_id;
    public $grade_category_id;
    public $grade_id;
    public $price;
    public $details;
    public $installments;
    public function __construct($request, $isCreate = false)
    {
        if ($isCreate) {
            $user = auth('user')->user();
            $this->user_id = $user->id;
            $this->school_id = $user->school_id;
        }
        if ($request->get('grade_category_id'))
            $this->grade_category_id = $request->get('grade_category_id');
        if ($request->get('grade_id'))
            $this->grade_id = $request->get('grade_id');
        if ($request->get('price'))
            $this->price = $request->get('price');
        if ($request->get('details'))
            $this->details = $request->get('details');
        if ($request->get('installments'))
            $this->installments = $request->get('installments');
    }

    public function dataFromRequest()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->user_id == null)
            unset($data['user_id']);
        if ($this->school_id == null)
            unset($data['school_id']);
        if ($this->grade_category_id == null)
            unset($data['grade_category_id']);
        if ($this->grade_id == null)
            unset($data['grade_id']);
        if ($this->price == null)
            unset($data['price']);
        if ($this->details == null)
            unset($data['details']);
        if ($this->installments == null)
            unset($data['installments']);
        return $data;
    }
}
