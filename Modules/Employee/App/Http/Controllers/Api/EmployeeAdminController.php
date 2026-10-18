<?php

namespace Modules\Employee\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Imports\EmployeesImport;
use Modules\User\App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Modules\User\DTO\EmployeeUserDto;
use Modules\User\Service\UserService;
use Modules\Employee\Service\EmployeeService;
use Modules\Employee\App\resources\EmployeeResource;
use Modules\Employee\App\Http\Requests\EmployeeRequest;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeAdminController extends Controller
{
    private $employeeService;
    public function __construct(EmployeeService $employeeService)
    {
        $this->middleware('auth:user')->except('employeeRoles');
        $this->middleware('role:Super Admin|School Manager|Financial Director|Salaries Employee');
        $this->employeeService = $employeeService;
    }
    public function index(Request $request)
    {
        $data = $request->all();
        $relations = ['roles:id,name', 'school'];
        $employees = $this->employeeService->findAll($data, $relations);
        return returnMessage(true, 'Employees', EmployeeResource::collection($employees)->response()->getData(true));
    }

    // public function store(EmployeeRequest $request)
    // {
    //     try {
    //         DB::beginTransaction();
    //         $data = (new EmployeeUserDto($request))->dataFromRequest();
    //         if ($employee->hasRole('Teacher')) {
    //         \Modules\Teacher\App\Models\TeacherProfile::firstOrCreate([
    //             'user_id' => $employee->id,
    //         ], [
    //             // أضف أي حقول إضافية مطلوبة في جدول teacher_profiles إذا وجدت (مثل school_id)
    //             'school_id' => $employee->school_id ?? $request->school_id,
    //         ]);
    //     }
    //         // $employee = (new UserService())->saveEmployeeUser($data);
    //         DB::commit();
    //         return returnMessage(true, 'Employee Created Successfully', $employee);
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return returnMessage(false, $e->getMessage(), null, 'error');
    //     }
    // }
    public function store(EmployeeRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = (new EmployeeUserDto($request))->dataFromRequest();
            
            // 1. أولاً نقوم بحفظ الموظف وتخزين النتيجة في متغير employee
            $employee = (new UserService())->saveEmployeeUser($data);

            // 2. ثم نتحقق مما إذا كان معلماً لإنشاء الـ TeacherProfile الخاص به
            if ($employee->hasRole('Teacher')) {
                \Modules\Teacher\App\Models\TeacherProfile::firstOrCreate([
                    'user_id' => $employee->id,
                ], [
                    'school_id' => $employee->school_id ?? $request->school_id,
                ]);
            }

            DB::commit();
            return returnMessage(true, 'Employee Created Successfully', $employee);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'error');
        }
    }

    public function update(EmployeeRequest $request, User $user)
    {
        try {
            DB::beginTransaction();
            $data = (new EmployeeUserDto($request))->dataFromRequest();
            $employee = (new UserService())->updateEmployeeUser($data, $user);
            DB::commit();
            return returnMessage(true, 'Employee Updated Successfully', $employee);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'error');
        }
    }

    public function employeeRoles()
    {
        $roles = ['Financial Director', 'Sales Employee', 'Teacher', 'Purchasing Employee', 'Salaries Employee', 'Maintenance Employee',"DataEntry", 'Other'];
        $employeeRoles = Role::select('id', 'name')->whereIn('name', $roles)->orderBy('id')->get();
        return returnMessage(true, 'Employee Roles', $employeeRoles);
    }

    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:2048'],
        ], [
            'file.required' => 'Please upload a file',
            'file.file' => 'The uploaded file is not valid',
            'file.mimes' => 'The file must be an Excel file (xlsx, xls, or csv)',
            'file.max' => 'The file size must not exceed 2MB',
        ]);
        if ($validator->fails())
            return returnValidationMessage(false, trans('validation.rules_failed'), $validator->errors()->messages(), 'unprocessable_entity');
        try {
            $import = new EmployeesImport();
            Excel::import($import, $request->file('file'));
            if (!empty($import->getFailures()))
                return returnValidationMessage(false, trans('validation.rules_failed'), $import->getFailures());
            return returnMessage(true, 'Employees Imported Successfully', null);
        } catch (\Exception $e) {
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }
}
