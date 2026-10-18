<?php

namespace Modules\Class\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Modules\Subject\App\Rules\GradeBelongToSchool;
use Illuminate\Http\Exceptions\HttpResponseException;

class ClassRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        if ($this->isMethod('POST')) {
            $rules = [
                'name' => ['required', 'string', 'max:255'],
                'grade_id' => auth('user')->user()->hasRole('School Manager') ?
                    ['required', 'exists:grades,id', new GradeBelongToSchool(auth('user')->user()->school_id)] :
                    ['required', 'exists:grades,id', new GradeBelongToSchool($this->input('school_id'))],
                'max_students' => ['required', 'integer'],
                'session_number' => ['required', 'integer'],
            ];
            if (auth('user')->user()->hasRole('Super Admin')) {
                $rules['school_id'] = ['required', 'exists:schools,id'];
            } else {
                $rules['school_id'] = ['prohibited'];
            }
            return $rules;
        }
        if ($this->isMethod('PUT')) {
            $rules = [
                'name' => ['nullable', 'string', 'max:255'],
                'grade_id' => auth('user')->user()->hasRole('School Manager') ?
                    ['nullable', 'exists:grades,id', new GradeBelongToSchool(auth('user')->user()->school_id)] :
                    ['nullable', 'exists:grades,id', new GradeBelongToSchool($this->input('school_id'))],
                'max_students' => ['nullable', 'integer'],
                'session_number' => ['nullable', 'integer'],
            ];
            if (auth('user')->user()->hasRole('Super Admin')) {
                $rules['school_id'] = ['nullable', 'exists:schools,id'];
            } else {
                $rules['school_id'] = ['prohibited'];
            }
            return $rules;
        }
        return [];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'name' => 'Name',
            'grade_id' => 'Grade',
            'max_students' => 'Max Students',
            'session_number' => 'Session Number',
            'school_id' => 'School',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if($this->isMethod('PUT')){
            $admin = auth('user')->user();
            if ($admin->hasRole('School Manager')) {
                // Check if the class's school ID matches the authenticated user's school ID
                return $admin->school_id == $this->class->school_id;
            }
        }
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