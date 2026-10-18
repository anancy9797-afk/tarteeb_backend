<?php

namespace Modules\Grade\App\Http\Controllers\Api;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Grade\DTO\GradeCategoryDto;
use Modules\Grade\App\Models\GradeCategory;
use Modules\Grade\Service\GradeCategoryService;
use Modules\Grade\App\resources\GradeCategoryResource;
use Modules\Grade\App\Http\Requests\GradeCategoryRequest;
use Illuminate\Http\Request;


class GradeCategoryController extends Controller
{
    private $gradeCategoryService;
    public function __construct(GradeCategoryService $gradeCategoryService)
    {
        $this->middleware('auth:user');
        $this->middleware('role:Super Admin|School Manager|Teacher')->except('index');
        $this->middleware('role:Super Admin|School Manager|Financial Director|Teacher')->only('index');

        // $this->middleware('permission:Index-grade-category|Create-grade-category|Edit-grade-category|Delete-grade-category', ['only' => ['index', 'store']]);
        // $this->middleware('permission:Create-grade-category', ['only' => ['store']]);
        // $this->middleware('permission:Edit-grade-category', ['only' => ['update', 'activate']]);
        // $this->middleware('permission:Delete-grade-category', ['only' => ['destroy']]);
        $this->gradeCategoryService = $gradeCategoryService;
    }
    public function index(Request $request)
    {
        $data = $request->all();
        $gradeCategories = $this->gradeCategoryService->findAll($data);
        return returnMessage(true, 'Grades fetched successfully', GradeCategoryResource::collection($gradeCategories)->response()->getData(true));
    }
    public function store(GradeCategoryRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = (new GradeCategoryDto($request))->dataFromRequest();
             $this->gradeCategoryService->create($data);
            DB::commit();
            return returnMessage(true, 'Grade Category Created Successfully', null);
        } catch (Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 500);
        }
    }

    public function update(GradeCategoryRequest $request, GradeCategory $gradeCategory)
    {
        try {
            DB::beginTransaction();
            $data = (new GradeCategoryDto($request))->dataFromRequest();
            $gradeCategory = $this->gradeCategoryService->update($gradeCategory, $data);
            DB::commit();
            return returnMessage(true, 'Grade Category Updated Successfully', $gradeCategory);
        } catch (Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 500);
        }
    }
}
