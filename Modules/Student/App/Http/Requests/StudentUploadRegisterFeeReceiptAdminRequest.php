<?php

namespace Modules\Student\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StudentUploadRegisterFeeReceiptAdminRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
            'register_fee_image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:1024'],
            'payment_method' => ['required', 'in:1,2,3'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'student_id' => 'Student',
            'register_fee_image' => 'Register Fee Image',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth('user')->user();

        if ($user->hasRole('Super Admin')) {
            return true;
        }

        $studentId = $this->input('student_id');
        $student = \Modules\Student\App\Models\Student::find($studentId);

        if (!$student) {
            $this->failedAuthorizationWithCustomMessage('Student not found.');
        }

        if ($student->school_id != $user->school_id) {
            $this->failedAuthorizationWithCustomMessage('You are not authorized to upload a receipt for this student.');
        }

        return true;
    }

    /**
     * Throw a failed authorization exception with a custom message.
     */
    protected function failedAuthorizationWithCustomMessage($message)
    {
        throw new HttpResponseException(
            returnValidationMessage(
                false,
                $message,
                [],
                'unauthorized'
            )
        );
    }



    protected function failedAuthorization()
    {
        throw new HttpResponseException(
            returnValidationMessage(
                false,
                'Unauthorized action.',
                [],
                'unauthorized'
            )
        );
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
