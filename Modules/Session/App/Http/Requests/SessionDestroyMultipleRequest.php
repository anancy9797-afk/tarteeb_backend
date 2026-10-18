<?php

namespace Modules\Session\App\Http\Requests;

use Modules\Session\App\Models\Session;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SessionDestroyMultipleRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'session_ids' => 'required|array',
            'session_ids.*' => 'required|exists:sessions,id',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'session_ids' => 'Session IDs',
            'session_ids.*' => 'Session ID',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $admin = auth('user')->user();
        if ($admin->hasRole('School Manager')) {
            $sessionIds = $this->input('session_ids');
            $sessions = Session::whereIn('id', $sessionIds)->get();

            foreach ($sessions as $session) {
                if ($admin->school_id != $session->school_id) {
                    throw new HttpResponseException(
                        returnUnauthorizeMessage(
                            false,
                            'Unauthorized access for session id ' . ($session->id),
                            null,
                            'unauthorized'
                        )
                    );
                }
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
