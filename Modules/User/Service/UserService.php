<?php

namespace Modules\User\Service;

use Modules\User\App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Modules\Common\Helpers\UploadHelper;
use Modules\Employee\App\Models\Employee;

class UserService
{
    use UploadHelper;

    public function changePassword($data)
    {
        $user = auth('user')->user();
        $user->update([
            'password' => Hash::make($data['new_password'])
        ]);
        if ($user->hasRole('Student')) {
            $user->student->update(['password' => $data['new_password']]);
        }
    }

    public function updateProfile($data)
    {
        $user = auth('user')->user();
        $gender = @$data['gender'];
        unset($data['gender']);

        if (request()->hasFile('image')) {
            if ($user->image) {
                File::delete(public_path('uploads/user/' . $this->getImageName('user', $user->image)));
            }
            $image = request()->file('image');
            $imageName = $this->upload($image, 'user');
            $data['image'] = $imageName;
        }

        if ($user->hasRole('Teacher')) {
            $user->update($data);
            if ($gender) {
                $user->teacherProfile()->update(['gender' => $gender]);
            }
        } else if ($user->hasRole('School Manager')) {
            $user->update($data);
            if ($data['name']) {
                $user->school()->update(['name' => $data['name']]);
            }
        } elseif ($user->hasRole('Student')) {
            // Extract parent data fields
            $parentFields = [
                'parent_name',
                'parent_nationality',
                'parent_identity_number',
                'parent_job',
                'parent_job_address',
                'parent_education_level',
                'mother_name',
                'mother_nationality',
                'mother_identity_number',
                'mother_job',
                'mother_job_address',
                'mother_education_level',
                'mother_phone',
                'parents_status',
                'relative_name',
                'relative_relation',
                'relative_phone',
            ];

            $parentData = [];
            foreach ($parentFields as $field) {
                if (isset($data[$field])) {
                    $parentData[$field] = $data[$field];
                    unset($data[$field]);
                }
            }

            // Extract user-specific fields (phone belongs to users table, not students)
            $userFields = ['phone'];
            $userUpdateData = [];
            foreach ($userFields as $field) {
                if (isset($data[$field])) {
                    $userUpdateData[$field] = $data[$field];
                    unset($data[$field]);
                }
            }

            // Handle student image upload (separate from user image)
            if (request()->hasFile('image')) {
                if ($user->student->image) {
                    File::delete(public_path('uploads/student/' . $this->getImageName('student', $user->student->image)));
                }
                $data['image'] = $this->upload(request()->file('image'), 'student');
            }

            if (request()->hasFile('application_form')) {
                if ($user->student->application_form) {
                    File::delete(public_path('uploads/student/application_form/' . $this->getImageName('student', $user->student->application_form)));
                }
                $data['application_form'] = $this->uploadFile(request()->file('application_form'), 'student/application_form');
            }
            if (request()->hasFile('parent_identity_card_image')) {
                if ($user->student->parent_identity_card_image) {
                    File::delete(public_path('uploads/student/parent_identity_card_image/' . $this->getImageName('student', $user->student->parent_identity_card_image)));
                }
                $data['parent_identity_card_image'] = $this->upload(request()->file('parent_identity_card_image'), 'student/parent_identity_card_image');
            }
            if (request()->hasFile('student_residence_card_image')) {
                if ($user->student->student_residence_card_image) {
                    File::delete(public_path('uploads/student/student_residence_card_image/' . $this->getImageName('student', $user->student->student_residence_card_image)));
                }
                $data['student_residence_card_image'] = $this->upload(request()->file('student_residence_card_image'), 'student/student_residence_card_image');
            }
            if (request()->hasFile('student_passport_image')) {
                if ($user->student->student_passport_image) {
                    File::delete(public_path('uploads/student/student_passport_image/' . $this->getImageName('student', $user->student->student_passport_image)));
                }
                $data['student_passport_image'] = $this->upload(request()->file('student_passport_image'), 'student/student_passport_image');
            }
            if (request()->hasFile('student_birth_certificate_image')) {
                if ($user->student->student_birth_certificate_image) {
                    File::delete(public_path('uploads/student/student_birth_certificate_image/' . $this->getImageName('student', $user->student->student_birth_certificate_image)));
                }
                $data['student_birth_certificate_image'] = $this->upload(request()->file('student_birth_certificate_image'), 'student/student_birth_certificate_image');
            }
            if (request()->hasFile('student_health_card_image')) {
                if ($user->student->student_health_card_image) {
                    File::delete(public_path('uploads/student/student_health_card_image/' . $this->getImageName('student', $user->student->student_health_card_image)));
                }
                $data['student_health_card_image'] = $this->upload(request()->file('student_health_card_image'), 'student/student_health_card_image');
            }
            if (request()->hasFile('home_map_image')) {
                if ($user->student->home_map_image) {
                    File::delete(public_path('uploads/student/home_map_image/' . $this->getImageName('student', $user->student->home_map_image)));
                }
                $data['home_map_image'] = $this->upload(request()->file('home_map_image'), 'student/home_map_image');
            }
            
            // Update student record
            $user->student()->update($data);

            // Update parent record only if it exists
            if (!empty($parentData) && $user->student->parent) {
                $user->student->parent()->update($parentData);
            }

            // Update user record with name, email, and phone
            if (isset($data['name'])) {
                $userUpdateData['name'] = $data['name'];
            }
            if (isset($data['email'])) {
                $userUpdateData['email'] = $data['email'];
            }
            if (!empty($userUpdateData)) {
                $user->update($userUpdateData);
            }
        } else {
            $user->update($data);
        }
    }


    public function saveStudentUser($data)
    {
        $user = User::create($data);
        $user->assignRole('Student');
        return $user;
    }
    public function saveEmployeeUser($data)
    {
        if (isset($data['role_ids']) && !empty($data['role_ids'])) {
            $roles = Role::whereIn('id', $data['role_ids'])->get();
            if ($roles->isNotEmpty()) {
                $data['role'] = $roles->first()->name;
                $user = User::create($data);
                $roleNames = $roles->pluck('name')->toArray();
                $user->assignRole($roleNames);
            }
        } else {
            $user = User::create($data);
        }
        return $user->fresh('roles');
    }

    public function updateEmployeeUser(array $data, User $user): User
    {
        if (isset($data['role_ids']) && !empty($data['role_ids'])) {
            $roles = Role::whereIn('id', $data['role_ids'])->get();
            if ($roles->isNotEmpty()) {
                $data['role'] = $roles->first()->name;
                $roleNames = $roles->pluck('name')->toArray();
                $user->syncRoles($roleNames);
                $hasOtherRole = $roles->contains(function ($role) {
                    return $role->name === 'Other';
                });
                if (!$hasOtherRole) {
                    $data['job_title'] = null;
                }
            }
            unset($data['role_ids']);
        }
        $user->update($data);
        return $user->fresh('roles');
    }

    public function findToken($user_id)
    {
        $user = User::find($user_id);
        return $user->fcm_token;
    }
    public function findTokens($user_ids)
    {
        $tokens = User::whereIn('id', $user_ids)->whereNotNull('fcm_token')->pluck('fcm_token')->toArray();
        return $tokens;
    }

    public function toggleActivate(User $user)
    {
        $user->is_active = !$user->is_active;
        $user->save();
        return $user->fresh();
    }
}
