<?php

namespace Modules\Salary\Service;

use Modules\Common\Helpers\UploadHelper;
use Modules\Salary\App\Models\Salary;

class SalaryService
{
    use UploadHelper;
    function findAll($data = [], $relations = [])
    {
        $salaries = Salary::query()
            ->available()
            ->with($relations)
            ->latest();
        return getCaseCollection($salaries, $data);
    }

    function findMySalaries($data = [], $relations = [])
    {
        $salaries = Salary::query()
            ->with($relations)
            ->where('created_by', auth('user')->id())
            ->latest();
        return getCaseCollection($salaries, $data);
    }

    function findById($id, $relations = [])
    {
        return Salary::with($relations)->findOrFail($id);
    }

    function findBy($key, $value, $relations = [])
    {
        return Salary::where($key, $value)->with($relations)->get();
    }

    function create($data)
    {
        $salary = Salary::create($data);
        return $salary;
    }

    function update($salary, $data)
    {
        $salary->update($data);
        return $salary;
    }

    function totalCost()
    {
        $salaries = Salary::available();
        $sumSalary = $salaries->sum('salary');
        $sumBonus = $salaries->sum('bonus');
        $sumDeduction = $salaries->sum('deduction');
        return [
            'salaries' => $sumSalary,
            'bonuses' => $sumBonus,
            'deductions' => $sumDeduction,
            'total' => $sumSalary + $sumBonus - $sumDeduction
        ];
    }
}
