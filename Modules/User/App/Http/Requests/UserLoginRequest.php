<?php

namespace Modules\User\App\Http\Requests;

use Modules\Student\App\Models\Student;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserLoginRequest extends FormRequest
{
    /**
     * Get the credentials for authentication.
     *
     * @return array<string, mixed>
     */
   public function credentials(): array
    {
        if ($this->filled('identity_number')) {
            $student = Student::where('identity_number', $this->identity_number)->first();

            if ($student && $student->user_id) {
                return [
                    'user_id' => $student->user_id,
                    'password' => $this->password,
                ];
            }

            return ['user_id' => null, 'password' => ''];
        }

        return $this->only(['email', 'password']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => ['required_without:identity_number', 'nullable', 'email', 'exists:users,email'],
            'identity_number' => ['required_without:email', 'nullable', 'string', 'exists:students,identity_number'],
            'password' => ['required', 'string'],
            'fcm_token' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'email' => 'Email Address',
            'identity_number' => 'Identity Number',
            'password' => 'Password',
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
