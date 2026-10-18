<?php

namespace Modules\Expense\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Modules\Expense\App\Models\StudentExpense;
use Illuminate\Http\Exceptions\HttpResponseException;

class ExpenseStudentAdminRequest extends FormRequest
{
    public function rules(): array
    {

        $studentExpense = $this->route('studentExpense');
        $rules = [
            'status' => ['required', 'in:accepted,rejected'],
            'rejected_reason' => ['required_if:status,rejected', 'string', 'max:255'],
        ];
        if (!$studentExpense->is_registration_fee) {
            $rules['amount_paid'] = ['prohibited_if:status,rejected', 'required_if:status,accepted', 'numeric', 'min:0'];
        } else {
            $rules['amount_paid'] = ['nullable', 'numeric', 'min:0'];
        }
        return $rules;

    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'status' => 'Status',
            'rejected_reason' => 'Rejected Reason',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
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
