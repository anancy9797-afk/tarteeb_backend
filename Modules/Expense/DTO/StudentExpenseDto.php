<?php


namespace Modules\Expense\DTO;

use Modules\Expense\App\Models\Expense;
use Modules\Expense\App\Models\StudentExpense;


class StudentExpenseDto
{
    public $student_id;
    public $expense_id;
    public $amount;
    public $date;
    public $payment_method;

    public function __construct($request)
    {
        $expense = $this->getExpense();
        $this->student_id = auth('user')->user()->student->id;
        $this->expense_id = $expense->id;
        $this->amount = $this->getAmount($expense);
        $this->date = now()->toDateString();
        $this->payment_method = $request->get('payment_method');
    }

    private function getAmount($expense)
    {
        $basePrice = $expense->exceptions->count() > 0
            ? $expense->exceptions->first()->pivot->exception_price
            : $expense->price;

        return $basePrice;
    }

    private function getExpense()
    {
        $student = auth('user')->user()->student;
        $expense = Expense::with([
            'exceptions' => function ($query) {
                $query->where('student_id', $this->student_id);
            }
        ])->where('grade_id', $student->grade_id)
            ->where('grade_category_id', $student->grade->gradeCategory->id)
            ->where('school_id', $student->school_id)
            ->first();
        return $expense;
    }

    public function dataFromRequest()
    {
        $data = json_decode(json_encode($this), true);
        if ($this->expense_id == null)
            unset($data['expense_id']);
        if ($this->amount == null)
            unset($data['amount']);
        if ($this->date == null)
            unset($data['date']);
        if ($this->payment_method == null)
            unset($data['payment_method']);
        return $data;
    }
}
