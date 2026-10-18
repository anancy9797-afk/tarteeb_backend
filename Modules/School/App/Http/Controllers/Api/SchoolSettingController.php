<?php

namespace Modules\School\App\Http\Controllers\Api;

use Exception;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\School\DTO\SchoolSettingDto;
use Modules\School\Service\SchoolService;
use Modules\School\App\Http\Requests\SchoolSettingRequest;



class SchoolSettingController extends Controller
{
    private $schoolService;
    public function __construct(SchoolService $schoolService)
    {
        $this->middleware('auth:user');
        $this->middleware('role:School Manager');
        $this->schoolService = $schoolService;
    }

    public function updateSchoolSettings(SchoolSettingRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = (new SchoolSettingDto($request))->dataFromRequest();
            $this->schoolService->updateSchoolSettings($data);
            DB::commit();
            return returnMessage(true, 'School Settings Updated Successfully', null);
        } catch (Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }
}