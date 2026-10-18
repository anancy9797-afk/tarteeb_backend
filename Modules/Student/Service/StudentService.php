<?php

namespace Modules\Student\Service;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Modules\Class\App\Models\Classroom;
use Modules\Student\App\Models\Student;
use Modules\Common\Helpers\UploadHelper;

class StudentService
{
    use UploadHelper;
    function findAll($data = [])
    {
        $students = Student::query()
            ->when($data['name'] ?? null, function ($query) use ($data) {
                $query->where('name', 'like', '%' . $data['name'] . '%');
            })
            ->when($data['email'] ?? null, function ($query) use ($data) {
                $query->where('email', 'like', '%' . $data['email'] . '%');
            })
            ->when($data['grade_id'] ?? null, function ($query) use ($data) {
                $query->where('grade_id', $data['grade_id']);
            })
            ->when($data['grade_category_id'] ?? null, function ($query) use ($data) {
                $query->whereHas('grade', function ($query) use ($data) {
                    $query->where('grade_category_id', $data['grade_category_id']);
                });
            })
            ->where(function ($query) {
                $query->where('is_register', 0)
                    ->orWhere(function ($query) {
                        $query->where('is_register', 1)
                            ->whereIn('id', function ($query) {
                                $query->select('student_expenses.student_id')
                                    ->from('student_expenses')
                                    ->join('expense_details', function ($join) {
                                        $join->on('expense_details.expense_id', '=', 'student_expenses.expense_id')
                                            ->where('expense_details.name', '=', 'مقدم الدفع');
                                    })
                                    ->where('student_expenses.status', 'accepted');
                            });
                    });
            })
            ->availableAll()
            ->with('grade.gradeCategory', 'school')
            ->orderByDesc('id');
        return getCaseCollection($students, $data);
    }

    function findById($id)
    {
        $student = Student::available()->findOrFail($id);
        return $student;
    }

    function findBy($key, $value, $relations = [])
    {
        $student = Student::available()->where($key, $value)->with($relations)->get();
        return $student;
    }

    function create($data, $studentUser, $studentParentData)
    {
        if (request()->hasFile('application_form')) {
            $data['application_form'] = $this->uploadFile(request()->file('application_form'), 'student/application_form');
        }
        if (request()->hasFile('parent_identity_card_image')) {
            $data['parent_identity_card_image'] = $this->uploadFile(request()->file('parent_identity_card_image'), 'student/parent_identity_card_image');
        }
        if (request()->hasFile('student_residence_card_image')) {
            $data['student_residence_card_image'] = $this->uploadFile(request()->file('student_residence_card_image'), 'student/student_residence_card_image');
        }
        if (request()->hasFile('image')) {
            $data['image'] = $this->uploadFile(request()->file('image'), 'student');
        }
        if (request()->hasFile('student_passport_image')) {
            $data['student_passport_image'] = $this->uploadFile(request()->file('student_passport_image'), 'student/student_passport_image');
        }
        if (request()->hasFile('student_birth_certificate_image')) {
            $data['student_birth_certificate_image'] = $this->uploadFile(request()->file('student_birth_certificate_image'), 'student/student_birth_certificate_image');
        }
        if (request()->hasFile('student_health_card_image')) {
            $data['student_health_card_image'] = $this->uploadFile(request()->file('student_health_card_image'), 'student/student_health_card_image');
        }
        if (request()->hasFile('home_map_image')) {
            $data['home_map_image'] = $this->uploadFile(request()->file('home_map_image'), 'student/home_map_image');
        }
        $student = $studentUser->student()->create($data);
        $student->parent()->create($studentParentData);
        return $student;
    }

    function update($student, $data, $studentUserData = null, $studentParentData = null)
    {
        // Handle file uploads
        if (request()->hasFile('application_form')) {
            if ($student->application_form) {
                File::delete(public_path('uploads/student/application_form/' . $student->application_form));
            }
            $data['application_form'] = $this->uploadFile(request()->file('application_form'), 'student/application_form');
        }
        if (request()->hasFile('parent_identity_card_image')) {
            if ($student->parent_identity_card_image) {
                File::delete(public_path('uploads/student/parent_identity_card_image/' . $student->parent_identity_card_image));
            }
            $data['parent_identity_card_image'] = $this->uploadFile(request()->file('parent_identity_card_image'), 'student/parent_identity_card_image');
        }
        if (request()->hasFile('student_residence_card_image')) {
            if ($student->student_residence_card_image) {
                File::delete(public_path('uploads/student/student_residence_card_image/' . $student->student_residence_card_image));
            }
            $data['student_residence_card_image'] = $this->uploadFile(request()->file('student_residence_card_image'), 'student/student_residence_card_image');
        }
        if (request()->hasFile('image')) {
            if ($student->image) {
                File::delete(public_path('uploads/student/' . $student->image));
            }
            $data['image'] = $this->uploadFile(request()->file('image'), 'student');
        }
        if (request()->hasFile('student_passport_image')) {
            if ($student->student_passport_image) {
                File::delete(public_path('uploads/student/student_passport_image/' . $student->student_passport_image));
            }
            $data['student_passport_image'] = $this->uploadFile(request()->file('student_passport_image'), 'student/student_passport_image');
        }
        if (request()->hasFile('student_birth_certificate_image')) {
            if ($student->student_birth_certificate_image) {
                File::delete(public_path('uploads/student/student_birth_certificate_image/' . $student->student_birth_certificate_image));
            }
            $data['student_birth_certificate_image'] = $this->uploadFile(request()->file('student_birth_certificate_image'), 'student/student_birth_certificate_image');
        }
        if (request()->hasFile('student_health_card_image')) {
            if ($student->student_health_card_image) {
                File::delete(public_path('uploads/student/student_health_card_image/' . $student->student_health_card_image));
            }
            $data['student_health_card_image'] = $this->uploadFile(request()->file('student_health_card_image'), 'student/student_health_card_image');
        }
        if (request()->hasFile('home_map_image')) {
            if ($student->home_map_image) {
                File::delete(public_path('uploads/student/home_map_image/' . $student->home_map_image));
            }
            $data['home_map_image'] = $this->uploadFile(request()->file('home_map_image'), 'student/home_map_image');
        }

        $student->update($data);

        if ($studentUserData && $student->user_id) {
            $userUpdateData = [];
            if (isset($studentUserData['name'])) {
                $userUpdateData['name'] = $studentUserData['name'];
            }
            if (isset($studentUserData['email'])) {
                $userUpdateData['email'] = $studentUserData['email'];
            }
            if (isset($studentUserData['phone'])) {
                $userUpdateData['phone'] = $studentUserData['phone'];
            }
            if (isset($studentUserData['password'])) {
                $userUpdateData['password'] = $studentUserData['password'];
            }
            if (!empty($userUpdateData)) {
                $student->user->update($userUpdateData);
            }
        }


        if ($studentParentData && !empty($studentParentData)) {
            $student->parent()->updateOrCreate(
                ['student_id' => $student->id],
                $studentParentData
            );
        }

        return $student->fresh('user', 'parent', 'grade', 'class', 'school', 'branch', 'state.governorate');
    }

    function delete($student)
    {
        $student->delete();
    }

    function activate($id)
    {
        $student = $this->findById($id);
        $student->is_active = !$student->is_active;
        $student->save();
    }

    function getStudentsToGraduate($data = [])
    {
        $students = Student::available()->where('is_graduated', 0)->whereHas('grade', function ($query) {
            $query->where('is_final', 1);
        });
        return getCaseCollection($students, $data);
    }

    function graduate($studentsIds)
    {
        Student::whereIn('id', $studentsIds)->update(['is_graduated' => 1]);
    }

    function getStudentsToUpgrade($data = [], $class_id)
    {
        $students = Student::query()
            ->available()
            ->where('is_graduated', 0)
            ->where('class_id', $class_id);
        return getCaseCollection($students, $data);
    }
    function upgrade($data)
    {
        $class = Classroom::where('id', $data['class_id'])->first();
        Student::whereIn('id', $data['student_ids'])
            ->update([
                'class_id' => $class->id,
                'grade_id' => $class->grade_id
            ]);
    }

    function uploadRegisterFeeReceipt($data)
    {
        if (isset($data['student_id'])) {
            $student = (new StudentService())->findById($data['student_id']);
        } else {
            $student = auth('user')->user()->student;
        }

        $firstExpense = \Modules\Expense\App\Models\Expense::query()
            ->where('grade_id', $student->grade_id)
            ->where('grade_category_id', $student->grade->grade_category_id)
            ->where('school_id', $student->school_id)
            ->with(['details', 'installments'])
            ->oldest()
            ->first();

        if (!$firstExpense) {
            throw new \Exception('No expenses found for your grade');
        }

        // $startPaymentDetail = $firstExpense->installments->firstWhere('name', 'مقدم الدفع');
        // $startPaymentDetail = $firstExpense->details->firstWhere('name', 'مقدم الدفع');

        // if (!$startPaymentDetail) {
        //     throw new \Exception('Registration fee (مقدم الدفع) not configured for this grade');
        // }

        $existingRegistrationExpense = \Modules\Expense\App\Models\StudentExpense::query()
            ->where('student_id', $student->id)
            ->where('is_registration_fee', true)
            ->whereIn('status', ['pending', 'accepted'])
            ->first();

        if ($existingRegistrationExpense) {
            throw new \Exception('Registration fee expense already submitted and is ' . $existingRegistrationExpense->status);
        }

        if ($student->expense_registration_fee_deducted > 0 || $student->is_register_fee_accepted) {
            throw new \Exception('Registration fee already processed');
        }

        if (request()->hasFile('register_fee_image')) {
            $data['register_fee_image'] = $this->upload(request()->file('register_fee_image'), 'student/register_fee_image');
            $data['receipt'] = $this->upload(request()->file('register_fee_image'), 'student/expense/receipt');
        }
        $studentExpense = \Modules\Expense\App\Models\StudentExpense::create([
            'student_id' => $student->id,
            'expense_id' => $firstExpense->id,
            'amount' => $firstExpense->price,
            // 'amount_paid' => $startPaymentDetail->price,
            'amount_paid' => null,
            'date' => now()->toDateString(),
            'payment_method' => $data['payment_method'],
            'receipt' => $data['receipt'],
            'status' => 'pending',
            'is_registration_fee' => 1,
        ]);

        $student->update(['register_fee_image' => $data['register_fee_image']]);

        $notificationData = [
            'title' => 'تم رفع إيصال رسوم التسجيل',
            'description' => 'قام الطالب ' . $student->name . ' برفع إيصال رسوم التسجيل وينتظر المراجعة',
        ];
        (new \Modules\Notification\Service\NotificationService())->sendNotificationToAdmins(
            $notificationData,
            $student->school_id,
            'registration_fee'
        );
        return $studentExpense;
    }
}
