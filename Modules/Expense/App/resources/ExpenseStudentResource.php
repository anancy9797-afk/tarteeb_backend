<?php

namespace Modules\Expense\App\resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseStudentResource extends JsonResource
{
    public function toArray($request): array
    {
        // $amount_paid = $this->is_registration_fee ? $this->expense->installments->where('name', 'مقدم الدفع')->first()->price : $this->amount_paid;
        // $amount_paid = $this->is_registration_fee ? $this->expense->details->where('name', 'مقدم الدفع')->first()->price : $this->amount_paid;

        return [
            'id' => $this->id,
            'final_amount' => $this->amount,
            'amount_paid' => $this->amount_paid,
            'amount_due' => $this->amount_due,
            'cumulative_paid' => $this->cumulative_paid,
            'date' => $this->date,
            'payment_status' => $this->payment_status ?? null,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'rejected_reason' => $this->rejected_reason ?? null,
            'expense_price' => $this->expense->price,
            'grade_category_name' => $this->expense->grade->gradeCategory->name,
            'grade_name' => $this->expense->grade->name,
            'exception_price' => $this->expense->exceptions->where('id', $this->student_id)->values()->first()->pivot->exception_price ?? null,
            'notes' => $this->expense->exceptions->where('id', $this->student_id)->values()->first()->pivot->notes ?? null,
            'receipt' => $this->receipt ?? null,
            'is_registration_fee' => $this->is_registration_fee,
            'created_at' => $this->created_at->format('Y-m-d H:i A'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i A'),
            'student' => $this->student,
            'details' => $this->expense?->details,
            'grade' => $this->expense?->grade,
            'grade_category' => $this->expense?->grade?->gradeCategory,
        ];
    }
}
