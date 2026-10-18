<?php

namespace Modules\Expense\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;

class ExpenseExceptionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'student_ids' => ['required', 'array'],
            'student_ids.*' => ['required', 'exists:students,id,school_id,' . auth('user')->user()->school_id, 'distinct'],
            'exception_price' => ['required', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            // Exception Details
            'details' => ['nullable', 'array'],
            'details.*.name' => ['nullable', 'string', 'max:255'],
            'details.*.price' => ['required_with:details', 'numeric', 'min:0'],
            // Exception Installments
            'installments' => ['nullable', 'array'],
            'installments.*.name' => ['nullable', 'string', 'max:255'],
            'installments.*.price' => ['required_with:installments', 'numeric', 'min:0'],
            'installments.*.is_optional' => ['nullable', 'in:0,1'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'student_ids' => 'Student IDs',
            'student_ids.*' => 'Student ID',
            'exception_price' => 'Exception Price',
            'notes' => 'Notes',
            'details' => 'Exception Details',
            'details.*.name' => 'Detail Name',
            'details.*.price' => 'Detail Price',
            'installments' => 'Exception Installments',
            'installments.*.name' => 'Installment Name',
            'installments.*.price' => 'Installment Price',
            'installments.*.is_optional' => 'Is Optional',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $expense = $this->route('expense');
        if ($expense) {
            $user = auth('user')->user();
            if ($expense->school_id !== $user->school_id) {
                throw new HttpResponseException(
                    returnMessage(
                        false,
                        'You are not authorized to update this expense',
                        null,
                        'unauthorized'
                    )
                );
            }
        }


        return true;
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        // Check for existing exceptions (only for store, not update)
        if (!in_array('update', request()->segments())) {
            $validator->after(function ($validator) {
                $expense = $this->route('expense');
                $studentIds = $this->input('student_ids');

                $existingExceptions = DB::table('expense_student_exceptions')
                    ->where('expense_id', $expense->id)
                    ->whereIn('student_id', $studentIds)
                    ->pluck('student_id');

                if (!empty($existingExceptions)) {
                    $studentNames = DB::table('students')
                        ->whereIn('id', $existingExceptions)
                        ->get()
                        ->pluck('name');
                }
                if ($existingExceptions->isNotEmpty()) {
                    $validator->errors()->add(
                        'student_ids',
                        'الطلاب بالأسماء ' . $studentNames->implode(', ') . ' لديهم بالفعل استثناءات لهذه النفقات'
                    );
                }
            });
        }

        $validator->after(function ($validator) {
            $installments = $this->input('installments');
            $exceptionPrice = $this->input('exception_price');

            if ($installments) {
                $installmentsSum = collect($installments)
                    ->filter(fn($installment) => !($installment['is_optional'] == 1))
                    ->sum('price');
                if ($installmentsSum != $exceptionPrice) {
                    $validator->errors()->add(
                        'installments',
                        'مجموع أسعار الأقساط (' . $installmentsSum . ') يجب أن يساوي سعر الاستثناء (' . $exceptionPrice . ')'
                    );
                }
            }
        });
    }

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
