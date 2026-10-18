<?php

namespace Modules\Grade\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Modules\Grade\App\Rules\GradeCategoryBelongToSchool;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class GradeRequest extends FormRequest
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
                'grades' => ['required', 'array'],
                'grades.*.name' => ['required', 'string', 'max:255'],
                'grade_category_id' => auth('user')->user()->hasRole('School Manager') ?
                    ['required', 'exists:grade_categories,id', new GradeCategoryBelongToSchool($this->input('grade_category_id'), auth('user')->user()->school_id)] :
                    ['required', 'exists:grade_categories,id', new GradeCategoryBelongToSchool($this->input('grade_category_id'), $this->input('school_id'))],
                'grades.*.is_final' => ['nullable', 'boolean'],
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
                'grade_category_id' => auth('user')->user()->hasRole('School Manager') ?
                    ['nullable', 'exists:grade_categories,id', new GradeCategoryBelongToSchool($this->input('grade_category_id'), auth('user')->user()->school_id)] :
                    ['nullable', 'exists:grade_categories,id', new GradeCategoryBelongToSchool($this->input('grade_category_id'), $this->input('school_id'))],
                'is_final' => ['nullable', 'boolean'],
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
            'school_id' => 'School',
            'grade_category_id' => 'Grade Category',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->isMethod('PUT')) {
            $admin = auth('user')->user();
            if ($admin->hasRole('School Manager')) {
                // Check if the grades's school ID matches the authenticated user's school ID
                return $admin->school_id == $this->grade->school_id;
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