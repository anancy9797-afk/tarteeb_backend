<?php

namespace Modules\Subject\App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Grade\App\Models\Grade;
use Modules\Subject\DTO\SubjectDto;
use App\Http\Controllers\Controller;
use Modules\Subject\App\Models\Subject;
use Modules\Subject\Service\SubjectService;
use Modules\Subject\App\resources\SubjectResource;
use Modules\Subject\App\Http\Requests\SubjectRequest;


class SubjectController extends Controller
{
    protected $subjectService;
    public function __construct(SubjectService $subjectService)
    {
        $this->middleware('auth:user');
        $this->middleware('role:Super Admin|School Manager');
        $this->middleware('permission:Index-subject|Create-subject|Edit-subject|Delete-subject', ['only' => ['index', 'store']]);
        $this->middleware('permission:Create-subject', ['only' => ['store']]);
        $this->middleware('permission:Edit-subject', ['only' => ['update', 'activate']]);
        $this->middleware('permission:Delete-subject', ['only' => ['destroy']]);
        $this->subjectService = $subjectService;
    }

    public function index(Request $request)
    {
        $data = $request->all();
        $subjects = $this->subjectService->findAll($data);
        return returnMessage(true, 'Subjects fetched successfully', SubjectResource::collection($subjects)->response()->getData(true));
    }

    public function store(SubjectRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = (new SubjectDto($request))->dataFromRequest();
            $subject = $this->subjectService->create($data);
            DB::commit();
            return returnMessage(true, 'Subject Created Successfully', $subject);
        } catch (Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 500);
        }
    }

    public function update(SubjectRequest $request, Subject $subject)
    {
        try {
            DB::beginTransaction();
            $data = (new SubjectDto($request))->dataFromRequest();
            $subject = $this->subjectService->update($subject, $data);
            DB::commit();
            return returnMessage(true, 'Subject Updated Successfully', $subject);
        } catch (Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 500);
        }
    }

       public function subjectsByGradeId(Grade $grade)
    {
        $subjects = $this->subjectService->getSubjectsByGradeId($grade);
        return returnMessage(true, 'Subjects fetched successfully', SubjectResource::collection($subjects)->response()->getData(true));
    }

}
