<?php

namespace Modules\Student\App\Http\Controllers\Api;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Modules\User\DTO\StudentUserDto;
use Modules\User\Service\UserService;
use Modules\Grade\Service\GradeService;
use Modules\Common\Helpers\UploadHelper;
use Modules\School\Service\SchoolService;
use Modules\Student\DTO\StudentParentDto;
use Modules\Student\DTO\StudentRegisterDto;
use Modules\Student\Service\StudentService;
use Modules\User\App\resources\UserResource;
use Modules\Grade\Service\GradeCategoryService;
use Modules\Student\App\Http\Requests\StudentRegisterRequest;
use Modules\Student\App\Jobs\ParentNotificationOnStudentRegisterWhatsAppJob;
use Modules\Student\App\Http\Requests\StudentUploadRegisterFeeReceiptRequest;

class StudentRegisterController extends Controller
{
    use UploadHelper;
    private $studentService;
    private $userService;
    public function __construct(StudentService $studentService, UserService $userService)
    {
        $this->studentService = $studentService;
        $this->userService = $userService;
        $this->middleware('auth:user')->only('uploadRegisterFeeReceipt');
        $this->middleware('role:Student')->only('uploadRegisterFeeReceipt');
    }

    public function register(StudentRegisterRequest $request)
    {
        Log::info('Student register: started', [
            'school_id' => $request->input('school_id'),
            'grade_id' => $request->input('grade_id'),
        ]);

        try {
            DB::beginTransaction();
            $studentUserData = (new StudentUserDto($request))->dataFromRequest();
            $studentUser = $this->userService->saveStudentUser($studentUserData);
            $data = (new StudentRegisterDto($request))->dataFromRequest();
            $studentParentData = (new StudentParentDto($request))->dataFromRequest();
            $student = $this->studentService->create($data, $studentUser, $studentParentData);
            $token = auth('user')->login($studentUser);
            DB::commit();

            Log::info('Student register: transaction committed', [
                'student_id' => $student->id,
                'school_id' => $student->school_id,
                'user_id' => $studentUser->id ?? null,
            ]);

            $school = (new SchoolService())->findById($student->school_id, ['settings']);
            $settings = $school->settings;

            Log::info('Student register: loading school for WhatsApp notification', [
                'student_id' => $student->id,
                'school_id' => $school->id,
                'school_settings_exists' => $settings !== null,
                'school_has_ultramsg_token' => $settings !== null && filled($settings->ultramsg_token),
                'school_has_ultramsg_instance_id' => $settings !== null && filled($settings->ultramsg_instance_id),
                'parent_phone_suffix' => $this->maskedPhoneSuffix($student->parent_phone ?? null),
            ]);

            $this->sendParentNotification($student, $settings, $request->get('password'));

            Log::info('Student register: parent notification job dispatched', [
                'student_id' => $student->id,
                'school_id' => $student->school_id,
                'queue_connection' => 'database',
            ]);

            return $this->respondWithToken($token);
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Student register: failed', [
                'school_id' => $request->input('school_id'),
                'message' => $e->getMessage(),
                'exception' => $e::class,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    /**
     * Last 4 digits only for log correlation (avoid logging full numbers).
     */
    private function maskedPhoneSuffix(?string $phone): ?string
    {
        if ($phone === null || $phone === '') {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        return strlen($digits) >= 4 ? substr($digits, -4) : '****';
    }

    private function sendParentNotification($student, $schoolSettings, $password)
    {
        ParentNotificationOnStudentRegisterWhatsAppJob::dispatch($student, $schoolSettings, $password)->onConnection('database');
    }

    // public function uploadRegisterFeeReceipt(StudentUploadRegisterFeeReceiptRequest $request)
    // {
    //     try {
    //         $student = $this->studentService->findById(auth('user')->user()->student->id);
    //         $student->register_fee_image = $this->upload(request()->file('register_fee_image'), 'student/register_fee_image');
    //         $student->save();
    //         return returnMessage(true, 'Register Fee Receipt Uploaded Successfully', $student);
    //     } catch (Exception $e) {
    //         return returnMessage(false, $e->getMessage(), null, 'server_error');
    //     }
    // }
    // ... existing code ...

    public function uploadRegisterFeeReceipt(StudentUploadRegisterFeeReceiptRequest $request)
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

    // ... existing code ...
    public function schools()
    {
        $schools = (new SchoolService)->active();
        return returnMessage(true, 'Schools Fetched Successfully', $schools);
    }

    public function gradeCategories($school_id)
    {
        $gradeCategories = (new GradeCategoryService)->findBy('school_id', $school_id);
        return returnMessage(true, 'Grade Categories Fetched Successfully', $gradeCategories);
    }

    public function grades($grade_category_id)
    {
        $grades = (new GradeService)->findBy('grade_category_id', $grade_category_id);
        return returnMessage(true, 'Grades Fetched Successfully', $grades);
    }
    protected function respondWithToken($token)
    {
        return returnMessage(true, 'Successfully Registered', [
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('user')->factory()->getTTL() * 60,
            'user' => new UserResource(auth('user')->user()),
        ]);
    }
}
