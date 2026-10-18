<?php

namespace Modules\Salary\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Salary\Service\SalaryService;
use Modules\Salary\App\resources\SalaryResource;

class SalaryAdminController extends Controller
{
    private $salaryService;

    public function __construct(SalaryService $salaryService)
    {
        $this->middleware('auth:user');
        $this->middleware('role:Super Admin|School Manager|Financial Director');
        $this->salaryService = $salaryService;
    }

    public function index(Request $request)
    {
        $relations = ['employee.roles', 'createdBy.roles', 'school'];
        $salaries = $this->salaryService->findAll($request->all(), $relations);
        $totalCost = $this->salaryService->totalCost();
        return returnMessage(true, 'Salaries fetched successfully', [
            'data' => SalaryResource::collection($salaries)->response()->getData(true),
            'salaries' => $totalCost['salaries'],
            'bonuses' => $totalCost['bonuses'],
            'deductions' => $totalCost['deductions'],
            'total_cost' => $totalCost['total'],
        ]);
    }
}
