<?php

namespace Modules\Common\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class IntroStoreRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
            return [
                'title_ar' => ['required', 'string', 'max:255'],
                'title_en' => ['required', 'string', 'max:255'],
                'description_ar' => ['required', 'string'],
                'description_en' => ['required', 'string'],
                'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:1024'],
                'section' => ['required', 'string'],
                'details' => ['nullable', 'array'],
                'details.*.title_ar' => ['nullable', 'string', 'max:255'],
                'details.*.title_en' => ['nullable', 'string', 'max:255'],
                'details.*.description_ar' => ['nullable', 'string'],
                'details.*.description_en' => ['nullable', 'string'],
                'details.*.image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:1024'],
            ];

    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'title_ar' => 'Arabic Title',
            'title_en' => 'English Title',
            'description_ar' => 'Arabic Description',
            'description_en' => 'English Description',
            'image' => 'Image',
            'section' => 'Section',
            'parent_id' => 'Parent',
            'details' => 'Details',
            'details.*.title_ar' => 'Details Arabic Title',
            'details.*.title_en' => 'Details English Title',
            'details.*.description_ar' => 'Details Arabic Description',
            'details.*.description_en' => 'Details English Description',
            'details.*.image' => 'Details Image',
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