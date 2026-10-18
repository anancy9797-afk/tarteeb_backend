<?php

namespace Modules\Student\App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Student\DTO\StudentDto;
use App\Http\Controllers\Controller;
use Modules\User\DTO\StudentUserDto;
use Modules\User\Service\UserService;
use Modules\School\Service\SchoolService;
use Modules\Student\App\Models\Student;
use Modules\Student\DTO\StudentParentDto;
use Modules\Student\DTO\StudentRegisterDto;
use Modules\Student\Service\StudentService;
use Modules\Student\Service\StudentImportService;
use Modules\Student\App\resources\StudentResource;
use Modules\Student\App\Http\Requests\StudentRequest;
use Modules\Student\App\Jobs\ParentNotificationOnStudentRegisterWhatsAppJob;
use Modules\Student\App\Http\Requests\UpgradeRequest;
use Modules\Student\App\Http\Requests\GraduateRequest;
use Modules\Student\App\Http\Requests\StudentImportRequest;
use Modules\Student\App\Http\Requests\StudentUpdateRequest;

class StudentController extends Controller
{
    private $studentService;
    public function __construct(StudentService $studentService)
    {
        $this->middleware('auth:user');
        $this->middleware('role:Super Admin|School Manager|Teacher|Financial Director');
        $this->studentService = $studentService;
    }
    public function index(Request $request)
    {
        $data = $request->all();
        $students = $this->studentService->findAll($data);
        return returnMessage(true, 'Students Fetched Successfully', StudentResource::collection($students)->response()->getData(true));
    }

    public function store(StudentRequest $request, UserService $userService)
    {
        Log::info('Student store (admin): started', [
            'school_id' => $request->input('school_id'),
            'grade_id' => $request->input('grade_id'),
        ]);

        try {
            DB::beginTransaction();
            $studentUserData = (new StudentUserDto($request))->dataFromRequest();
            $studentUser = $userService->saveStudentUser($studentUserData);
            $data = (new StudentRegisterDto($request))->dataFromRequest();
            $studentParentData = (new StudentParentDto($request))->dataFromRequest();
            $student = $this->studentService->create($data, $studentUser, $studentParentData);
            DB::commit();

            Log::info('Student store (admin): transaction committed', [
                'student_id' => $student->id,
                'school_id' => $student->school_id,
                'user_id' => $studentUser->id ?? null,
            ]);

            $school = (new SchoolService())->findById($student->school_id, ['settings']);
            $settings = $school->settings;

            Log::info('Student store (admin): school settings for WhatsApp', [
                'student_id' => $student->id,
                'school_id' => $school->id,
                'school_settings_exists' => $settings !== null,
                'school_has_ultramsg_token' => $settings !== null && filled($settings->ultramsg_token),
                'school_has_ultramsg_instance_id' => $settings !== null && filled($settings->ultramsg_instance_id),
                'env_ultramsg_configured' => filled(config('services.ultramsg.token')) && filled(config('services.ultramsg.instance_id')),
                'parent_phone_suffix' => $this->maskedPhoneSuffix($student->parent_phone ?? null),
            ]);

            // ParentNotificationOnStudentRegisterWhatsAppJob::dispatch($student, $settings, $request->get('password'))
            //     ->onConnection('database');

            Log::info('Student store (admin): parent WhatsApp job dispatched', [
                'student_id' => $student->id,
                'school_id' => $student->school_id,
                'queue_connection' => 'database',
            ]);

            return returnMessage(true, 'Student Created Successfully', $student);
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Student store (admin): failed', [
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

    public function update(StudentUpdateRequest $request, Student $student)
    {
        try {
            DB::beginTransaction();
            $studentUserData = (new StudentUserDto($request))->dataFromRequest();
            $data = (new StudentDto($request))->dataFromRequest();
            $studentParentData = (new StudentParentDto($request))->dataFromRequest();
            $student = $this->studentService->update($student, $data, $studentUserData, $studentParentData);
            DB::commit();
            return returnMessage(true, 'Student Updated Successfully', $student);
        } catch (Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function destroy(Student $student)
    {
        try {
            DB::beginTransaction();
            $this->studentService->delete($student);
            DB::commit();
            return returnMessage(true, 'Student Deleted Successfully', null);
        } catch (Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function getStudentsToGraduate(Request $request)
    {
        $data = $request->all();
        $students = $this->studentService->getStudentsToGraduate($data);
        return returnMessage(true, 'Students Fetched Successfully', StudentResource::collection($students)->response()->getData(true));
    }

    public function graduate(GraduateRequest $request)
    {
        try {
            DB::beginTransaction();
            $this->studentService->graduate($request->validated());
            DB::commit();
            return returnMessage(true, 'Students Graduated Successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }
    public function getStudentsToUpgrade(Request $request, $class_id)
    {
        $data = $request->all();
        $students = $this->studentService->getStudentsToUpgrade($data, $class_id);
        return returnMessage(true, 'Students Fetched Successfully', StudentResource::collection($students)->response()->getData(true));
    }
    public function upgrade(UpgradeRequest $request)
    {
        try {
            DB::beginTransaction();
            $this->studentService->upgrade($request->validated());
            DB::commit();
            return returnMessage(true, 'Students Upgraded Successfully');
        } catch (Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function importStudents(StudentImportRequest $request)
    {
        try {
            $importService = new StudentImportService();
            $importService->importStudents(
                $request->file('file'),
                $request->file('pdf_zip')
            );
            return returnMessage(true, 'Students Imported Successfully', null);
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            return $e->getResponse();
        } catch (Exception $e) {
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }
}
