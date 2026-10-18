<?php

namespace Modules\Expense\App\resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RequiredExpensesResourceOld extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        $total_amount_required = $this->requests && $this->requests->isNotEmpty()
            ? $this->requests->first()->amount
            : null;
        $total_paid_amount = $this->requests && $this->requests->isNotEmpty()
            ? $this->requests->where('status', 'accepted')->sum('amount_paid')
            : null;
        $total_amount_due = ($total_amount_required && $total_paid_amount !== null)
            ? $total_amount_required - $total_paid_amount
            : null;

        return [
            'id' => $this->id,
            'school_name' => $this->school->name,
            'grade_category_name' => $this->gradeCategory->name,
            'grade_name' => $this->grade->name,
            'price' => $this->price,
            'year' => $this->created_at->format('Y') ?? null,
            'exceptions_price' => $this->exceptions->first()->pivot->exception_price ?? null,
            'exceptions_notes' => $this->exceptions->first()->pivot->notes ?? null,
            'student_expenses' => $this->requests && $this->requests->isNotEmpty()
                ? $this->requests->map(function ($request) {
                    return [
                        'id' => $request->id,
                        'amount' => $request->amount,
                        'amount_paid' => $request->amount_paid ?? null,
                        'receipt' => $request->receipt,
                        'payment_status' => $request->payment_status ?? null,
                        'payment_method' => $request->payment_method,
                        'date' => $request->date,
                        'status' => $request->status,
                        'rejected_reason' => $request->rejected_reason ?? null,
                    ];
                })
                : [],
            'total_amount_required' => $total_amount_required,
            'total_amount_paid' => $total_paid_amount,
            'total_amount_due' => $total_amount_due,
        ];
    }
}
