<?php

namespace Modules\Teacher\App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use App\Imports\TeachersImport;
use Modules\User\DTO\TeacherDto;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Teacher\DTO\TeacherProfileDto;
use Modules\Teacher\Service\TeacherService;
use Modules\Teacher\App\Models\TeacherProfile;
use Modules\Teacher\App\resources\TeacherResource;
use Modules\Teacher\App\Http\Requests\TeacherRequest;
use Modules\School\App\Http\Requests\SchoolImportRequest;

class TeacherController extends Controller
{
    private $teacherService;
    public function __construct(TeacherService $teacherService)
    {
        $this->middleware('auth:user');
        $this->middleware('role:Super Admin|School Manager');
        $this->middleware('permission:Index-teacher|Create-teacher|Edit-teacher|Delete-teacher', ['only' => ['index', 'store', 'getTeachersBySubjectId']]);
        $this->middleware('permission:Create-teacher', ['only' => ['store', 'importTeachers']]);
        $this->middleware('permission:Edit-teacher', ['only' => ['update', 'activate']]);
        $this->middleware('permission:Delete-teacher', ['only' => ['destroy']]);
        $this->teacherService = $teacherService;
    }
    public function index(Request $request)
    {
        $data = $request->all();
        $teachers = $this->teacherService->findAll($data);
        return returnMessage(true, 'Teachers Fetched Successfully', TeacherResource::collection($teachers)->response()->getData(true));
    }

    public function store(TeacherRequest $request)
    {
        try {
            DB::beginTransaction();
            $teacherData = (new TeacherDto($request))->dataFromRequest();
            $teacherProfileData = (new TeacherProfileDto($request))->dataFromRequest();
            $teacher = $this->teacherService->create($teacherData, $teacherProfileData);
            DB::commit();
            return returnMessage(true, 'Teacher Created Successfully', null);
        } catch (Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function update(TeacherRequest $request, TeacherProfile $teacher)
    {
        try {
            DB::beginTransaction();
            $teacherData = (new TeacherDto($request))->dataFromRequest();
            $teacherProfileData = (new TeacherProfileDto($request))->dataFromRequest();
            $teacher = $this->teacherService->update($teacher, $teacherData, $teacherProfileData);
            DB::commit();
            return returnMessage(true, 'Teacher Updated Successfully', $teacher);
        } catch (Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function destroy(TeacherProfile $teacher)
    {
        try {
            DB::beginTransaction();
            $this->teacherService->delete($teacher);
            DB::commit();
            return returnMessage(true, 'Teacher Deleted Successfully', null);
        } catch (Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function getTeachersBySubjectId(Request $request, $subjectId)
    {
        return returnMessage(true, 'Teachers fetched successfully', TeacherResource::collection($this->teacherService->getTeachersBySubjectId($subjectId))->response()->getData(true));
    }

    public function importTeachers(SchoolImportRequest $request)
    {
        Excel::import(new TeachersImport, $request->file('file'));
        return returnMessage(true, 'Teachers Imported Successfully', null);
    }
}