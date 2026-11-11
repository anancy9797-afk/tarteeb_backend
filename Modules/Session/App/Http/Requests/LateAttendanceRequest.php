<?php

namespace Modules\Session\App\Http\Requests;

use Modules\Class\App\Models\Classroom;
use Modules\Session\App\Models\Session;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Session\App\Rules\SessionLimit;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LateAttendanceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        if($this->isMethod('GET')){
       return [
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:students,id'
            ],
        ];
        }
        if ($this->isMethod('POST')) {
           return [
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:students,id'
            ],
        ];
        }
        if ($this->isMethod('PUT')) {
               return [
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => [
                'required',
                'integer',
                'distinct',
                'exists:students,id'
            ],
        ];
        }
        return [];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'day' => 'Day',
            'session_number' => 'Session Number',
            'is_present' => 'Is Present',
            'class_id' => 'Class',
            'semester' => 'Semester',
            'year' => 'Year',
            'student_id' => 'Student',
            'session_id' => 'Session',
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