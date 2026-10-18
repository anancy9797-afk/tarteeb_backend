<?php

namespace Modules\Purchase\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Purchase\App\Models\Purchase;

class PurchaseAcceptRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'purchase_ids' => ['required', 'array'],
            'purchase_ids.*' => ['required', 'exists:purchases,id']
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'purchase_ids' => 'Purchases',
            'purchase_ids.*' => 'Purchase',

        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth('user')->user();

        if ($user->hasRole('Super Admin')) {
            return true;
        }

        if (!$user->hasAnyRole(['School Manager', 'Financial Director']) || is_null($user->school_id)) {
            throw new HttpResponseException(
                returnMessage(false, 'You are not authorized to accept purchases', null, 'unauthorized')
            );
        }

        $ids = $this->input('purchase_ids');

        $notAllowedIds = Purchase::whereIn('id', $ids)
            ->where('school_id', '!=', $user->school_id)
            ->where('status' , '!=', Purchase::STATUS_ACCEPTED)
            ->pluck('id')
            ->toArray();

        if (!empty($notAllowedIds)) {
            throw new HttpResponseException(
                returnMessage(false, 'You are not allowed to accept purchase in ids [' . implode(', ', $notAllowedIds) . ']', null, 'unauthorized')
            );
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
