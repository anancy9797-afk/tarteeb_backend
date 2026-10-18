<?php

namespace Modules\Expense\App\Http\Requests;

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ExpenseRequest extends FormRequest
{
    private $expense;
    public function prepareForValidation()
    {
        $this->expense = $this->route()->hasParameter('expense') ? $this->route('expense') : null;
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $isUpdate = $this->expense !== null;

        return [
            'grade_category_id' => [$isUpdate ? 'sometimes' : 'required', 'exists:grade_categories,id'],
            'grade_id' => [$isUpdate ? 'sometimes' : 'required', 'exists:grades,id'],
            'price' => [$isUpdate ? 'sometimes' : 'required', 'numeric', 'min:0'],
            'details' => [$isUpdate ? 'sometimes' : 'required', 'array'],
            'details.*.name' => ['required', 'string', 'max:255'],
            'details.*.price' => ['required', 'numeric', 'min:0'],
            'installments' => ['nullable', 'array'],
            'installments.*.name' => ['nullable', 'string', 'max:255'],
            'installments.*.price' => ['nullable', 'integer', 'min:0'],
            'installments.*.is_optional' => ['nullable', 'in:1,0'],
        ];

    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function (Validator $validator) {
            $price = $this->input('price');
            $details = $this->input('details');

            // $detailsSum = collect($details)->sum('price');
            // if ($detailsSum != $price) {
            //     $validator->errors()->add('details', 'The details price must be equal to the price');
            // }

            $installments = $this->input('installments');
            if ($installments) {
                $installmentsSum = collect($installments)
                    ->filter(fn($installment) => !($installment['is_optional'] == 1))
                    ->sum('price');
                if ($installmentsSum != $price) {
                    $validator->errors()->add('installments', 'The non-optional installments price must be equal to the price');
                }
            }
        });
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'grade_category_id' => 'Grade Category ID',
            'grade_id' => 'Grade ID',
            'price' => 'Price',
            'details' => 'Details',
            'details.*.name' => 'Name',
            'details.*.price' => 'Price',
            'installments' => 'Installments',
            'installments.*.title' => 'Title',
            'installments.*.price' => 'Price',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        if ($this->expense) {
            $user = auth('user')->user();
            if ($this->expense->user_id !== $user->id) {
                throw new HttpResponseException(
                    returnMessage(
                        false,
                        'You are not authorized to update this expense',
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
