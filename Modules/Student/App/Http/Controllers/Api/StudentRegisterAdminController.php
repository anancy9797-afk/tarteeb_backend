<?php

namespace Modules\Student\App\Http\Controllers\Api;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Student\Service\StudentService;
use Modules\Student\App\Http\Requests\StudentUploadRegisterFeeReceiptAdminRequest;


class StudentRegisterAdminController extends Controller
{
    private $studentService;
    public function __construct(StudentService $studentService)
    {
        $this->middleware('auth:user');
        $this->middleware('role:School Manager|Financial Director');
        $this->studentService = $studentService;
    }

    public function index()
    {
        $relations = ['grade.gradeCategory', 'class'];
        $students = $this->studentService->findBy('is_fee_paid', 0, $relations);
        return returnMessage(true, 'Students fetched successfully', $students);
    }


    public function markAsPaid($id)
    {
        $student = $this->studentService->findById($id);
        $student->is_fee_paid = 1;
        $student->is_register_fee_accepted = 1;
        $student->save();
        return returnMessage(true, 'Student marked as paid successfully');
    }

    public function rejectRegisterFee($id)
    {
        $student = $this->studentService->findById($id);
        $student->is_register_fee_accepted = 0;
        $student->save();
        return returnMessage(true, 'Student register fee rejected successfully');
    }

    public function uploadRegisterFeeReceipt(StudentUploadRegisterFeeReceiptAdminRequest $request)
    {
        try {
            DB::beginTransaction();
            $student = $this->studentService->uploadRegisterFeeReceipt($request->validated());
            DB::commit();
            return returnMessage(true, 'Register Fee Receipt Uploaded Successfully and pending admin approval', $student);
        } catch (Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }
}
