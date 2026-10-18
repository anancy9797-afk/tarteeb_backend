<?php

namespace Modules\Session\App\Http\Requests;

use Modules\Class\App\Models\Classroom;
use Modules\Session\App\Models\Session;
use Illuminate\Foundation\Http\FormRequest;
use Modules\Session\App\Rules\SessionLimit;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AttendanceRequest extends FormRequest
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
'grade_id' => ['required', 'exists:grades,id'], // تم التعديل هنا لفحص جدول grades
                // 'stage_id'    => ['required', 'exists:grades,id'], // أو الجدول الخاص بالمرحلة
                'semester'    => ['required', 'string', 'in:first,second'], // أو term_id لو بتبعت برقم الترم
                // 'class_id' => ['required'],
                'day' => ['required', 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
                // 'semester' => ['required', 'string', 'in:first,second'],
                // 'session_number' => ['required', 'integer', 'min:1', 'max:15'],
                'year' => ['required', 'integer'],
            ];
        }
        if ($this->isMethod('POST')) {
            return [
                'session_id' => [ 'exists:sessions,id'],
                'attendance' => ['required', 'array'],
                'attendance.*.student_id' => ['required', 'exists:students,id'],
                'attendance.*.is_present' => ['required', 'boolean'],
            ];
        }
        if ($this->isMethod('PUT')) {
            return [
                'is_present' => ['required', 'boolean'],
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