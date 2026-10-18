<?php

namespace Modules\Session\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Session\App\Rules\SessionLimit;
use Illuminate\Contracts\Validation\Validator;
use Modules\Class\App\Rules\ClassBelongToSchool;
use Modules\Subject\App\Rules\SubjectBelongToSchool;
use Modules\Teacher\App\Rules\TeacherBelongToSchool;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Subject\App\Rules\SubjectBelongToClassGrade;

class SessionStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {

            $rules = [
                'day' => ['required', 'in:saturday,sunday,monday,tuesday,wednesday,thursday,friday'],
                'session_number' => ['required', 'integer', 'max:15'],
                'semester' => ['required', 'in:first,second', 'exists:subjects,semester,id,' . $this->input('subject_id')],
                'year' => ['required', 'string'],
                'class_id' => auth('user')->user()->hasRole('School Manager') ?
                    ['required', 'exists:classes,id', new ClassBelongToSchool($this->input('class_id'), auth('user')->user()->school_id), new SessionLimit($this->input('class_id'), $this->input('semester'), $this->input('year'), $this->input('day'))] :
                    ['required', 'exists:classes,id', new ClassBelongToSchool($this->input('class_id'), $this->input('school_id')), new SessionLimit($this->input('class_id'), $this->input('semester'), $this->input('year'), $this->input('day'))],
                'subject_id' => auth('user')->user()->hasRole('School Manager') ?
                    ['required', 'exists:subjects,id', new SubjectBelongToSchool(auth('user')->user()->school_id), new SubjectBelongToClassGrade($this->input('class_id'))] :
                    ['required', 'exists:subjects,id', new SubjectBelongToSchool($this->input('school_id')), new SubjectBelongToClassGrade($this->input('class_id'))],
                'teacher_id' => auth('user')->user()->hasRole('School Manager') ?
                    ['required', 'exists:teacher_profiles,id', new TeacherBelongToSchool($this->input('teacher_id'), auth('user')->user()->school_id)] :
                    ['required', 'exists:teacher_profiles,id', new TeacherBelongToSchool($this->input('teacher_id'), $this->input('school_id'))],
                'is_final' => ['required', 'boolean'],
            ];
            if (auth('user')->user()->hasRole('Super Admin')) {
                $rules['school_id'] = ['required', 'exists:schools,id'];
            } else {
                $rules['school_id'] = ['prohibited'];
            }
            return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'day' => 'Day',
            'session_number' => 'Session Number',
            'semester' => 'Semester',
            'year' => 'Year',
            'class_id' => 'Class',
            'subject_id' => 'Subject',
            'teacher_id' => 'Teacher',
            'school_id' => 'School',
            'is_final' => 'Is Final',
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