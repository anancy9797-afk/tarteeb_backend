<?php

namespace Modules\Expense\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Modules\Expense\App\Models\StudentExpense;
use Illuminate\Http\Exceptions\HttpResponseException;

class ExpenseStudentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            // 'expense_id' => ['required', 'exists:expenses,id'],
            'receipt' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:1024'],
            'payment_method' => ['required', 'in:1,2,3'],
        ];

    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'expense_id' => 'Expense ID',
            'receipt' => 'Receipt',
            'payment_method' => 'Payment Method',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // $studentExpense = StudentExpense::query()
        //     ->where('student_id', auth('user')->user()->student->id)
        //     ->where('expense_id', $this->input('expense_id'))
        //     ->first();
        // if ($studentExpense) {
        //     throw new HttpResponseException(
        //         returnMessage(false, 'You have already requested to pay this expense before', null, 'unprocessable_entity')
        //     );
        // }
        return true;
    }

    /**
     * Configure the validator instance.
     */

    /**
     * Handle a failed validation attempt.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            returnValidationMessage(
                false,
                trans('validation.rules_failed'),
                $validator->errors()->messages(),
                'unprocessable_entity'
            )
        );
    }
}
