<?php

namespace Modules\Employee\Service;

use Modules\User\App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Modules\Common\Helpers\UploadHelper;
use Modules\Employee\App\Models\Employee;

class EmployeeService
{
    use UploadHelper;
    public function findAll($data, $relations)
    {
        $roles = ['Financial Director', 'Sales Employee', 'Purchasing Employee', 'Salaries Employee', 'Maintenance Employee','Teacher', 'Other',"DataEntry"];
        $employees = User::query()->available()->whereIn('role', $roles)->orWhereNull('role')->with($relations)->latest();
        return getCaseCollection($employees, $data);
    }

    public function create($data)
    {
        if (request()->hasFile('image')) {
            $image = request()->file('image');
            $imageName = $this->upload($image, 'employee');
            $data['image'] = $imageName;
        }
        $employee = Employee::create($data);
        $role = Role::findOrFail($data['role_id']);
        $employee->assignRole($role);
        return $employee;
    }

    public function changePassword($data)
    {
        $employee = auth('employee')->user();
        $employee->update([
            'password' => Hash::make($data['new_password'])
        ]);
    }


    public function updateProfile($data)
    {
        $employee = auth('employee')->user();

        if (request()->hasFile('image')) {
            if ($employee->image) {
                File::delete(public_path('uploads/employee/' . $this->getImageName('employee', $employee->image)));
            }
            $image = request()->file('image');
            $imageName = $this->upload($image, 'employee');
            $data['image'] = $imageName;
        }

        if ($employee->hasRole('School Manager')) {
            $employee->update($data);
            if ($data['name']) {
                $employee->school()->update(['name' => $data['name']]);
            }
        } else {
            $employee->update($data);
        }
    }


}
