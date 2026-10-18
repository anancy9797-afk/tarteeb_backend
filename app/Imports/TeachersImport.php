<?php

namespace App\Imports;

use Exception;
use Modules\User\App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\Subject\App\Rules\GradeBelongToSchool;
use Modules\Subject\App\Rules\SubjectBelongToSchool;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Str;

class TeachersImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                if (isset($row['subjects'])) {
                    $row['subjects'] = array_filter(array_map('trim', explode(',', $row['subjects'])));
                }

                $rules = [
                    'name' => ['required', 'max:255'],
                    'email' => ['required', 'email', 'unique:users,email'],
                    'phone' => ['required', 'unique:users,phone'],
                    'password' => ['required', 'min:6'],
                    'gender' => ['required', 'in:m,f'],
                ];

                if (auth('user')->user()->hasRole('Super Admin')) {
                    $rules['school_id'] = ['required', 'exists:schools,id'];
                    // $rules['grade_id'] = ['required', 'exists:grades,id', new GradeBelongToSchool($row['grade_id'], $row['school_id'])];
                    $rules['subjects'] = ['required', 'array'];
                    $rules['subjects.*'] = ['required', 'exists:subjects,id', new SubjectBelongToSchool($row['school_id'])];
                } elseif (auth('user')->user()->hasRole('School Manager')) {
                    $schoolId = auth('user')->user()->school_id;
                    $rules['school_id'] = ['prohibited'];
                    // $rules['grade_id'] = ['required', 'exists:grades,id', new GradeBelongToSchool($row['grade_id'], $schoolId)];
                    $rules['subjects'] = ['required', 'array'];
                    $rules['subjects.*'] = ['required', 'exists:subjects,id', new SubjectBelongToSchool($schoolId)];
                }

                // Validate each row
                $validator = Validator::make($row->toArray(), $rules);

                if ($validator->fails()) {
                    throw new HttpResponseException(
                        returnValidationMessage(
                            false,
                            trans('validation.rules_failed'),
                            ['row' => $index + 1, 'errors' => $validator->errors()->messages()],
                            'unprocessable_entity'
                        )
                    );
                }
                $teacherData = [
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'phone' => $row['phone'],
                    'role' => 'Teacher',
                    'password' => Hash::make($row['password']),
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                if (auth('user')->user()->hasRole('Super Admin')) {
                    $teacherData['school_id'] = $row['school_id'];
                } elseif (auth('user')->user()->hasRole('School Manager')) {
                    $teacherData['school_id'] = auth('user')->user()->school_id;
                }
                // Create teacher if validation passes
                $teacher = User::create($teacherData);
                $teacher->assignRole('Teacher');

                $teacherProfileData = [
                    // 'grade_id' => $row['grade_id'],
                    'gender' => $row['gender'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $teacher->teacherProfile()->create($teacherProfileData);
                $teacher->teacherProfile->subjects()->attach($row['subjects']);
            }
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function toArray($row)
    {
        // Convert comma-separated subjects to array
        if (isset($row['subjects'])) {
            $row['subjects'] = array_filter(array_map('trim', explode(',', $row['subjects'])));
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:6'],
            'gender' => ['required', 'in:m,f'],
            'subjects' => ['required', 'array'],
            'subjects.*' => ['required', 'exists:subjects,id'],
            // ... other rules ...
        ];

        // Validate each row
        $validator = Validator::make($row->toArray(), $rules);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }

        return $row;
    }
}