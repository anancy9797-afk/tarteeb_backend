<?php

namespace Modules\Maintenance\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class MaintenanceRequest extends FormRequest
{
    private $maintenance;
    public function prepareForValidation()
    {
        if ($this->route()->hasParameter('maintenance')) {
            $this->maintenance = $this->route('maintenance');
        } else {
            $this->maintenance = null;
        }
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'description' => ['required', 'string'],
            'date' => ['required', 'date'],
            'price' => ['required', 'numeric', 'min:0'],
        ];
        $rules['image'] = [$this->maintenance ? 'nullable' : 'required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:1024'];
        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'description' => 'Description',
            'image' => 'Image',
            'date' => 'Date',
            'price' => 'Price',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->maintenance) {
            $user = auth('user')->user();
            if ($this->maintenance->user_id !== $user->id) {
                throw new HttpResponseException(
                    returnMessage(
                        false,
                        'You are not authorized to update this maintenance',
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
