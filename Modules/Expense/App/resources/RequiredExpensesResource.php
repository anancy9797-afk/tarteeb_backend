<?php

namespace Modules\Expense\App\resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RequiredExpensesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        // Check if student has an exception for this expense
        $hasException = $this->exceptions->isNotEmpty();
        $exception = $hasException ? $this->exceptions->first() : null;

        // Base price: use exception price if exists, otherwise use expense price
        $basePrice = $hasException
            ? $exception->pivot->exception_price
            : $this->price;

        // Get details: use exception details if exists, otherwise use expense details
        $details = $hasException && $this->exceptionDetails->isNotEmpty()
            ? $this->exceptionDetails
            : $this->details;

        // Get installments: use exception installments if exists, otherwise use expense installments
        $installments = $hasException && $this->exceptionInstallments->isNotEmpty()
            ? $this->exceptionInstallments
            : $this->installments;

        $total_paid_amount = $this->requests && $this->requests->isNotEmpty()
            ? $this->requests->where('status', 'accepted')->sum('amount_paid')
            : 0;

        $total_amount_due = $basePrice - $total_paid_amount;

        return [
            'id' => $this->id,
            'school_name' => $this->school->name,
            'grade_category_name' => $this->gradeCategory->name,
            'grade_name' => $this->grade->name,
            'price' => $basePrice,
            'original_price' => $this->price,
            'year' => $this->created_at->format('Y') ?? null,
            'has_exception' => $hasException,
            'exceptions_price' => $hasException
                ? $exception->pivot->exception_price
                : null,
            'exceptions_notes' => $exception->pivot->notes ?? null,
            'details' => $details,
            'installments' => $installments,
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
                        'is_registration_fee' => $request->is_registration_fee,
                        'rejected_reason' => $request->rejected_reason ?? null,
                    ];
                })
                : [],
            'total_amount_required' => $basePrice,
            'total_amount_paid' => $total_paid_amount,
            'total_amount_due' => $total_amount_due,
        ];
    }
}
