<?php

namespace Modules\School\App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use App\Imports\SchoolsImport;
use Modules\School\DTO\SchoolDto;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\School\App\Models\School;
use Modules\School\DTO\SchoolGradeDto;
use Modules\User\DTO\SchoolManagerDto;
use Modules\School\Service\SchoolService;
use Modules\School\App\resources\SchoolResource;
use Modules\School\App\Http\Requests\SchoolRequest;
use Modules\School\App\Http\Requests\SchoolImportRequest;


class SchoolController extends Controller
{
    private $schoolService;
    public function __construct(SchoolService $schoolService)
    {
        $this->middleware('auth:user');
        $this->middleware('role:Super Admin');
        $this->middleware('permission:Index-school|Create-school|Edit-school|Delete-school', ['only' => ['index', 'store']]);
        $this->middleware('permission:Create-school', ['only' => ['store']]);
        $this->middleware('permission:Edit-school', ['only' => ['update', 'activate']]);
        $this->middleware('permission:Delete-school', ['only' => ['destroy']]);
        $this->schoolService = $schoolService;
    }
    public function index(Request $request)
    {
        $data = $request->all();
        $schools = $this->schoolService->findAll($data);
        return returnMessage(true, 'Schools Fetched Successfully', SchoolResource::collection($schools)->response()->getData(true));
    }

    public function store(SchoolRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = (new SchoolDto($request))->dataFromRequest();
            $managerData = (new SchoolManagerDto($request))->dataFromRequest();
            // $schoolGradesData = (new SchoolGradeDto($request))->dataFromRequest();
            $school = $this->schoolService->create($data, $managerData);
            DB::commit();
            return returnMessage(true, 'School Created Successfully', $school);
        } catch (Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 500);
        }
    }

    public function update(SchoolRequest $request, School $school)
    {
        try {
            DB::beginTransaction();
            $schoolData = (new SchoolDto($request))->dataFromRequest();
            $managerData = (new SchoolManagerDto($request))->dataFromRequest();
            // $schoolGradesData = (new SchoolGradeDto($request))->dataFromRequest();
            $school = $this->schoolService->update($school, $schoolData, $managerData);
            DB::commit();
            return returnMessage(true, 'School Updated Successfully', $school);
        } catch (Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 500);
        }
    }

    public function destroy(School $school)
    {
        try {
            DB::beginTransaction();
            $this->schoolService->delete($school);
            DB::commit();
            return returnMessage(true, 'School Deleted Successfully', null);
        } catch (Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 500);
        }
    }
    
    public function importSchools(SchoolImportRequest $request)
    {
        Excel::import(new SchoolsImport, $request->file('file'));
        return returnMessage(true, 'Schools Imported Successfully', null);
    }
}