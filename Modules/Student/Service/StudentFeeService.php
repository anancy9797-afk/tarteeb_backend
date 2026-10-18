<?php

namespace Modules\Student\Service;

use Modules\Common\Helpers\UploadHelper;
use Modules\Student\App\Models\StudentFee;
use Modules\Student\Service\StudentService;

class StudentFeeService
{
    use UploadHelper;

    function findBySchoolStudents($data = [] , $relations = [])
    {
        $schoolId = auth('user')->user()->school_id;
        $students = (new StudentService())->findBy('school_id', $schoolId)->pluck('id');
        $studentFees = StudentFee::whereIn('student_id', $students)->with($relations)->latest();
        return getCaseCollection($studentFees, $data);
    }

    public function findByStudent($data = [])
    {
        $studentId = auth('user')->id();
        $studentFees = StudentFee::where('student_id', $studentId)->latest();
        return getCaseCollection($studentFees, $data);
    }
    public function save($data)
    {
        if (request()->hasFile('receipt'))
            $data['receipt'] = $this->upload(request()->file('receipt'), 'student/receipt');
        return StudentFee::create($data);
    }

    public function update($data, $studentFee)
    {
        if (request()->hasFile('receipt'))
            $data['receipt'] = $this->upload(request()->file('receipt'), 'student/receipt');
        $studentFee->update($data);
        return $studentFee;
    }
}
