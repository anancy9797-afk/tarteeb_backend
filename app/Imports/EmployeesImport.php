<?php

namespace App\Imports;

use Modules\User\App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EmployeesImport implements ToCollection, WithHeadingRow, SkipsEmptyRows, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    private function getEmployeeRoles()
    {
        static $roles = null;
        if ($roles === null) {
            $roles = Role::whereIn('name', ['Financial Director', 'Sales Employee', 'Purchasing Employee', 'Salaries Employee', 'Maintenance Employee', 'Teacher','Other'])->get();
        }
        return $roles;
    }
    public function collection(Collection $rows)
    {
        DB::beginTransaction();
        $roles = $this->getEmployeeRoles();
        $roles_by_id = $roles->keyBy('id');
        $other_role = $roles->firstWhere('name', 'Other');
        try {
            foreach ($rows as $row) {
                $role_ids = array_filter(array_map('intval', explode(',', $row['role_ids'])));
                $selected_roles = $roles->whereIn('id', $role_ids);
                $has_other_role = $other_role && in_array($other_role->id, $role_ids);

                if ($has_other_role && empty($row['job_title'])) {
                    throw new \Exception('Job title is required when "Other" role is selected');
                }

                $user = User::create([
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'phone' => $row['phone'],
                    'role' => $selected_roles->first()->name,
                    'password' => bcrypt($row['password']) ?? null,
                    'job_title' => $has_other_role ? $row['job_title'] : null,
                    'school_id' => auth('user')->user()->hasRole('Super Admin') ? $row['school_id'] : auth('user')->user()->school_id,
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $user->syncRoles($selected_roles->pluck('name')->toArray());
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }


    /**
     * Get all failures
     */
    public function getFailures(): array
    {
        $failures = [];
        foreach ($this->failures() as $failure) {
            $rowKey = "row_{$failure->row()}";
            if (!isset($failures[$rowKey])) {
                $failures[$rowKey] = [];
            }
            foreach ($failure->errors() as $error) {
                $failures[$rowKey][] = $error;
            }
        }

        return $failures;
    }
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email', 'distinct'],
            'phone' => ['sometimes', 'nullable', 'max:255', 'unique:users,phone', 'distinct'],
            'password' => ['nullable', 'min:6'],
            'role_ids' => ['nullable'],
        ];

        if (auth('user')->user()->hasRole('Super Admin')) {
            $rules['school_id'] = ['required', 'exists:schools,id'];
        }

        return $rules;
    }
    public function attributes(): array
    {
        return [
            'name' => 'Name',
            'email' => 'Email Address',
            'phone' => 'Phone Number',
            'password' => 'Password',
            'role_ids' => 'Roles',
            'school_id' => 'School',
            'job_title' => 'Job Title',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'name.required' => 'Name is required',
            'name.string' => 'Name must be a string',
            'name.max' => 'Name must not exceed 255 characters',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'email.unique' => 'Email already exists',
            'phone.string' => 'Phone must be a string',
            'phone.max' => 'Phone must not exceed 255 characters',
            'phone.unique' => 'Phone already exists',
            'password.required' => 'Password is required',
            'password.string' => 'Password must be a string',
            'password.min' => 'Password must be at least 6 characters',
            'role_ids.required' => 'At least one role is required',
            'school_id.required' => 'School is required',
            'school_id.exists' => 'School does not exist',
        ];
    }
}
