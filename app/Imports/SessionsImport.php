<?php

namespace App\Imports;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Modules\Session\App\Models\Session;
use Illuminate\Support\Facades\Validator;
use Modules\Session\App\Rules\SessionLimit;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\Class\App\Rules\ClassBelongToSchool;
use Modules\Subject\App\Rules\SubjectBelongToSchool;
use Modules\Teacher\App\Rules\TeacherBelongToSchool;
use Illuminate\Http\Exceptions\HttpResponseException;
use Modules\Subject\App\Rules\SubjectBelongToClassGrade;

class SessionsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                // Base validation rules
                $rules = [
                    'day' => ['required', 'in:saturday,sunday,monday,tuesday,wednesday,thursday,friday'],
                    'session_number' => ['required', 'integer', 'max:15'],
                    'semester' => ['required', 'in:first,second', 'exists:subjects,semester,id,' . $row['subject_id']],
                    'year' => ['required','date_format:Y'],
                    'is_final' => ['required', 'boolean'],
                ];

                // Add rules based on user role
                if (auth('user')->user()->hasRole('Super Admin')) {
                    $rules['school_id'] = ['required', 'exists:schools,id'];
                    $rules['class_id'] = ['required', 'exists:classes,id',
                        new ClassBelongToSchool($row['class_id'], $row['school_id']),
                        new SessionLimit($row['class_id'], $row['semester'], $row['year'], $row['day'])
                    ];
                    $rules['subject_id'] = ['required', 'exists:subjects,id',
                        new SubjectBelongToSchool($row['school_id']),
                        new SubjectBelongToClassGrade($row['class_id'])
                    ];
                    $rules['teacher_id'] = ['required', 'exists:teacher_profiles,id',
                        new TeacherBelongToSchool($row['teacher_id'], $row['school_id'])
                    ];
                } elseif (auth('user')->user()->hasRole('School Manager')) {
                    $schoolId = auth('user')->user()->school_id;
                    $rules['school_id'] = ['prohibited'];
                    $rules['class_id'] = ['required', 'exists:classes,id',
                        new ClassBelongToSchool($row['class_id'], $schoolId),
                        new SessionLimit($row['class_id'], $row['semester'], $row['year'], $row['day'])
                    ];
                    $rules['subject_id'] = ['required', 'exists:subjects,id',
                        new SubjectBelongToSchool($schoolId),
                        new SubjectBelongToClassGrade($row['class_id'])
                    ];
                    $rules['teacher_id'] = ['required', 'exists:teacher_profiles,id',
                        new TeacherBelongToSchool($row['teacher_id'], $schoolId)
                    ];
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
                $data = [
                    'day' => $row['day'],
                    'session_number' => $row['session_number'],
                    'semester' => $row['semester'],
                    'year' => $row['year'],
                    'class_id' => $row['class_id'],
                    'subject_id' => $row['subject_id'],
                    'teacher_id' => $row['teacher_id'],
                    'is_final' => $row['is_final'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (auth('user')->user()->hasRole('Super Admin')) {
                    $data['school_id'] = $row['school_id'];
                }else if(auth('user')->user()->hasRole('School Manager')){
                    $data['school_id'] = auth('user')->user()->school_id;
                }
                // Create session if validation passes
                Session::create($data);
            }
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}