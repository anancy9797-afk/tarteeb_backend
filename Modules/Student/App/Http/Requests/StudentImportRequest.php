<?php


namespace Modules\Student\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class StudentImportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
            'pdf_zip' => ['required', 'file', 'mimes:zip', 'max:102400'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Excel file is required',
            'file.mimes' => 'Excel file must be xlsx, xls, or csv',
            'pdf_zip.required' => 'PDF ZIP file is required',
            'pdf_zip.mimes' => 'PDF file must be a ZIP archive',
        ];
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
