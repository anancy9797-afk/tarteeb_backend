<?php

namespace Modules\Session\App\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Session\DTO\AttendanceDto;
use Modules\Session\Service\AttendanceService;
use Modules\Session\App\Http\Requests\AttendanceRequest;
use Modules\Session\App\resources\StudentAttendanceResource;


class AttendanceController extends Controller
{
   private $attendanceService;
   public function __construct(AttendanceService $attendanceService){
      $this->middleware('auth:user');
      $this->middleware('role:Teacher');
      $this->middleware('permission:Index-attendance|Create-attendance|Edit-attendance|Delete-attendance', ['only' => ['index', 'store']]);
      $this->middleware('permission:Create-attendance', ['only' => ['store']]);
      $this->middleware('permission:Edit-attendance', ['only' => ['update', 'activate']]);
      $this->middleware('permission:Delete-attendance', ['only' => ['destroy']]);
      $this->attendanceService = $attendanceService;
   }
   public function index(AttendanceRequest $request)
{
    $data = $request->all();
    $students = $this->attendanceService->getStudentsByGradeAndTerm($data);

    if ($students->isEmpty()) {
        return returnMessage(false, 'No Students Found for this Grade', null, 'not_found');
    }

    return returnMessage(true, 'Students Fetched Successfully', new StudentAttendanceResource($students));
}

//    public function index(AttendanceRequest $request){
//     $data = $request->all();
//     $session = $this->attendanceService->getStudentsByGradeAndTerm($data);
//     if(!$session){
//         return returnMessage(false, 'Session Not Found', null, 'not_found');
//     }
//     return returnMessage(true, 'Students Fetched Successfully', new StudentAttendanceResource($session));
//  }

   public function store(AttendanceRequest $request){
      try{
         DB::beginTransaction();
         $data = (new AttendanceDto($request))->dataFromRequest();
         $this->attendanceService->create($data);
         DB::commit();
         return returnMessage(true, 'Attendance Created Successfully', null);
      }catch(Exception $e){
         DB::rollBack();
         return returnMessage(false, $e->getMessage(), null, 500);
      }
   }
}
