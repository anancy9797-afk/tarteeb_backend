<?php

namespace App\Imports;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\User\App\Models\User;
use Illuminate\Support\Facades\Hash;
use Modules\School\App\Models\School;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SchoolsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                // Validation rules
                $rules = [
                    'name' => ['required', 'max:255'],
                    'email' => ['required', 'email', 'unique:users,email'],
                    'phone' => ['required', 'unique:users,phone'],
                    'password' => ['required', 'min:6'],
                ];

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

                $school = School::create([
                    'name' => $row['name'],
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $schoolManager = User::create([
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'phone' => $row['phone'],
                    'password' => Hash::make($row['password']),
                    'role' => 'School Manager',
                    'school_id' => $school->id,
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $schoolManager->assignRole('School Manager');
                // $gradesArray = explode(',', $row['grades']);
                // $school->grades()->sync(array_filter($gradesArray));
            }
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}