<?php

namespace Modules\Salary\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Imports\SalariesImport;
use Modules\Salary\DTO\SalaryDto;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Salary\App\Models\Salary;
use Illuminate\Support\Facades\Validator;
use Modules\Salary\Service\SalaryService;
use Modules\Salary\App\resources\SalaryResource;
use Modules\Salary\App\Http\Requests\SalaryRequest;

class SalaryController extends Controller
{
    private $salaryService;

    public function __construct(SalaryService $salaryService)
    {
        $this->middleware('auth:user');
        $this->middleware('role:School Manager|Financial Director|Salaries Employee');
        $this->salaryService = $salaryService;
    }

    public function mySalaries(Request $request)
    {
        $relations = ['employee'];
        $salaries = $this->salaryService->findMySalaries($request->all(), $relations);
        return returnMessage(true, 'Salaries fetched successfully', SalaryResource::collection($salaries)->response()->getData(true));
    }

    public function index(Request $request)
    {
        $relations = ['employee'];
        $salaries = $this->salaryService->findAll($request->all(), $relations);
        return returnMessage(true, 'Salaries fetched successfully', SalaryResource::collection($salaries)->response()->getData(true));
    }

    public function store(SalaryRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = (new SalaryDto($request, true))->dataFromRequest();
            $salary = $this->salaryService->create($data);
            DB::commit();
            return returnMessage(true, 'Salary created successfully', $salary);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }

    public function update(SalaryRequest $request, Salary $salary)
    {
        try {
            DB::beginTransaction();
            $data = (new SalaryDto($request))->dataFromRequest();
            $salary = $this->salaryService->update($salary, $data);
            DB::commit();
            return returnMessage(true, 'Salary updated successfully', $salary);
        } catch (\Exception $e) {
            DB::rollBack();
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
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
            $import = new SalariesImport();
            Excel::import($import, $request->file('file'));
            if (!empty($import->getFailures()))
                return returnValidationMessage(false, trans('validation.rules_failed'), $import->getFailures());
            return returnMessage(true, 'Salaries Imported Successfully', null);

        } catch (\Exception $e) {
            return returnMessage(false, $e->getMessage(), null, 'server_error');
        }
    }
}
