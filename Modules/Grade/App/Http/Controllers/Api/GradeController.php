<?php

namespace Modules\Grade\App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use Modules\Grade\DTO\GradeDto;
use Illuminate\Support\Facades\DB;
use Modules\Grade\App\Models\Grade;
use App\Http\Controllers\Controller;
use Modules\Grade\Service\GradeService;
use Modules\Grade\App\Models\GradeCategory;
use Modules\Grade\App\resources\GradeResource;
use Modules\Grade\App\Http\Requests\GradeRequest;


class GradeController extends Controller
{
    protected $gradeService;
    public function __construct(GradeService $gradeService)
    {
$this->middleware('auth:user')->except(['index']);
        $this->middleware('role:Super Admin|School Manager|Teacher|DataEntry')->except(['index']);
        $this->middleware('role:Super Admin|School Manager|Financial Director|Teacher')->only('getGradesByGradeCategory');

        // $this->middleware('permission:Index-grade|Create-grade|Edit-grade|Delete-grade', ['only' => ['index', 'store', 'getGradesByGradeCategory']]);
        // $this->middleware('permission:Create-grade', ['only' => ['store']]);
        // $this->middleware('permission:Edit-grade', ['only' => ['update', 'activate']]);
        // $this->middleware('permission:Delete-grade', ['only' => ['destroy']]);
        $this->gradeService = $gradeService;
    }
    public function index(Request $request)
    {
        $data = $request->all();
        $grades = $this->gradeService->findAll($data);
        return returnMessage(true, 'Grades fetched successfully', GradeResource::collection($grades)->response()->getData(true));
    }

    public function store(GradeRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = (new GradeDto($request))->dataFromRequest();
            $this->gradeService->create($data);
            DB::commit();
            return returnMessage(true, 'Grade Created Successfully', null);
        } catch (Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 500);
        }
    }

    public function update(GradeRequest $request, Grade $grade)
    {
        try {
            DB::beginTransaction();
            $data = (new GradeDto($request))->dataFromRequest();
            $grade = $this->gradeService->update($grade, $data);
            DB::commit();
            return returnMessage(true, 'Grade Updated Successfully', $grade);
        } catch (Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 500);
        }
    }

    public function getGradesByGradeCategory(Request $request, GradeCategory $grade_category){
        $data = $request->all();
        $grades = $this->gradeService->getGradesByGradeCategory($data, $grade_category->id);
        return returnMessage(true, 'Grades Fetched Successfully', GradeResource::collection($grades)->response()->getData(true));
    }

}
