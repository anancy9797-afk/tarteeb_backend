<?php

namespace Modules\Student\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Modules\Class\App\Rules\ClassBelongToSchool;
use Modules\Student\App\Rules\StudentBelongToSchool;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpgradeRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            "student_ids" => ['required', 'array'],
            'student_ids.*' => ['required', 'exists:students,id', new StudentBelongToSchool($this->input('student_ids.*'), auth('user')->user()->school_id)],
            'class_id' => ['required', 'exists:classes,id', new ClassBelongToSchool($this->input('class_id'), auth('user')->user()->school_id)]
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'students' => 'Students',
            'class_id' => 'Class'
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