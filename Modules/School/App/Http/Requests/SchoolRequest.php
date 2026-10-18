<?php

namespace Modules\School\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SchoolRequest extends FormRequest
{
    /**
     * Get the credentials for authentication.
     *
     * @return array<string, mixed>
     */
    public function credentials(): array
    {
        if ($this->isMethod('POST')) {
            return $this->only(['name', 'email', 'password', 'phone', 'image']);
        }
        if ($this->isMethod('PUT')) {
            return $this->only(['name', 'email', 'password', 'phone', 'image']);
        }
        return [];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        if ($this->isMethod('POST')) {
            return [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'unique:users,email'],
                'phone' => ['required', 'string', 'unique:users,phone'],
                'password' => ['required', 'string', 'min:6'],
                // 'grades' => ['required', 'array'],
                // 'grades.*' => ['required', 'exists:grades,id'],
                'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:1024'],
            ];
        }
        if ($this->isMethod('PUT')) {
            return [
                'name' => ['nullable', 'string', 'max:255'],
                'email' => ['nullable', 'email', 'unique:users,email,' . $this->school->manager->id],
                'phone' => ['nullable', 'string', 'unique:users,phone,' . $this->school->manager->id],
                'password' => ['nullable', 'string', 'min:6'],
                // 'grades' => ['nullable', 'array'],
                // 'grades.*' => ['nullable', 'exists:grades,id'],
                'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:1024'],
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
            'name' => 'Name',
            'email' => 'Email Address',
            'phone' => 'Phone Number',
            'grades' => 'Grades',
            'grades.*' => 'Grade',
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