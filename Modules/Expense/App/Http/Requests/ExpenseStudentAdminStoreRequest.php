<?php

namespace Modules\Expense\App\Http\Requests;

use Modules\Student\App\Models\Student;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ExpenseStudentAdminStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
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
        $admin = auth('user')->user();
        if (!$admin) {
            return false;
        }

        $studentId = $this->input('student_id');

        if (!$studentId) {
            return true;
        }

        $student = Student::query()
            ->select('id', 'school_id')
            ->find($studentId);

        if (!$student) {
            return true;
        }

        return (int) $student->school_id === (int) $admin->school_id;
    }

    protected function failedAuthorization(): void
    {
        throw new HttpResponseException(
            returnValidationMessage(
                false,
                'This student not related to your school'
                [],
                'forbidden'
            )
        );
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
