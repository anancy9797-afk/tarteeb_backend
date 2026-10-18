<?php

namespace Modules\Employee\App\Http\Requests;

use Spatie\Permission\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class EmployeeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $roles = Role::whereIn('name', ['Financial Director', 'Teacher', 'Sales Employee', 'Purchasing Employee', 'Salaries Employee', 'Maintenance Employee',"DataEntry", 'Other'])->get();
        $roles_ids = $roles->pluck('id');
        $other_role_id = $roles->firstWhere('name', 'Other')?->id;
        $rules = [
            'image' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
        ];
        if ($this->route('user')) {
            $rules['name'] = 'nullable|string|max:255';
            $rules['email'] = 'nullable|email|unique:users,email,' . $this->route('user')->id;
            $rules['phone'] = 'sometimes|nullable|string|max:255|unique:users,phone,' . $this->route('user')->id;
            $rules['role_ids'] = 'nullable|array';
            $rules['role_ids.*'] = 'exists:roles,id|in:' . $roles_ids->implode(',');
            $rules['password'] = 'nullable|string|min:6';
            $has_other_role = $this->hasOtherRole($other_role_id);
            $rules['job_title'] = $has_other_role
                ? 'required|string|max:255'
                : 'nullable';
            if (auth('user')->user()->hasRole('Super Admin'))
                $rules['school_id'] = 'nullable|exists:schools,id';
        } else {
            $rules['name'] = 'required|string|max:255';
            $rules['email'] = 'required|email|unique:users,email';
            $rules['phone'] = 'sometimes|nullable|string|max:255|unique:users,phone';
            $rules['role_ids'] = 'nullable|array';
            $rules['role_ids.*'] = 'exists:roles,id|in:' . $roles_ids->implode(',');
            $has_other_role = $this->hasOtherRole($other_role_id);
            $rules['job_title'] = $has_other_role
                ? 'required|string|max:255'
                : 'nullable|prohibited';
            $rules['password'] = 'nullable|string|min:6';
            if (auth('user')->user()->hasRole('Super Admin'))
                $rules['school_id'] = 'required|exists:schools,id';
        }
        return $rules;
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
            'password' => 'Password',
            'image' => 'Image',
            'role_id' => 'Role',
            'role_ids' => 'Roles',
            'school_id' => 'School',
            'job_title' => 'Job Title',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    private function hasOtherRole(?int $other_role_id): bool
    {
        if (!$other_role_id) {
            return false;
        }

        $role_ids = $this->input('role_ids', []);

        return in_array($other_role_id, $role_ids);
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
