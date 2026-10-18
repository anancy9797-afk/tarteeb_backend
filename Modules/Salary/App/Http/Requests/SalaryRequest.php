<?php

namespace Modules\Salary\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SalaryRequest extends FormRequest
{
    private $salary;
    public function prepareForValidation()
    {
        $this->salary = $this->route()->hasParameter('salary') ? $this->route('salary') : null;
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id',],
            'salary' => ['required', 'numeric', 'min:0'],
            'month' => ['required', 'integer', 'min:1', 'max:12'],
            'year' => ['required', 'integer', 'max:65535'],
            'deduction' => ['sometimes', 'numeric', 'min:0'],
            'deduction_reason' => ['nullable', 'sometimes', 'string', 'max:255'],
            'bonus' => ['sometimes', 'numeric', 'min:0'],
            'bonus_reason' => ['nullable', 'sometimes', 'string', 'max:255'],
        ];

    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'user_id' => 'User ID',
            'salary' => 'Salary',
            'month' => 'Month',
            'year' => 'Year',
            'deduction' => 'Deduction',
            'deduction_reason' => 'Deduction Reason',
            'bonus' => 'Bonus',
            'bonus_reason' => 'Bonus Reason',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->salary) {
            $user = auth('user')->user();
            // if ($this->salary->created_by !== $user->id) {
            if ($this->salary->school_id !== $user->school_id) {
                throw new HttpResponseException(
                    returnMessage(
                        false,
                        'You are not authorized to update this salary',
                        null,
                        'unauthorized'
                    )
                );
            }
        }

        return true;
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
