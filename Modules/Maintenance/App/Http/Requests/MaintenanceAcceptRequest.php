<?php

namespace Modules\Maintenance\App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Maintenance\App\Models\Maintenance;

class MaintenanceAcceptRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'maintenance_ids' => ['required', 'array'],
            'maintenance_ids.*' => ['required', 'exists:maintenances,id']
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'maintenance_ids' => 'Maintenances',
            'maintenance_ids.*' => 'Maintenance',

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
                returnMessage(false, 'You are not authorized to accept maintenances', null, 'unauthorized')
            );
        }

        $ids = $this->input('maintenance_ids');

        $notAllowedIds = Maintenance::whereIn('id', $ids)
            ->where('school_id', '!=', $user->school_id)
            ->where('status' , '!=', Maintenance::STATUS_ACCEPTED)
            ->pluck('id')
            ->toArray();

        if (!empty($notAllowedIds)) {
            throw new HttpResponseException(
                returnMessage(false, 'You are not allowed to accept maintenance in ids [' . implode(', ', $notAllowedIds) . ']', null, 'unauthorized')
            );
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
