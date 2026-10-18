<?php

namespace Modules\Student\App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Student\App\Models\StudentFee;
use Modules\Student\Service\StudentFeeService;
use Modules\Notification\Service\NotificationService;

class StudentFeeAdminController extends Controller
{
    private $studentFeeService;
    public function __construct(StudentFeeService $studentFeeService)
    {
        $this->middleware('auth:user');
        $this->middleware('role:School Manager|Financial Director|Sales Employee');
        $this->studentFeeService = $studentFeeService;
    }

    public function index(Request $request)
    {
        $relations = ['student.grade.gradeCategory'];
        $studentFees = $this->studentFeeService->findBySchoolStudents($request->all(), $relations);
        return returnMessage(true, 'Student Fees Fetched Successfully', $studentFees);
    }

    public function update(Request $request, StudentFee $studentFee)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'status' => 'required|in:accepted,rejected',
                'reason' => 'required_if:status,rejected|string',
            ]);
            $studentFee->update(['status' => $request->status, 'reason' => @$request->reason]);
            if ($request->status == 'accepted')
                $studentFee->student->update(['is_fee_paid' => 1]);
            $this->sendNotificationToUser($studentFee);
            DB::commit();
            return returnMessage(true, 'Student Fee Updated Successfully', $studentFee->fresh()->load('student'));
        } catch (Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function sendNotificationToUser($studentFee)
    {
        if ($studentFee->status == 'accepted') {
            $data = [
                'title' => 'تم قبول طلب دفع الاستمارة الخاصة بك',
                'description' => 'تم قبول طلب دفع الاستمارة الخاصة بك.',
            ];
        } elseif ($studentFee->status == 'rejected') {
            $data = [
                'title' => 'تم رفض طلب دفع الاستمارة الخاصة بك',
                'description' => 'تم رفض طلب دفع الاستمارة الخاصة بك. السبب: ' . ($studentFee->reason ?? 'لم يتم تحديد السبب'),
            ];
        }
        (new NotificationService())->sendNotificationToUser($data, $studentFee->student->user_id, 'student_fee');
    }
}
